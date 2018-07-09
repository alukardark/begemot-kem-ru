<?php

namespace Artamonov\Api\Controllers;


class Shops extends BaseAPIController
{
    public function GetCities()
    {
        $result = getCitiesList();

        $this->Execute($result);
    }

    public function GetList()
    {
        $arSelect = [
            'NAME',
            'PROPERTY_CITY',
            'PROPERTY_DISTRICT',
            'PROPERTY_ADDRESS',
            'PROPERTY_PHONE',
            'PROPERTY_PHONE_ADD',
            'PROPERTY_EMAIL',
            'PROPERTY_WORKTIME',
            'PROPERTY_FEATURES',
            'PROPERTY_COORDINATES',
            'PROPERTY_GALLERY'
        ];
        $rawResult = \ShopsModel::select($arSelect)->filter(['ACTIVE' => 'Y'])
            ->cache(30)
            ->sort('sort')->getList()->toArray();

        $result = [];
        foreach ($rawResult as $item) {
            $result[] = [
                'TITLE' => $item['NAME'],
                'CITY' => $item['PROPERTY_CITY_VALUE'],
                'DISTRICT' => $item['PROPERTY_DISTRICT_VALUE'],
                'ADDRESS' => $item['PROPERTY_ADDRESS_VALUE'],
                'PHONE' => $item['PROPERTY_PHONE_VALUE'],
                'PHONE_ADD' => $item['PROPERTY_PHONE_ADD_VALUE'],
                'EMAIL' => $item['PROPERTY_EMAIL_VALUE'],
                'WORKTIME' => $item['PROPERTY_WORKTIME_VALUE'],
                'FEATURES' => array_values($item['PROPERTY_FEATURES_VALUE']),
                'COORDINATES' => $this->GetCoordinatesArray($item['PROPERTY_COORDINATES_VALUE']),
                'GALLERY' => $this->GetGalleryArray($item['PROPERTY_GALLERY_VALUE'])
            ];
        }

        $this->Execute($result);
    }

    protected function GetCoordinatesArray($item)
    {
        return array_map('trim', explode(',', $item));
    }

    protected function GetGalleryArray($item)
    {
        $gallery = [];
        foreach ($item as $image) {
            $gallery[] = \CFile::GetPath($image);
        }
        return $gallery;
    }
}