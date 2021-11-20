<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Очередь бонусов");
?>
<?$APPLICATION->IncludeComponent(
    "strategy:realty.queuebonuse",
    "",
    Array()
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>