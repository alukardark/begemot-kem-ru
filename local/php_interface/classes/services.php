<?php

class CServicesExt
{

    const SERVICES_UPLOAD_PATH = 'services_files';

    public static function getCategory($CODE)
    {
        $arFilter = array("IBLOCK_ID" => USLUGI_IB, "ACTIVE" => "Y", "CODE" => $CODE);

        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("1day", 0);
        $cachePath = "/ccache_services/getCategory/";
        $cacheID   = "getCategory" . serialize($arFilter);

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arResult"]))
            {
                $arResult = $vars["arResult"];
                $lifeTime = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $arResult = false;

            $obList  = \CIBlockSection::GetList(array(), $arFilter, true);
            if ($arFetch = $obList->Fetch())
            {
                $arResult = $arFetch;
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arResult" => $arResult,
            ));
        }


        return $arResult;
    }

    public static function getItems($arStores = array())
    {
        $arSort   = array("SORT" => "ASC");
        $arFilter = array("IBLOCK_ID" => USLUGI_IB, "ACTIVE" => "Y", "PROPERTY_STORES" => $arStores);
        $arSelect = array("ID", "IBLOCK_ID", "NAME", "IBLOCK_SECTION_ID");

        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("1day", 0);
        $cachePath = "/ccache_services/getItems/";
        $cacheID   = "getItems" . serialize($arSort + $arFilter + $arSelect);

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arResult"]))
            {
                $arResult = $vars["arResult"];
                $lifeTime = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $arItems    = array();
            $arSections = array();

            $obList  = \CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
            while ($arFetch = $obList->Fetch())
            {
                $arItems[] = $arFetch['ID'];

                if (!in_array($arFetch['IBLOCK_SECTION_ID'], $arSections))
                {
                    $arSections[] = $arFetch['IBLOCK_SECTION_ID'];
                }
            }

            $arResult = array(
                'SECTIONS' => $arSections,
                'ELEMENTS' => $arItems,
            );

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arResult" => $arResult,
            ));
        }

        return $arResult;
    }

    public static function getServicesSectionsTemp()
    {
        $arServices = array();

        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("1day", 0);
        $cachePath = "/ccache_services/getServicesSections/";
        $cacheID   = "getServicesSectionsTemp";

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arServices"]))
            {
                $arServices = $vars["arServices"];
                $lifeTime   = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $arFilter = array("IBLOCK_ID" => 14, "ACTIVE" => "Y");
            $arSelect = array("ID", "NAME", "PROPERTY_URL");
            $obList   = \CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);
            while ($arItem   = $obList->Fetch())
            {
                $arItem["SECTION_PAGE_URL"] = $arItem["PROPERTY_URL_VALUE"];
                $arItem["NEW_TAB"]          = true;
                $arServices[]               = $arItem;
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arServices" => $arServices,
            ));
        }

        return $arServices;
    }

    public static function getServicesSections()
    {
        $arServices = array();

        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("1day", 0);
        $cachePath = "/ccache_services/getServicesSections/";
        $cacheID   = "getServicesSections" . USLUGI_IB;

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arServices"]))
            {
                $arServices = $vars["arServices"];
                $lifeTime   = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $arFilter = array("IBLOCK_ID" => USLUGI_IB, "ACTIVE" => "Y", "DEPTH_LEVEL" => 1);
            $arSelect = array('ID', 'IBLOCK_ID', 'NAME', 'SECTION_PAGE_URL');
            $obList   = \CIBlockSection::GetList(Array("SORT" => "ASC"), $arFilter, false, $arSelect);
            while ($arItem   = $obList->GetNext())
            {
                $arServices[] = $arItem;
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arServices" => $arServices,
            ));
        }

        return $arServices;
    }

    /**
     * Обновляет услуги с помощью запроса в 1С
     */
    public static function updateServices()
    {
        $arServices = \CURL::getReplay("Services", array(), true, false, true);

        //echo '<pre>';
        // var_dump($arServices);
        //echo '</pre>';
        //die;

        if (empty($arServices['Services']))
        {
            return;
        }

        //декактивируем все элементы и разделы
        deactivateIBlock(USLUGI_IB);

        $arTranslitParams = array(
            "replace_space" => "-",
            "replace_other" => "-",
        );

        $IBlockSection = new \CIBlockSection;
        $IBlockElement = new \CIBlockElement;

        foreach ($arServices['Services'] as $arService)
        {
            $XML_ID        = (string) $arService["XML_ID"];
            $NAME          = (string) $arService["NAME"];
            $DESCRIPTION   = (string) $arService["DESCRIPTION"];
            $DEPTH_LEVEL   = (int) ($arService["DEPTH_LEVEL"] ?: 1);
            $IS_PARENT     = (bool) ($arService["IS_PARENT"] || $DEPTH_LEVEL === 1);
            $PARENT_XML_ID = (string) $arService["PARENT_XML_ID"];
            $CHILDREN      = (array) $arService["CHILDREN"];

            $SORT = (int) $arService["SORTING"] ?: 500;

            //иконка
            $ICON_B64 = (string) $arService["PICTURE_ICON"];
            $ICON_ID  = false;

            //картинка
            $PICTURE_B64 = (string) $arService["PICTURE"];
            $PICTURE_ID  = false;

            $SECTION_CODE = \Cutil::translit($NAME, "ru", $arTranslitParams);

            $SECTION_ID        = false; //ID раздела
            $PARENT_SECTION_ID = false; //ID родительского раздела


            if (!empty($PARENT_XML_ID))
            {
                //првоеряем сществует ли раздел с таким XML_ID
                $arFilter = array("IBLOCK_ID" => USLUGI_IB, "XML_ID" => $PARENT_XML_ID);
                $obList   = \CIBlockSection::GetList(array(), $arFilter, false, array('ID', 'IBLOCK_ID'));
                if ($arFetch  = $obList->Fetch())
                {
                    $PARENT_SECTION_ID = $arFetch["ID"];
                }
            }

            if ($IS_PARENT)
            {
                if (empty($CHILDREN)) continue;

                //првоеряем сществует ли раздел с таким XML_ID
                $arFilter = array("IBLOCK_ID" => USLUGI_IB, "XML_ID" => $XML_ID);
                $obList   = \CIBlockSection::GetList(array(), $arFilter, false, array('ID', 'IBLOCK_ID', 'PICTURE', 'DETAIL_PICTURE',));
                if ($arFetch  = $obList->Fetch())
                {
                    $SECTION_ID     = $arFetch["ID"];
                    $ICON_OLD_ID    = $arFetch["PICTURE"]; //иконка
                    $PICTURE_OLD_ID = $arFetch["DETAIL_PICTURE"]; //картинка
                }

                if (!empty($ICON_B64))
                {
                    $ICON_ID = base64ToFile($ICON_B64, self::SERVICES_UPLOAD_PATH, "icon_" . $SECTION_CODE . ".png", $ICON_OLD_ID);
                }

                if (!empty($PICTURE_B64))
                {
                    $PICTURE_ID = base64ToFile($PICTURE_B64, self::SERVICES_UPLOAD_PATH, "picture_" . $SECTION_CODE . ".png", $PICTURE_OLD_ID);
                }

                $arFields = Array(
                    "IBLOCK_ID"         => USLUGI_IB,
                    "IBLOCK_SECTION_ID" => $PARENT_SECTION_ID,
                    "ACTIVE"            => "Y",
                    "NAME"              => $NAME,
                    "DESCRIPTION"       => $DESCRIPTION,
                    "SORT"              => $SORT,
                    "DESCRIPTION_TYPE"  => "html",
                    "PICTURE"           => $ICON_ID ? \CFile::MakeFileArray($ICON_ID) : array('del' => 'Y'),
                    "DETAIL_PICTURE"    => $PICTURE_ID ? \CFile::MakeFileArray($PICTURE_ID) : array('del' => 'Y'),
                );

                if (!empty($SECTION_ID))
                {
                    $IBlockSection->Update($SECTION_ID, $arFields);
                }
                else
                {
                    $arFields["XML_ID"] = $XML_ID;
                    $arFields["CODE"]   = $SECTION_CODE;

                    $SECTION_ID = $IBlockSection->Add($arFields);
                }

                foreach ($CHILDREN as $arItem)
                {
                    $ELEMENT_ID = false;

                    $XML_ID         = (string) $arItem["XML_ID"];
                    $NAME           = (string) $arItem["NAME"];
                    $DESCRIPTION    = (string) $arItem["DESCRIPTION"];
                    $PARENT_XML_ID  = (string) $arItem["PARENT_XML_ID"];
                    $STORES         = (array) $arItem["STORES"];
                    $PRICE          = (int) $arItem["PRICE"];
                    $IS_PRICE_EXACT = $arItem["IS_PRICE_EXACT"] ? "Y" : "N";
                    $SORT           = (int) $arItem["SORTING"] ?: 500;

                    $ELEMENT_CODE = "usluga_" . \Cutil::translit($NAME, "ru", $arTranslitParams);

                    //првоеряем сществует ли раздел с таким XML_ID
                    $arFilter = array("IBLOCK_ID" => USLUGI_IB, "XML_ID" => $PARENT_XML_ID);
                    $obList   = \CIBlockSection::GetList(array(), $arFilter, false, array('ID', 'IBLOCK_ID'));
                    if ($arFetch  = $obList->Fetch())
                    {
                        $PARENT_SECTION_ID = $arFetch["ID"];
                    }

                    //printra($STORES);
                    $PROPERTY_VALUES                   = array();
                    $PROPERTY_VALUES['STORES']         = $STORES;
                    $PROPERTY_VALUES['PRICE']          = $PRICE;
                    $PROPERTY_VALUES["IS_PRICE_EXACT"] = $IS_PRICE_EXACT;

                    $arFields = Array(
                        "IBLOCK_ID"         => USLUGI_IB,
                        "IBLOCK_SECTION_ID" => $PARENT_SECTION_ID,
                        "PROPERTY_VALUES"   => $PROPERTY_VALUES,
                        "NAME"              => $NAME,
                        "DETAIL_TEXT"       => $DESCRIPTION,
                        "ACTIVE"            => "Y",
                        "SORT"              => $SORT,
                    );

                    //првоеряем сществует ли элемент с таким XML_ID
                    $arFilter = array("IBLOCK_ID" => USLUGI_IB, "XML_ID" => $XML_ID);
                    $obList   = \CIBlockElement::GetList(array(), $arFilter, false, array('ID', 'IBLOCK_ID'));
                    if ($arFetch  = $obList->Fetch())
                    {
                        $ELEMENT_ID = $arFetch["ID"];
                    }

                    if (!empty($ELEMENT_ID))
                    {
                        $IBlockElement->Update($ELEMENT_ID, $arFields);
                    }
                    else
                    {
                        $arFields["XML_ID"] = $XML_ID;
                        $arFields["CODE"]   = $ELEMENT_CODE;

                        $ELEMENT_ID = $IBlockElement->Add($arFields);
                    }
                }
            }
        }

        $dir = $_SERVER["DOCUMENT_ROOT"] . '/upload/' . self::SERVICES_UPLOAD_PATH . "/";
        clearPath($dir);

        clearCache("/news.list/", true);
        clearCache("/news.detail/", true);
        clearCache("/catalog.section.list/", true);
        clearCache("/ccache_services/");
        return $arServices;
    }

}
