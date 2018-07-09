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

    if (count($item['PROPERTIES']['PARTNERS']['VALUE']) > 1) {
        foreach ($item['DISPLAY_PROPERTIES']['PARTNERS']['FILE_VALUE'] as $file) {
            $item['PARTNERS'][] = [
                'SRC' => $file['SRC'],
                'DESCRIPTION' => $file['DESCRIPTION']
            ];
        }
    } else if (!$item['PROPERTIES']['PARTNERS']['VALUE'] == false) {
        $item['PARTNERS'][] = [
            'SRC' => $item['DISPLAY_PROPERTIES']['PARTNERS']['FILE_VALUE']['SRC'],
            'DESCRIPTION' => $item['DISPLAY_PROPERTIES']['PARTNERS']['FILE_VALUE']['DESCRIPTION']
        ];
    }
}