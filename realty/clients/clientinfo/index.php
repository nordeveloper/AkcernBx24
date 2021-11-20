<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Kарточка клиента");
?>

<?$APPLICATION->IncludeComponent(
	"strategy:contact.info",
	"",
	Array()
); ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>