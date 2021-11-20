<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Блокированные");
?><?$APPLICATION->IncludeComponent("strategy:locked.address", "")?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>