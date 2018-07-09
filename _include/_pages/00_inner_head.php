<div class="ipage__head">

    <? $APPLICATION->IncludeComponent(
        "bitrix:breadcrumb",
        "axi",
        Array(
            "START_FROM" => "0",
            "PATH" => "",
            "SITE_ID" => "-"
        ),
        false
    ); ?>

    <div class="ipage__head__title">
        <h1 data-aos="fade" data-aos-delay="200"><? $APPLICATION->ShowTitle(false); ?></h1>

        <? $APPLICATION->ShowViewContent('page_feedback'); ?>
    </div>


</div>