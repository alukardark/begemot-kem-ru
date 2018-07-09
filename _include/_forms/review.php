<div id="form_review" class="form__review" style="display: none;">
    <? $APPLICATION->IncludeComponent(
        "axi:feedback",
        "main",
        array(
            "EVENT_ALIAS" => FOS_REVIEW_EVENT,
            "STORE_IBLOCK_ID" => FOS_REVIEW_IB,
            "FIELDS" => array(
                "ANSWER_NAME" => "Имя",
                "ANSWER_PHONE" => "Телефон",
                "ANSWER_EMAIL" => "E-mail",
                "ANSWER_TEXT" => "Отзыв",
            ),
            "REQUIRED_FIELDS" => array(
                "ANSWER_NAME",
                "ANSWER_PHONE",
                "ANSWER_EMAIL",
                "ANSWER_TEXT"
            ),
            "UPLOAD_FILE" => "Прикрепить фaйл",
            "OK_MESSAGE" => "Спасибо, ваш отзыв принят!"
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    ); ?>
</div>