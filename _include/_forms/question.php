<div id="form_question" class="form__question" style="display: none;">
    <? $APPLICATION->IncludeComponent(
        "axi:feedback",
        "main",
        array(
            "EVENT_ALIAS" => FOS_QUESTION_EVENT,
            "STORE_IBLOCK_ID" => FOS_QUESTION_IB,
            "FIELDS" => array(
                "ANSWER_NAME" => "Имя",
                "ANSWER_PHONE" => "Телефон",
                "ANSWER_EMAIL" => "E-mail",
                "ANSWER_TEXT" => "Вопрос",
            ),
            "REQUIRED_FIELDS" => array(
                "ANSWER_NAME",
                "ANSWER_PHONE",
                "ANSWER_EMAIL",
                "ANSWER_TEXT",
            ),
            "UPLOAD_FILE" => false,
            "OK_MESSAGE" => "Спасибо, ваш вопрос принят!"
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    ); ?>
</div>