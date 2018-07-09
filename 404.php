<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/urlrewrite.php');

if (substr_count($_SERVER['REQUEST_URI'], '/api/') < 1) {
    CHTTP::SetStatus('404 Not Found');
    @define('ERROR_404', 'Y');
}

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetTitle("Страница не найдена");
?>

    <div class="errorpage">
        <div class="errorpage-num">404</div>
        <div class="errorpage-button"><a class="g-btn g-btn-red g-btn-rounded" href="/">Вернуться на главную</a></div>
    </div>

<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>