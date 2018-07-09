<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 */

/* Соберем галереи */
foreach ($arResult['ITEMS'] as &$item) {
    if (count($item['PROPERTIES']['BOOKLET']['VALUE']) > 1) {
        foreach ($item['DISPLAY_PROPERTIES']['BOOKLET']['FILE_VALUE'] as $file) {
            $item['BOOKLET'][] = $file['SRC'];
        }
    } else if (!$item['PROPERTIES']['BOOKLET']['VALUE'] == false) {
        $item['BOOKLET'][] = $item['DISPLAY_PROPERTIES']['BOOKLET']['FILE_VALUE']['SRC'];
    }
}