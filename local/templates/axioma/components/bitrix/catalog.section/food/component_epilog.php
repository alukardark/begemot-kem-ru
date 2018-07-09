<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (get_class($this->__template) !== "CBitrixComponentTemplate") $this->InitComponentTemplate();

$this->__template->SetViewTarget('page_feedback');

Axi\Helpers::GF('_forms/review_btn');
Axi\Helpers::GF('_forms/review');

$this->__template->EndViewTarget();