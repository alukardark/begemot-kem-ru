<?php

namespace Artamonov\Api\Controllers;


class Infoscreen extends BaseAPIController
{
    public function GetList()
    {
        $arSelect = [
            'NAME', 'PREVIEW_TEXT', 'PROPERTY_LINK_TEXT', 'PROPERTY_LINK', 'DETAIL_PICTURE', 'PREVIEW_PICTURE'
        ];
        $rawResult = \APIInfoscreenModel::select($arSelect)->filter(['ACTIVE' => 'Y'])
            ->cache(30)
            ->sort('sort')->getList()->toArray();

        $result = [];
        foreach ($rawResult as $item) {
            $result[] = [
                'TITLE' => $item['NAME'],
                'TEXT' => $item['PREVIEW_TEXT'],
                'LINK_TEXT' => $item['PROPERTY_LINK_TEXT_VALUE'],
                'LINK' => $item['PROPERTY_LINK_VALUE'],
                'IMAGE' => \CFile::GetPath($item['DETAIL_PICTURE']),
                'ICON' => \CFile::GetPath($item['PREVIEW_PICTURE']),
            ];
        }

        $this->Execute($result);
    }
}