
<?php 
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if($_REQUEST['add']=='Y'){
    $result = Zone::Add($_REQUEST);
    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}

if($_REQUEST['save']=='Y' and $_REQUEST['id']>0 and !empty($_REQUEST['code']) ){
    $result = Zone::Update($_REQUEST['id'], $_REQUEST);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}