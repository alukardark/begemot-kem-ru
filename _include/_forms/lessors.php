<div id="form_lessors" class="form__lessors" style="display: none;">
    <? $APPLICATION->IncludeComponent(
        "axi:feedback",
        "main",
        array(
            "EVENT_ALIAS" => FOS_LESSORS_EVENT,
            "STORE_IBLOCK_ID" => FOS_LESSORS_IB,
            "FIELDS" => array(
                "ANSWER_NAME" => "Имя",
                "ANSWER_PHONE" => "Телефон",
                "ANSWER_EMAIL" => "E-mail"
            ),
            "REQUIRED_FIELDS" => array(
                "ANSWER_NAME",
                "ANSWER_PHONE",
                "ANSWER_EMAIL"
            ),
            "UPLOAD_FILE" => "Прикрепить форму",
            "OK_MESSAGE" => "Спасибо, ваша заявка принята!"
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    ); ?>
</div>