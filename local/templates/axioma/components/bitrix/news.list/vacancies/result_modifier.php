<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 */

/* Получим выбраный город */
$arResult['CURRENT_CITY'] = $_COOKIE['VACANCY_FILTER_CITY'] ?: 'Все города';

/* Список магазинов для которых есть вакансии */
$shopsID = [];
foreach ($arResult['ITEMS'] as $vacancy) {
    $shopsID[] = $vacancy['PROPERTIES']['SHOP']['VALUE'];
}
$shopsID = array_unique($shopsID);

$shopsSelect = ['PROPERTY_CITY', 'PROPERTY_ADDRESS'];
$shopsFilter = ['ACTIVE' => 'Y', 'ID' => $shopsID];
$shopsResult = \ShopsModel::select($shopsSelect)->filter($shopsFilter)
    ->cache(30)
    ->sort('name')->getList()->toArray();

/* Получим список городов в которых есть вакансии */
$arResult['CITIES'] = array_merge(['Все города'], array_unique(array_column($shopsResult, 'PROPERTY_CITY_VALUE')));

if ($arResult['CURRENT_CITY'] != 'Все города') {
    /* Если в фильтре выбран город удалим магазины остальных городов */
    foreach ($shopsResult as $kShop => $shop) {
        if ($arResult['CURRENT_CITY'] != $shop['PROPERTY_CITY_VALUE']) {
            unset($shopsResult[$kShop]);
            continue;
        }
        $shopsResult[$kShop]['FILTER_STRING'] = $shop['PROPERTY_ADDRESS_VALUE'];
    }
} else {
    /* Иначе добавим названия городов к адресам */
    foreach ($shopsResult as $kShop => $shop) {
        $shopsResult[$kShop]['FILTER_STRING'] = $shop['PROPERTY_CITY_VALUE'] . ', ' . $shop['PROPERTY_ADDRESS_VALUE'];
    }
}

/* Список магазинов в выбранном городе */
$arResult['SHOPS_FILTERED'] = array_merge([['FILTER_STRING' => 'Все магазины']], $shopsResult);

$filteredShopsID = array_column($shopsResult, 'ID');
foreach ($arResult['ITEMS'] as $arKey => $arItem) {
    $shopID = $arItem['PROPERTIES']['SHOP']['VALUE'];
    if (in_array($shopID, $filteredShopsID)) {
        /* Добавим в результирующий массив привязанные магазины */
        $arResult['ITEMS'][$arKey]['SHOP'] = $shopsResult[$shopID];
    } else {
        /* Или удалим вакансии для которых не осталось магазинов */
        unset($arResult['ITEMS'][$arKey]);
    }
}

/* Список названий вакансий */
$arResult['VACANCIES'] = array_merge(['Все вакансии'], array_unique(array_column($arResult['ITEMS'], 'NAME')));


$component = $this->getComponent();
$component->setResultCacheKeys(['CURRENT_CITY', 'CITIES', 'SHOPS', 'VACANCIES']);