<?php
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$data = array();

if(!empty($_REQUEST['city']) ){
    $data['name_'.LANGUAGE_ID] = trim($_REQUEST['city']);
}

$res = City::getList($data);

$html = '<div class="cities-list finder-box">';
    if( !empty($res['ITEMS']) ){
        foreach ($res['ITEMS'] as $item){
            $html.='<div class="finder-item" data-name="'.$item['name_'.LANGUAGE_ID].'" data-val="'.$item['id'].'">'.$item['name_'.LANGUAGE_ID].'</div>';
        }
    }
$html.='</div>';
echo $html;