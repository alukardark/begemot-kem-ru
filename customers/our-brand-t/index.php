<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
use Axi\Helpers as Axi;
$APPLICATION->SetTitle("Наша марка");
?>

<? Axi::GT('our-brand_text') ?>
<? Axi::GF('_pages/our-brand_form') ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>