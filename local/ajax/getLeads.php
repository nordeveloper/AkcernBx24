<?php 
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule('crm'))
{
	ShowError(GetMessage('CRM_MODULE_NOT_INSTALLED'));
	return;
}


if( !empty($_REQUEST['contact']) ){

    $arFilter['%TITLE'] = $_REQUEST['contact'];

    $q = trim($_REQUEST['q']);
    $res = CCrmLead::GetList(array('ID'=>'DESC'), $arFilter, array());

    if($res){

        while($row= $res->fetch()){
            $lead['id'] = $row['ID'];
            $lead['text'] = $row["TITLE"].' '.$row['NAME'];
            $results[] = $lead;
        }
    }

    echo json_encode($results, JSON_UNESCAPED_UNICODE);
}