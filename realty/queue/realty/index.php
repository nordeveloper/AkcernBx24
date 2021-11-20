<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Очеред недвижимости");
?>
<?$APPLICATION->IncludeComponent(
	"strategy:realty.queue",
	"",
Array()
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>