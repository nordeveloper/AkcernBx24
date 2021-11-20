<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Журнал посещений");
?>
<div class="row">
	<div class="col-12">
	<h3 class="text-center page-title">Журнал посещений</h3>
		<?$APPLICATION->IncludeComponent("strategy:realty.visitshistory", "")?>
	</div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>