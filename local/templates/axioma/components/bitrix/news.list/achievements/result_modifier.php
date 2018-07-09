<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 */

/* Соберем галереи */
foreach ($arResult['ITEMS'] as &$item) {
    if (count($item['PROPERTIES']['GALLERY']['VALUE']) > 1) {
        foreach ($item['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE'] as $file) {
            $item['GALLERY'][] = [
                'SRC' => $file['SRC'],
                'DESCRIPTION' => $file['DESCRIPTION']
            ];
        }
    } else if (!$item['PROPERTIES']['GALLERY']['VALUE'] == false) {
        $item['GALLERY'][] = [
            'SRC' => $item['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE']['SRC'],
            'DESCRIPTION' => $item['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE']['DESCRIPTION']
        ];
    }
}