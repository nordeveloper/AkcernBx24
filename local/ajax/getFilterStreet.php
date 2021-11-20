
<?php
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$data = array();

if( !empty($_REQUEST['region_id']) and intval($_REQUEST['region_id'])>0 ){
    $data['region_id'] = $_REQUEST['region_id'];
}

if(!empty($_REQUEST['zone']) ){
    $data['zone'] = $_REQUEST['zone'];
}

if(!empty($_REQUEST['street']) ){
    $data['name_'.LANGUAGE_ID] = trim($_REQUEST['street']);
}


$res = Street::getList($data);

if(!empty($res['ITEMS'])){
    $str = '<div class="street-list finder-box">';
    foreach ($res['ITEMS'] as $arItem){
        $str.= '<div class="finder-item" data-zone="'.$arItem['zone_code'].'" data-name="'.$arItem['name_'.LANGUAGE_ID].'" data-val="'.$arItem['id'].'">'.$arItem['name_'.LANGUAGE_ID].'</div>';
    }
    $str.= '</div>';

    echo $str;
}
