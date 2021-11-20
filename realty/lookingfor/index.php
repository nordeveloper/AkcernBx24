<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Сейчас ищут");
?>

<?$APPLICATION->IncludeComponent(
	"strategy:realty.looking_realty", 
	".default", 
	array(),
	false
);?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>