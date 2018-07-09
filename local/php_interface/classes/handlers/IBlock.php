<?php

namespace Axi\Handlers;


class IBlock
{
    const WITH_GALLERY_IBLOCK_ARRAY = [SHOPS_IB, FOOD_IB, BAKERY_IB, NEWS_IB, SALES_IB, TENANTS_IB, BONUSES_IB, GALLERIES_IB];
    const FOS_RESULT_IBLOCK_ARRAY = [
        API_STAR_FOS_IB, FOS_FEEDBACK_IB, FOS_QUESTION_IB, FOS_PROMO_IB, FOS_REVIEW_IB, FOS_ACTION_MEMBER_IB,
        FOS_RESUME_IB, FOS_TENANTS_IB, FOS_LESSORS_IB
    ];
    const CLEARCACHE_IBLOCK_ARRAY = [API_INFOSCREEN_IB, API_BONUS_IB, API_SALE_IB, API_STAR_FOS_IB, SHOPS_IB, GALLERIES_IB];
    const API_CACHE_DIR = '/bitrix-models/';

    public static function OnBeforeIBlockElementAdd(&$arParams)
    {
        /* Запретим загрузку больших изображений в галереи */
        if (!self::ValidateUploadImageSize($arParams)) return false;
    }

    public static function OnBeforeIBlockElementUpdate(&$arParams)
    {
        /* Запретим изменение результатов ФОС */
        if (in_array($arParams['IBLOCK_ID'], self::FOS_RESULT_IBLOCK_ARRAY)) {
            global $APPLICATION;
            $APPLICATION->ThrowException('Нельзя изменять результат ФОС');

            return false;
        }

        /* Запретим загрузку больших изображений в галереи */
        if (!self::ValidateUploadImageSize($arParams)) return false;

        /* Обновление кэша API */
        if (in_array($arParams['IBLOCK_ID'], self::CLEARCACHE_IBLOCK_ARRAY)) {
            BXClearCache(true, self::API_CACHE_DIR);
        }
    }

    public static function OnBeforeIBlockElementDelete($id)
    {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $element = \CIBlockElement::GetByID($id)->Fetch();

        global $APPLICATION;

        /* Запретим удаление результатов ФОС */
        if (in_array($element['IBLOCK_ID'], self::FOS_RESULT_IBLOCK_ARRAY) && $request->getQuery('D') != 'Y') {
            $APPLICATION->ThrowException('Нельзя удалять результат ФОС');
            return false;
        }

        /* Запретим удаление ИБ галерей */
        if ($element['IBLOCK_CODE'] == 'galleries') {
            $APPLICATION->ThrowException('Нельзя удалять инфоблоки типа ' . $element['IBLOCK_NAME']);
            return false;
        }
    }

    protected static function ValidateUploadImageSize($arParams)
    {
        if (!in_array($arParams['IBLOCK_ID'], self::WITH_GALLERY_IBLOCK_ARRAY))
            return true;

        $errors = [];
        foreach ($arParams['PROPERTY_VALUES'] as $property) {
            foreach ($property as $item) {
                if ($item['VALUE']['size'] >= UPLOAD_MAX_FILE_SIZE)
                    $errors[] = $item['VALUE']['name'];
            }
        }

        if (!empty($errors)) {
            $errorsString = implode(', ', $errors);

            global $APPLICATION;
            $APPLICATION->ThrowException("Нельзя загружать изображения более 1Мб ({$errorsString})");

            return false;
        } else {
            return true;
        }
    }
}