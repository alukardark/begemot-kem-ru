<div id="form_action_member" class="form__action_member" style="display: none;">
    <? $APPLICATION->IncludeComponent(
        "axi:feedback",
        "main",
        array(
            "EVENT_ALIAS" => FOS_ACTION_MEMBER_EVENT,
            "STORE_IBLOCK_ID" => FOS_ACTION_MEMBER_IB,
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