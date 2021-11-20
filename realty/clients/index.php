<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Клиенты");
?>

<?$APPLICATION->IncludeComponent(
	"strategy:contact.list", 
	"", 
	array(
		"CONTACT_COUNT" => "30",
		"COMPONENT_TEMPLATE" => ""
	),
	false
);?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>