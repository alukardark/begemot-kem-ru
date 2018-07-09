<?php

class CYouTubeExt
{

    public static function GetYoutubeUID($sText)
    {
        $sUID = false;
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
                $sText, $arMatches))
        {
            return $arMatches[1];
        }
        return false;
    }

    public static function GetVideoInfo($sUID)
    {
        //$sVideoInfo = file_get_contents("http://youtube.com/get_video_info?video_id=" . $sUID);
        //parse_str($sVideoInfo, $arVideoInfo);
        $obVideoInfo = simplexml_load_file('http://www.youtube.com/oembed?url=http%3A//www.youtube.com/watch?v=' . $sUID . '&format=xml');

        if (!is_object($obVideoInfo))
        {
            dmp("false");
            return false;
        }

        $arResult['UID'] = $sUID;
        $arResult['title'] = (string) $obVideoInfo->title;
        $arResult['width'] = (string) $obVideoInfo->width;
        $arResult['height'] = (string) $obVideoInfo->height;
        $arResult['thumb'] = (string) $obVideoInfo->thumbnail_url;
        $arResult['thumb_width'] = (string) $obVideoInfo->thumbnail_width;
        $arResult['thumb_height'] = (string) $obVideoInfo->thumbnail_height;
        $arResult['html'] = (string) $obVideoInfo->html;

        return $arResult;
    }

    public static function YoutubeImageUpdate($arFields)
    {
        $obList = CIBlockElement::GetList(Array(), Array("ID" => $arFields["ID"]), false, false,
                Array("ID", "IBLOCK_ID", "PROPERTY_*"));
        while ($obElement = $obList->GetNextElement())
        {
            $arFields = $obElement->GetFields();
            $arProps = $obElement->GetProperties();

            if (empty($arProps['VIDEO']['VALUE']))
            {
                return true;
            }

            $sUID = self::GetYoutubeUID($arProps['VIDEO']['VALUE']);
            $arVideoInfo = self::GetVideoInfo($sUID);
            $arUpdateProps = Array(
                "YOUTUBE" => serialize($arVideoInfo),
                "YOUTUBE_PICTURE" => CFile::MakeFileArray($arVideoInfo['thumb']),
                );
            CIBlockElement::SetPropertyValuesEx($arFields["ID"], $arFields["IBLOCK_ID"], $arUpdateProps);
            break;
        }
        
        return true;
    }
}