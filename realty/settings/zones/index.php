<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Зоны");
?><br>
<?$APPLICATION->IncludeComponent(
	"strategy:location.zone", 
	".default", 
	array(),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>