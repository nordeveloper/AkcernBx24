<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Очеред запросов");
?>
<?$APPLICATION->IncludeComponent(
	"strategy:requests.queue",
	"",
Array()
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>