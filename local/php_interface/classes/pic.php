<?php
class CPic
{
    const NO_IMAGE_SRC = "/images/not_found/image.png";

    private static $arItem;
    private static $iItemId;
    private static $iSectionId;
    private static $useSectionPicture;

    private static function GetRelativePath($sPath)
    {
        return substr_count($sPath, $_SERVER["DOCUMENT_ROOT"]) ? str_replace($_SERVER["DOCUMENT_ROOT"], "", $sPath) : $sPath;
    }

    private static function setItemId()
    {
        self::$iItemId = self::getItemId();
    }

    private static function getItemId($data = null)
    {
        if (empty($data)) $data = self::$arItem;

        if (!empty($data['PRODUCT_ID'])) return $data['PRODUCT_ID'];
        elseif (is_numeric($data) && intval($data) > 0) return $data;
        else return $data['ID'];
    }

    private static function setSectionId()
    {
        $iSectionId = null;

        if (!empty(self::$arItem['IBLOCK_SECTION_ID']))
        {
            $iSectionId = self::$arItem['IBLOCK_SECTION_ID'];
        }
        elseif (self::$useSectionPicture)
        {
            $obCache   = \Bitrix\Main\Data\Cache::createInstance();
            $lifeTime  = strtotime("30day", 0);
            $cachePath = "/ccache_common/setSectionId/";
            $cacheID   = "setSectionId" . self::$iItemId;

            if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
            {
                $vars = $obCache->GetVars();
                if (isset($vars["iSectionId"]))
                {
                    $iSectionId = $vars["iSectionId"];
                    $lifeTime   = 0;
                }
            }

            if ($lifeTime > 0)
            {
                $obEelement = \CIBlockElement::GetByID(self::$iItemId);
                if ($arFetch    = $obEelement->Fetch())
                {
                    if (!empty($arFetch['IBLOCK_SECTION_ID']))
                    {
                        $iSectionId = $arFetch['IBLOCK_SECTION_ID'];
                    }
                }

                //кешируем
                $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
                $obCache->EndDataCache(array(
                    "iSectionId" => $iSectionId,
                ));
            }
        }

        self::$iSectionId = $iSectionId;
    }

