<?php
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule("iblock"))
    return;

//if( strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){

    $arFilter['IBLOCK_ID'] = REALTY_IBLOCK_ID;
    $arFilter['ACTIVE'] = "Y";
    $arFilter['ACTIVE_DATE'] ="Y";

    if(!empty($_REQUEST['name'])){
        $arFilter['%NAME'] = trim($_REQUEST['name']);
    }

    if(!empty($_REQUEST['DEAL_TYPE'])){
        $arFilter['PROPERTY_DEAL_TYPE'] = $_REQUEST['DEAL_TYPE'];
    }

    $arSelect = array("ID", "NAME");

    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while($row = $res->GetNext())
    {
        $arElement['id'] = $row['ID'];
        $arElement['text'] = $row['NAME'];
        $results[] = $arElement;
    }

    echo json_encode($results);

//}