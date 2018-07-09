<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$assets = Bitrix\Main\Page\Asset::getInstance();
$assetsPath = SITE_TEMPLATE_PATH . "/assets/modules";

$assets->addJs("https://maps.googleapis.com/maps/api/js?key="  . GOOGLE_API_KEY);
$assets->addJs($assetsPath . "/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js");