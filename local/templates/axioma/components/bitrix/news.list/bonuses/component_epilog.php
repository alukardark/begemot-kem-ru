<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (get_class($this->__template) !== "CBitrixComponentTemplate") $this->InitComponentTemplate();

$this->__template->SetViewTarget('page_feedback');
?>
    <div class="ipage__head__feedback" data-aos="fade">
        <div class="ipage__head__feedback__button">
            <a href="#form_question" class="fancymodal-call g-btn g-btn-rounded g-btn-red">Задать вопрос о бонусной программе</a>
        </div>
    </div>
    <? Axi\Helpers::GF('_forms/question') ?>
<?
$this->__template->EndViewTarget();