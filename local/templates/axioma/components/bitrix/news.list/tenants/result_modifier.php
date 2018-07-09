<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 */

/* Получим выбраный город */
$arResult['CURRENT_CITY'] = $_COOKIE['TENANT_FILTER_CITY'] ?: 'Все города';

/* Соберем Фильтр */
$arCities = [];
$rawCities = \TenantsModel::select(['NAME', 'PROPERTY_CITY'])->filter(['ACTIVE' => 'Y'])
    ->cache(30)
    ->sort('sort')->getList()->toArray();
$arCities = array_count_values(array_column($rawCities, 'PROPERTY_CITY_VALUE'));
asort($arCities, SORT_STRING);

$allCitiesAr['Все города'] = array_sum(array_values($arCities));
$arResult['CITIES'] = array_merge($allCitiesAr, $arCities);

/* Соберем галереи */
foreach ($arResult['ITEMS'] as &$item) {
    if (count($item['PROPERTIES']['GALLERY']['VALUE']) > 1) {
        foreach ($item['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE'] as $file) {
            $item['GALLERY'][] = $file['SRC'];
        }
    } else if (!$item['PROPERTIES']['GALLERY']['VALUE'] == false) {
        $item['GALLERY'][] = $item['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE']['SRC'];
    } else {
        $item['GALLERY'][] = NO_IMAGE;
    }
}

$component = $this->getComponent();
$component->setResultCacheKeys(['CITIES']);