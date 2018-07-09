<?php

namespace Artamonov\Api\Controllers;


class Sale extends BaseAPIController
{
    public function GetList()
    {
        $arSelect = [
            'NAME', 'PROPERTY_PRICE', 'PROPERTY_OLD_PRICE', 'PROPERTY_DISCOUNT', 'PROPERTY_DATE_RANGE', 'PREVIEW_PICTURE'
        ];
        $rawResult = \APISaleModel::select($arSelect)->filter(['ACTIVE' => 'Y'])
            ->cache(30)
            ->sort('sort')->getList()->toArray();

        $result = [];
        foreach ($rawResult as $item) {
            $result[] = [
                'TITLE' => $item['NAME'],
                'PRICE' => $item['PROPERTY_PRICE_VALUE'],
                'OLD_PRICE' => $item['PROPERTY_OLD_PRICE_VALUE'],
                'DISCOUNT' => $item['PROPERTY_DISCOUNT_VALUE'],
                'DATE_RANGE' => $item['PROPERTY_DATE_RANGE_VALUE'],
                'IMAGE' => \CFile::GetPath($item['PREVIEW_PICTURE'])
            ];
        }

        $this->Execute($result);
    }
}