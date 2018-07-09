<?php
global $isMainPage, $CURRENT_CITY;

use Axi\Helpers as Axi;
?>

<div class="header">
    <div class="header__wraper wraper_global_outer">
        <a href="/" class="header__logo" data-aos="fade"></a>

        <div class="header__location" data-aos="fade" data-aos-delay="100">
            <div class="header__location__dropdown">
                <div class="dropdown__button"
                     data-toggle="dropdown" role="button" aria-expanded="false"
                >
                    <div class="dropdown__content"><?= $CURRENT_CITY ?></div>
                    <div class="dropdown__button-arrow"><i class="ion-ios-arrow-down"></i></div>
                </div>
                <div class="dropdown-menu dropdown__menu">
                    <?php foreach (CITIES_LIST as $city): ?>
                        <div class="dropdown__menu-item <?= $city == $CURRENT_CITY ? 'active' : '' ?>"
                             data-city="<?= $city ?>"
                        >
                            <div class="dropdown__content"><?= $city ?></div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
            <a href="/shops/" class="g-underline">Адреса универсамов</a>
        </div>

        <div class="header__socnets" data-aos="fade" data-aos-delay="200">
            <?php foreach (SOCNETS_LIST as $socnet): ?>
                <a href="<?= $socnet['PROPERTY_LINK_VALUE'] ?>" title="<?= $socnet['NAME'] ?>"
                   class="header__socnets__item" rel="nofollow" target="_blank">
                    <i class="fa fa-<?= $socnet['PROPERTY_CLASS_VALUE'] ?>"></i>
                </a>
            <?php endforeach ?>
        </div>

        <div class="header__feedback" data-aos="fade" data-aos-delay="300">
            <div class="header__feedback__text">
                <? Axi::GT('header_feedback_text') ?>
            </div>
            <div class="header__feedback__button">
                <a href="#form_feedback" class="fancymodal-call g-btn g-btn-rounded g-btn-red">Написать нам</a>
            </div>
            <? Axi::GF('_forms/feedback') ?>
        </div>
    </div>
</div>

<div class="mainnav" data-aos="fade">
    <div class="mainnav__wraper wraper_global_outer">
        <a href="#" class="mobnav__toogle">
            <span class="hamburger hamburger--squeeze">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </span>
            Меню
        </a>

        <div class="mainnav__menu" data-aos="fade" data-aos-delay="200">
            <? $APPLICATION->IncludeComponent(
                "bitrix:menu",
                "pc_top",
                array(
                    "ALLOW_MULTI_SELECT" => "N",
                    "CHILD_MENU_TYPE" => "left",
                    "DELAY" => "N",
                    "MAX_LEVEL" => "2",
                    "MENU_CACHE_GET_VARS" => array(),
                    "MENU_CACHE_TIME" => "3600",
                    "MENU_CACHE_TYPE" => "A",
                    "MENU_CACHE_USE_GROUPS" => "Y",
                    "ROOT_MENU_TYPE" => "top",
                    "USE_EXT" => "N",
                    "COMPONENT_TEMPLATE" => "pc_top"
                ),
                false
            ); ?>
        </div>

        <div class="mainnav__hotline">
            Горячая линия <a href="tel:<?= Axi::GS('hotline') ?>"><?= Axi::GS('hotline') ?></a>
        </div>
    </div>

    <div class="mobnav off">
        <? $APPLICATION->IncludeComponent(
            "bitrix:menu",
            "mobile_top",
            array(
                "ALLOW_MULTI_SELECT" => "N",
                "CHILD_MENU_TYPE" => "left",
                "DELAY" => "N",
                "MAX_LEVEL" => "2",
                "MENU_CACHE_GET_VARS" => array(),
                "MENU_CACHE_TIME" => "3600",
                "MENU_CACHE_TYPE" => "A",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "ROOT_MENU_TYPE" => "top",
                "USE_EXT" => "N",
                "COMPONENT_TEMPLATE" => "mobile_top"
            ),
            false
        ); ?>
    </div>
</div>