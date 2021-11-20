<?php 
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", false);
define("NOT_CHECK_PERMISSIONS", true);

$_SERVER["DOCUMENT_ROOT"]  = '/home/bitrix/www';

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

ExportRealty();
?>

