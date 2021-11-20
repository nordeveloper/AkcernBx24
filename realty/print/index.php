<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("print");
?>
<? 
if($_REQUEST['ids'] and $_REQUEST['gridID']){
    PrintData( $_REQUEST['ids'], $_REQUEST['gridID'], $_REQUEST['iblockID']);
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>