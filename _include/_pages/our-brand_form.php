<?php
/**
 * @var CMain $APPLICATION
 */
ob_start();
?>
    <div class="ipage__head__feedback" data-aos="fade">
        <div class="ipage__head__feedback__text">
            Пожалуйста, оставьте свой отзыв о нашей продукции СТМ
        </div>
        <div class="ipage__head__feedback__button">
            <a href="#form_review" class="fancymodal-call g-btn g-btn-rounded g-btn-red">Оставить отзыв</a>
        </div>
    </div>
    <? Axi\Helpers::GF('_forms/review') ?>
<?
$content = ob_get_clean();

$APPLICATION->AddViewContent('page_feedback', $content);
