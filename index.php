<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle(" Главная");
?>

<? Axi\Helpers::GF('_pages/_main/00_top_section'); ?>
<? Axi\Helpers::GF('_pages/_main/03_goodsoftheweek'); ?>
<? Axi\Helpers::GF('_pages/_main/04_bubbles'); ?>
<? Axi\Helpers::GF('_pages/_main/05_bottom_section'); ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>