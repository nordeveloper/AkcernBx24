<?php

define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (intval($_REQUEST['region'])>0) {

    $data['region_id'] = $_REQUEST['region'];
    $res = City::getList($data);

    echo '<option value=""></option>';
    if (!empty($res['ITEMS'])) {
        foreach ($res['ITEMS'] as $arItem) {
            echo '<option value="' . $arItem['id'] . '">' . $arItem['name_' . LANGUAGE_ID] . '</option>';
        }
    }
}