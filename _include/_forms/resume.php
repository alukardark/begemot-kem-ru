<div id="form_resume" class="form__resume" style="display: none;">
    <? $APPLICATION->IncludeComponent(
        "axi:feedback",
        "main",
        array(
            "EVENT_ALIAS" => FOS_RESUME_EVENT,
            "STORE_IBLOCK_ID" => FOS_RESUME_IB,
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
            "UPLOAD_FILE" => "Прикрепить резюме",
            "OK_MESSAGE" => "Спасибо, ваше резюме принято!"
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    ); ?>
</div>