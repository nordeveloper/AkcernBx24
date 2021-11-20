<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Блок аналитики");
?>


<?$APPLICATION->IncludeComponent(
	"strategy:realty.analitic", 
	"", 
	array(
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>