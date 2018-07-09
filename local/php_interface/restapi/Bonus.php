<?php

namespace Artamonov\Api\Controllers;


class Bonus extends BaseAPIController
{
    public function GetList()
    {
        $arSelect = [
            'NAME', 'PROPERTY_PRICE', 'PREVIEW_PICTURE'
        ];
        $rawResult = \APIBonusModel::select($arSelect)->filter(['ACTIVE' => 'Y'])
            ->cache(30)
            ->sort('sort')->getList()->toArray();

        $result = [];
        foreach ($rawResult as $item) {
            $result[] = [
                'TITLE' => $item['NAME'],
                'PRICE' => $item['PROPERTY_PRICE_VALUE'],
                'IMAGE' => \CFile::GetPath($item['PREVIEW_PICTURE'])
            ];
        }

        $this->Execute($result);
    }
}