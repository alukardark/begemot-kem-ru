<div id="form_feedback" class="form__feedback" style="display: none;">
    <? $APPLICATION->IncludeComponent(
        "axi:feedback",
        "main",
        array(
            "EVENT_ALIAS" => FOS_FEEDBACK_EVENT,
            "STORE_IBLOCK_ID" => FOS_FEEDBACK_IB,
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
                "ANSWER_TEXT",
            ),
            "UPLOAD_FILE" => false,
            "OK_MESSAGE" => "Спасибо, ваше сообщение принято!"
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    ); ?>
</div>