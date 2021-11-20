<?php 
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("NEED_AUTH", true); 

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if( strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){

   if($_REQUEST['state']==1){
    $res = HideImageFromSite($_REQUEST['photo_id'], $_REQUEST['el_id']);
   }else{
    $res = ShowImageFromSite($_REQUEST['photo_id']);
   }

   $el = new CIBlockElement;
   $arFields['MODIFIED_BY'] = $USER->getID();
   $el->Update($_REQUEST['el_id'], $arFields);

   echo json_encode($res);
}