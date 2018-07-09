<?
use Axi\Helpers as Axi;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?>

<? Axi::GT('contacts_text'); ?>
    <br>
    <br>
<? Axi::GT('contacts_map'); ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>