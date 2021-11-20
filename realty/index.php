<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Недвижимость");
?>
<?$APPLICATION->IncludeComponent(
	"strategy:realty.info", 
	"", 
	array(
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>