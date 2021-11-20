
<?php 
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if( !empty($_REQUEST['zone']) ){

    // dump($_REQUEST['zone']);
    $data['zone'] = $_REQUEST['zone'];
    $res = Street::getList($data);

    echo '<option value=""></option>';
    if(!empty($res['ITEMS'])){
        foreach ($res['ITEMS'] as $arItem){
            echo '<option data-zone="'.$arItem['zone_code'].'" value="'.$arItem['id'].'">'.$arItem['name_'.LANGUAGE_ID].'</option>';
        }
    }
}
