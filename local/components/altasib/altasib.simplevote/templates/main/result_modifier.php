<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arResult['ACTIVE_FROM_FORMATTED'] = FormatDate("j F Y", MakeTimeStamp($arResult["ACTIVE_FROM"], CSite::GetDateFormat()));

$voters = $arResult['VOTE_PROP']['TOTALNUMBEROFVOTES'];

foreach ($arResult["VAR"] as &$item) {
    $item['PERCENT'] = round($item['NUMBER_OF_VOTES'] * 100 / $voters);
}