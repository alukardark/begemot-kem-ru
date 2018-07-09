<div id="form_tenants" class="form__tenants" style="display: none;">
    <? $APPLICATION->IncludeComponent(
        "axi:feedback",
        "main",
        array(
            "EVENT_ALIAS" => FOS_TENANTS_EVENT,
            "STORE_IBLOCK_ID" => FOS_TENANTS_IB,
            "FIELDS" => array(
                "ANSWER_NAME" => "Имя",
                "ANSWER_PHONE" => "Телефон",
                "ANSWER_EMAIL" => "E-mail",
                "ANSWER_TEXT" => "Сообщение"
            ),
            "REQUIRED_FIELDS" => array(
                "ANSWER_NAME",
                "ANSWER_PHONE",
                "ANSWER_EMAIL",
                "ANSWER_TEXT"
            ),
            "UPLOAD_FILE" => "Прикрепить файл",
            "OK_MESSAGE" => "Спасибо, ваша заявка принята!"
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    ); ?>
</div>