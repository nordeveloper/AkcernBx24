<?php
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule("iblock"))
    return;

$arFilter['IBLOCK_ID'] = CONTACT_IBLOCK_ID;
$arFilter['ACTIVE'] = "Y";
//$arFilter['CREATED_BY'] = $USER->getID();

if(!empty($_REQUEST['listContact'])){
    $arFilter['%NAME'] = trim($_REQUEST['listContact']);
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