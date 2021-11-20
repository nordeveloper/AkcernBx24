<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Редактирование городов");
?>
<?$APPLICATION->IncludeComponent(
	"strategy:location.cities", 
	".default", 
	array(),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>