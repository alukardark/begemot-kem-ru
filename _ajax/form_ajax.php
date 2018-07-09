<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Axi\Helpers as Axi;
use Bitrix\Main\Application;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$isPost = $request->isPost();
$sAction = $request->getPost("AJAX");
$template = $request->getPost("TEMPLATE");

if (!$isPost) {
    json_result(false, array('alert' => "Ошибочный запрос"));
}

if (empty($sAction)) {
    json_result(false, array('alert' => "Неверное действие", 'request' => $_REQUEST));
}

switch ($template) {
    case FOS_FEEDBACK_EVENT:
        Axi::GF("_forms/feedback");
        break;
    case FOS_QUESTION_EVENT:
        Axi::GF("_forms/question");
        break;
    case FOS_REVIEW_EVENT:
        Axi::GF("_forms/review");
        break;
    case FOS_ACTION_MEMBER_EVENT:
        Axi::GF("_forms/action_member");
        break;
    case FOS_RESUME_EVENT:
        Axi::GF("_forms/resume");
        break;
    case FOS_TENANTS_EVENT:
        Axi::GF("_forms/tenants");
        break;
    case FOS_LESSORS_EVENT:
        Axi::GF("_forms/lessors");
        break;
    case FOS_PROMO_EVENT:
        Axi::GF("_forms/promo");
        break;
    default:
        json_result(false, ['alert' => "Неизвестная ошибка"]);
}
