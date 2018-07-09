<?
use Axi\Helpers as Axi;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Промо в универсамах");
?>
<? Axi::GT('promo_text') ?>
<? Axi::GF('_pages/promo_form') ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>