<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 */

/* Сгруппируем магазины по городам */
/* Список магазинов с нужной сортировкой */
$propCityDB = CIBlock::GetProperties(SHOPS_IB, [], ["CODE" => "CITY"]);
$propCityRes = $propCityDB->Fetch();
$citiesListDB = CIBlockProperty::GetPropertyEnum($propCityRes['ID'], ['sort' => 'asc']);
$citiesListAr = [];
while($citiesListRes = $citiesListDB->Fetch()) {
    $citiesListAr[] = $citiesListRes['VALUE'];
}

/* Список магазинов с их количеством */
$citiesCount = [];
foreach ($arResult['ITEMS'] as $arItem) {
    $citiesCount[] = $arItem['DISPLAY_PROPERTIES']['CITY']['VALUE'];
}
$citiesCount = array_count_values($citiesCount);

/* Список магазинов с нужной сортировкой и их количеством */
$cities = [];
foreach ($citiesListAr as $citiesListArItem) {
    $cities[$citiesListArItem] = $citiesCount[$citiesListArItem];
}

$arCities = [];
foreach ($cities as $cityName => $count) {
    $arCities[$cityName]['COUNT'] = $count;

    foreach ($arResult['ITEMS'] as $item) {
        if ($item['DISPLAY_PROPERTIES']['CITY']['VALUE'] != $cityName) continue;

        /* Добавим список районов */
        if ($item['DISPLAY_PROPERTIES']['DISTRICT']['VALUE'])
            $arCities[$cityName]['DISTRICTS'][] = $item['DISPLAY_PROPERTIES']['DISTRICT']['VALUE'];

        $arCities[$cityName]['SHOPS'][] = $item;
    }

    if (isset($arCities[$cityName]['DISTRICTS'])) {
        asort($arCities[$cityName]['DISTRICTS'], SORT_STRING);
        $arCities[$cityName]['DISTRICTS'] = array_count_values($arCities[$cityName]['DISTRICTS']);
    }
}
$arResult['ITEMS'] = $arCities;

/* Получим выбраный город */
$arResult['CURRENT_CITY'] = $arParams['CURRENT_CITY'];

/* Получим список доп услуг */
$dbRes = CIBlockPropertyEnum::GetList(["SORT" => "ASC"], ["IBLOCK_ID" => SHOPS_IB, "CODE" => "FEATURES"]);
while ($feature = $dbRes->Fetch()) {
    $arResult['FEATURES'][] = $feature;
}

/* Соберем галереи */
foreach ($arResult['ITEMS'] as &$item) {
    foreach ($item['SHOPS'] as &$shop) {
        if (count($shop['PROPERTIES']['GALLERY']['VALUE']) > 1) {
            foreach ($shop['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE'] as $file) {
                $shop['GALLERY'][] = $file['SRC'];
            }
        } else if (!$shop['PROPERTIES']['GALLERY']['VALUE'] == false) {
            $shop['GALLERY'][] = $shop['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE']['SRC'];
        }
    }
}

//var_dump($arResult['ITEMS']);

$component = $this->getComponent();
$component->setResultCacheKeys(['FEATURES']);