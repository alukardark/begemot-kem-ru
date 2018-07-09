<?php
use Axi\Helpers as Axi;

$googleplay = Axi::GS('googleplay');
$appstore = Axi::GS('appstore');
?>

<div class="footer" data-aos="fade">
    <div class="scrolltop scrolltop_320">
        <i class="ion-chevron-up"></i>
    </div>

    <div class="wraper_global_outer">
        <div class="wraper_global_inner footer__wraper">

            <div class="footer__left" data-aos="fade" data-aos-delay="100">
                <a href="/" class="footer__logo footer__logo__pc"></a>
                <a href="/" class="footer__logo footer__logo__sm"></a>

                <?php if ($googleplay || $appstore): ?>
                    <div class="footer__applications">
                        <div class="footer__applications-title">
                            <? Axi::GT('footer_applications_title') ?>
                        </div>
                        <div class="footer__applications__wraper">
                            <?php if ($googleplay): ?>
                                <a href="<?= $googleplay ?>" class="footer__applications__googleplay" rel="nofollow"
                                   target="_blank"></a>
                            <?php endif ?>
                            <?php if ($appstore): ?>
                                <a href="<?= $appstore ?>" class="footer__applications__appstore" rel="nofollow"
                                   target="_blank"></a>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endif ?>

                <div class="footer__socnets__pc">
                    <? Axi::GF('00_footer_socnets') ?>
                </div>
            </div>

            <div class="footer__center" data-aos="fade" data-aos-delay="200">
                <? $APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "pc_bottom",
                    array(
                        "ALLOW_MULTI_SELECT" => "N",
                        "CHILD_MENU_TYPE" => "left",
                        "DELAY" => "N",
                        "MAX_LEVEL" => "2",
                        "MENU_CACHE_GET_VARS" => array(),
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_TYPE" => "A",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "ROOT_MENU_TYPE" => "bottom",
                        "USE_EXT" => "N",
                        "COMPONENT_TEMPLATE" => "pc_bottom"
                    ),
                    false
                ); ?>
            </div>

            <div class="footer__right" data-aos="fade" data-aos-delay="300">
                <div class="footer__hotline">
                    Горячая линия: <a href="tel:<?= Axi::GS('hotline') ?>"><?= Axi::GS('hotline') ?></a>
                </div>
                <div class="footer__officephone">
                    Наш офис: <a href="tel:<?= Axi::GS('officephone') ?>"><?= Axi::GS('officephone') ?></a>
                </div>
                <div class="footer__address">
                    <?= Axi::GS('address') ?>
                </div>
                <div class="footer__email">
                    <a href="mailto:<?= Axi::GS('email') ?>"><?= Axi::GS('email') ?></a>
                </div>

                <div class="footer__legallinks">
                    <a href="/copyright/" target="_blank">Информация для правообладателей</a><br>
                    <a href="/privacy-policy/" target="_blank">Политика конфиденциальности</a>
                </div>

            </div>

            <div class="footer__socnets__xs">
                <? Axi::GF('00_footer_socnets') ?>
            </div>

            <a href="https://www.web-axioma.ru" title="Создание, продвижение и администрирование сайтов"
               target="_blank" rel="nofollow" class="footer__axi"></a>

        </div>
    </div>
</div>