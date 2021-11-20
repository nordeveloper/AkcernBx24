<?php
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule('crm'))
{
    ShowError(GetMessage('CRM_MODULE_NOT_INSTALLED'));
    return;
}

if(intval($_REQUEST['id'])>0 and $_REQUEST['visit_date']){
    $oContact = new CCrmContact(false);

    $arFields = array("UF_CRM_1593888851"=>$_REQUEST['visit_date']);

    $res = $oContact->Update($_REQUEST['id'], $arFields);

    echo json_encode($res);
}
