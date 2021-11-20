<?php 
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if( strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){

    if($_REQUEST['region_id']){
        $data['region_id'] = trim($_REQUEST['region_id']);
        $res = Zone::getList(  $data );
        
        $str = '';
        $str.='<option value=""></option>';
        foreach($res as $item){
            $str.='<option value="'.$item['code'].'">'.$item['code'].'</option>';
        }
        echo $str;
    }

}