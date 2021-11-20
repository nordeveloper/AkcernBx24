
<?php 
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(is_array($_REQUEST['imgsort'])){

    foreach ( $_REQUEST['imgsort'] as $key=> $imgid ){
        if($imgid>0){
            $sql = "UPDATE b_file SET DESCRIPTION='$key' WHERE ID='$imgid'";
            $DB->Query($sql);
        }
    }
}






