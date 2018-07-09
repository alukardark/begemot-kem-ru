<?php

use Arrilot\BitrixModels\Models\ElementModel;


class GalleriesModel extends ElementModel
{
    /**
     * Corresponding iblock id.
     *
     * @return int
     */
    const IBLOCK_ID = GALLERIES_IB;

    public static function GetIBElement($code)
    {
        $arResult = self::select('PROPERTY_PHOTO')->filter(['ACTIVE' => 'Y', 'CODE' => $code])
            ->cache(30)
            ->getList()->toArray();
        return array_shift($arResult);
    }
}