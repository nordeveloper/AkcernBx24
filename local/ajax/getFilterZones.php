<?php
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$data = array();

if($_REQUEST['zone']){
    $data['zone'] = trim($_REQUEST['zone']);
}

$res = Zone::getList($data);

$html = '<div class="zone-list finder-box">';
    if(!empty($res)){
        foreach ($res as $item){
            $html.='<label class="finder-item" data-val="'.$item['code'].'">'.$item['code'].'</label>';
        }
    }
$html.='</div>';
echo $html;