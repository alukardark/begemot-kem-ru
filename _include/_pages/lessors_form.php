<?php
/**
 * @var CMain $APPLICATION
 */
ob_start();
?>
    <div class="ipage__head__feedback" data-aos="fade">
        <div class="ipage__head__feedback__button">
            <a href="#form_lessors" class="fancymodal-call g-btn g-btn-rounded g-btn-red">Сдать в аренду</a>
        </div>
    </div>
    <? Axi\Helpers::GF('_forms/lessors') ?>
<?
$content = ob_get_clean();

$APPLICATION->AddViewContent('page_feedback', $content);
