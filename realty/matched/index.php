<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Совпадении");
?>

<?$APPLICATION->IncludeComponent(
	"strategy:realty.requests.matched", 
	"", 
	"",
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>