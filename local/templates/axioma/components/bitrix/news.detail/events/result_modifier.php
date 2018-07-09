<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (count($arResult['PROPERTIES']['MORE_PHOTO']['VALUE']) > 1) {
    foreach ($arResult['DISPLAY_PROPERTIES']['MORE_PHOTO']['FILE_VALUE'] as $file) {
        $arResult['MORE_PHOTO'][] = $file['SRC'];
    }
} else if (!$arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] == false) {
    $arResult['MORE_PHOTO'][] = $arResult['DISPLAY_PROPERTIES']['MORE_PHOTO']['FILE_VALUE']['SRC'];
}