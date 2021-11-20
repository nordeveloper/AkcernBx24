<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Блокировка по коду");
?><?$APPLICATION->IncludeComponent("strategy:locked.realty", "")?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>