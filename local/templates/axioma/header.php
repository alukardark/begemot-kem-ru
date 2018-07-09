<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @global object $APPLICATION
 */

global $USER, $isMainPage, $curSection, $curSubSection, $CURRENT_CITY;

use Axi\Helpers as Axi;

$axi = Axi::getInstance();
$curAlias = $axi->getAlias();
$curSection = $axi->getSection();
$curSubSection = $axi->getSection('sub');
$sectionName = $axi->getSectionName();

Axi::SetGlobalCity();

$isMainPage = $curAlias == 'indexpage' ? true : false;

$assets = Bitrix\Main\Page\Asset::getInstance();
$assetsPath = SITE_TEMPLATE_PATH . "/assets/modules";

$assets->addJs($assetsPath . "/jquery/dist/jquery.min.js");
$assets->addJs($assetsPath . "/cookies/cookies.js");
$assets->addJs($assetsPath . "/inputmask/dist/min/jquery.inputmask.bundle.min.js");
$assets->addJs($assetsPath . "/jquery.uploadedFileInfo/jquery.uploadedFileInfo.js");
$assets->addJs($assetsPath . "/popper.js/dist/umd/popper.min.js");

$assets->addJs($assetsPath . "/bootstrap/dist/js/bootstrap.min.js");
$assets->addCss($assetsPath . "/bootstrap/dist/css/bootstrap.min.css");

$assets->addJs($assetsPath . "/slick-carousel/slick/slick.js");
$assets->addCss($assetsPath . "/slick-carousel/slick/slick.css");

$assets->addJs($assetsPath . "/@fancyapps/fancybox/dist/jquery.fancybox.min.js");
$assets->addCss($assetsPath . "/@fancyapps/fancybox/dist/jquery.fancybox.min.css");

$assets->addJs($assetsPath . "/aos/dist/aos.js");
$assets->addCss($assetsPath . "/aos/dist/aos.css");

$assets->addJs($assetsPath . "/waitMe/waitMe.min.js");
$assets->addCss($assetsPath . "/waitMe/waitMe.min.css");

$assets->addCss($assetsPath . "/ionicons/css/ionicons.min.css");
$assets->addCss($assetsPath . "/font-awesome/css/font-awesome.min.css");

$assets->addJs(SITE_TEMPLATE_PATH . "/assets/_js/common.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/assets/_js/forms.js");

$assets->addString('<link href="' . SITE_DIR . 'favicon.ico?v=2" rel="shortcut icon"  type="image/x-icon" />');
?>

<!DOCTYPE html>
<html xml:lang="<?= LANGUAGE_ID ?>" lang="<?= LANGUAGE_ID ?>">

<head>
    <title><?= SITE_NAME . " — " ?><? $APPLICATION->ShowTitle('title', true); ?></title>

    <!-- <link rel="manifest" href="/manifest.json"> -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="author" content="web-studio «AXIOMA»"/>
    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="HandheldFriendly" content="true"/>
    <meta name="MobileOptimized" content="width"/>
    <meta name="yandex-verification" content=""/>
    <meta name="google-site-verification" content=""/>

    <? $APPLICATION->ShowHead(true); ?>

    <!--[if IE]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js" data-skip-moving="true"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js" data-skip-moving="true"></script>
    <![endif]-->

    <link href="/_include/custom.css" rel="stylesheet" type="text/css">
</head>

<body class="<?= $curAlias ?>">
<? if ($USER->IsAdmin()) : ?>
    <div id="panel"><? $APPLICATION->ShowPanel(); ?></div>
<? endif; ?>

<div id="site-wraper" class="site-wraper">

    <div id="site-content" class="site-content in-<?=$curSection?>">

        <div id="site-header" class="site-header">
            <? Axi::GF("00_header"); ?>
        </div>

            <?php if (!$isMainPage): ?>
                <div class="ipage" <? if (!in_array($curSubSection, AOS_OFF_PAGES)): ?>data-aos="fade"<? endif; ?>>
                    <div class="wraper_global_outer">
                        <div class="wraper_global_inner">
                            <? Axi::GF("_pages/00_inner_head"); ?>
            <?php endif; ?>