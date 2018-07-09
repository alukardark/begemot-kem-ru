<div class="main__poll">
    <div class="main__poll__header">
        <div class="main_section_title main__news-title">Опрос</div>
    </div>

    <? $APPLICATION->IncludeComponent(
        "altasib:altasib.simplevote",
        "main",
        array(
            "AJAX_MODE" => "Y",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "CACHE_TIME" => "60",
            "CACHE_TYPE" => "A",
            "COLOR" => "#acacac",
            "IBLOCK_ID" => "2",
            "IBLOCK_TYPE" => "altasib_simplevote",
            "IP_STOP" => "1",
            "POSITION" => "0",
            "SCALE_HEIGHT" => "",
            "SECTION_ID" => "0",
            "SHOW_IMAGE" => "0",
            "SHOW_RESULTS" => "1",
            "SHOW_VAR" => "1",
            "STRING_RES" => "",
            "STRING_VOTE" => "",
            "UNSORT_VARIANT" => "0",
            "VARIANT_IMAGE_POSITION" => "0",
            "VOTE_WIDTH" => "",
            "COMPONENT_TEMPLATE" => "main"
        ),
        false
    ); ?>
</div>