    private static function getSectionPicture()
    {
        $sectionPicture = false;

        if (empty(self::$iSectionId))
        {
            return $sectionPicture;
        }

        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("30day", 0);
        $cachePath = "/ccache_common/getSectionPicture/";
        $cacheID   = "getSectionPicture" . self::$iSectionId;

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["sectionPicture"]))
            {
                $sectionPicture = $vars["sectionPicture"];
                $lifeTime       = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $obEelement = \CIBlockSection::GetByID(self::$iSectionId);
            if ($arFetch    = $obEelement->Fetch())
            {
                $sectionPicture = $arFetch['PICTURE'];
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "sectionPicture" => $sectionPicture,
            ));
        }

        return $sectionPicture;
    }

    private static function getPreviewPictureId()
    {
        $picture = false;

        if (isset(self::$arItem['PREVIEW_PICTURE']) && !empty(self::$arItem['PREVIEW_PICTURE']))
        {
            $picture = self::$arItem['PREVIEW_PICTURE'];
        }
        elseif (isset(self::$arItem['PROPERTIES']['YOUTUBE_PICTURE']['VALUE']) && !empty(self::$arItem['PROPERTIES']['YOUTUBE_PICTURE']['VALUE']))
        {
            $picture = self::$arItem['PROPERTIES']['YOUTUBE_PICTURE']['VALUE'];
        }
        elseif (self::$useSectionPicture)
        {
            $picture = self::getSectionPicture();
        }


        if (!empty($picture['ID']))
        {
            $picture = $picture['ID'];
        }
        return $picture;
    }

    private static function getDetailPictureId()
    {
        $picture = false;

        if (isset(self::$arItem['DETAIL_PICTURE']) && !empty(self::$arItem['DETAIL_PICTURE']))
        {
            $picture = self::$arItem['DETAIL_PICTURE'];
        }
        elseif (isset(self::$arItem['PREVIEW_PICTURE']) && !empty(self::$arItem['PREVIEW_PICTURE']))
        {
            $picture = self::$arItem['PREVIEW_PICTURE'];
        }
        elseif (isset(self::$arItem['PROPERTIES']['YOUTUBE_PICTURE']['VALUE']) && !empty(self::$arItem['PROPERTIES']['YOUTUBE_PICTURE']['VALUE']))
        {
            $picture = self::$arItem['PROPERTIES']['YOUTUBE_PICTURE']['VALUE'];
        }
        elseif (self::$useSectionPicture)
        {
            $picture = self::getSectionPicture();
        }

        if (!empty($picture['ID']))
        {
            $picture = $picture['ID'];
        }
        return $picture;
    }

    public static function getPreviewSrc($arItem, $w = 100, $h = 100, $mode = BX_RESIZE_IMAGE_PROPORTIONAL_ALT, $useSectionPicture = false, $iQuality = 85)
    {
        self::$arItem            = $arItem;
        self::$useSectionPicture = $useSectionPicture;
        self::setItemId();
        self::setSectionId();

        return self::getResized(self::getPreviewPictureId(), $w, $h, $mode, self::NO_IMAGE_SRC, $iQuality);
    }

    public static function getDetailSrc($arItem, $w = 100, $h = 100, $mode = BX_RESIZE_IMAGE_PROPORTIONAL_ALT, $useSectionPicture = false, $iQuality = 85)
    {
        self::$arItem            = $arItem;
        self::$useSectionPicture = $useSectionPicture;
        self::setItemId();
        self::setSectionId();

        return self::getResized(self::getDetailPictureId(), $w, $h, $mode, self::NO_IMAGE_SRC, $iQuality);
    }

    public static function getResized($arPicture, $width = 100, $heght = 100, $sMode = BX_RESIZE_IMAGE_PROPORTIONAL_ALT, $sPath = self::NO_IMAGE_SRC, $iQuality = 85)
    {
        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("30day", 0);
        $cachePath = "/ccache_common/getResized/";
        $cacheID   = "getResized" . $width . $heght . $sMode . $sPath . $iQuality . serialize($arPicture);

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["file"]))
            {
                $file     = $vars["file"];
                $lifeTime = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $file = array();
            $sSrc = \CFile::GetPath(self::getItemId($arPicture));

            if (empty($arPicture) || !$arPicture || !file_exists($_SERVER["DOCUMENT_ROOT"] . $sSrc) || (!is_numeric($arPicture) && !isset($arPicture['ID'])))
            {
                if ($sPath !== false)
                {
                    $arPathParts = pathinfo($sPath);
                    $sFileName   = md5($arPathParts['filename'] . $width . $heght . $arPathParts['extension']);

                    $destinationFile = $_SERVER["DOCUMENT_ROOT"] . '/upload/resize_cache/no_photo/' . $sFileName . '.' . $arPathParts['extension'];

                    if (!file_exists($destinationFile))
                    {
                        \CFile::ResizeImageFile($_SERVER["DOCUMENT_ROOT"] . $sPath, $destinationFile, array('width' => $width, 'height' => $heght), BX_RESIZE_IMAGE_PROPORTIONAL, true, array(), true, $iQuality);
                    }
                    $file['src'] = self::GetRelativePath($destinationFile);
                }
            }
            else
            {
                $file = \CFile::ResizeImageGet($arPicture, array('width' => $width, 'height' => $heght), $sMode, true, array(), true, $iQuality);
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "file" => $file,
            ));
        }


        if (!empty($file))
        {
            $noCacheHash = filemtime($_SERVER["DOCUMENT_ROOT"] . $file['src']);
            return $file['src'] . "?" . $noCacheHash;
        }
        else
        {
            return false;
        }
    }

    public static function makeFile($src, $path = "saved_files")
    {
        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("360day", 0);
        $cachePath = "/ccache_common/makeFile/";
        $cacheID   = "makeFile" . $src . $path;

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["iFileId"]))
            {
                $iFileId  = $vars["iFileId"];
                $lifeTime = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $arFile  = \CFile::MakeFileArray($src);
            $iFileId = \CFile::SaveFile($arFile, $path);

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "iFileId" => $iFileId,
            ));
        }

        return $iFileId;
    }

}
