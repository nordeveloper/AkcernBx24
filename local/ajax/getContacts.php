<?php 
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule('crm'))
{
	ShowError(GetMessage('CRM_MODULE_NOT_INSTALLED'));
	return;
}

if( !empty($_REQUEST['q']) ){

    $q = trim($_REQUEST['q']);
    $res = getContacts($q);

    if($res){
        foreach ($res as $arContatct){
            $contatct['id'] = $arContatct['CONTACT_ID'];
            $contatct['text'] = $arContatct['FULL_NAME'];
            $results[] = $contatct;
        }
    }

    echo json_encode($results, JSON_UNESCAPED_UNICODE);
};