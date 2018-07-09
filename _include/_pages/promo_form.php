<?php
/**
 * @var CMain $APPLICATION
 */
ob_start();
?>
    <div class="ipage__head__feedback" data-aos="fade">
        <div class="ipage__head__feedback__button">
            <a href="#form_promo" class="fancymodal-call g-btn g-btn-rounded g-btn-red">Записаться на промо</a>
        </div>
    </div>
    <? Axi\Helpers::GF('_forms/promo') ?>
<?
$content = ob_get_clean();

$APPLICATION->AddViewContent('page_feedback', $content);
