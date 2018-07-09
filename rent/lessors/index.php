<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
use Axi\Helpers as Axi;
$APPLICATION->SetTitle("Арендодателям");
?>

<? Axi::GT('lessors_text') ?>
<? Axi::GF('_pages/lessors_form') ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>