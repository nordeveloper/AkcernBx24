<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
$this->setFrameMode(false);

if(!CModule::IncludeModule("iblock"))
{
    ShowError(GetMessage("CC_BIEAF_IBLOCK_MODULE_NOT_INSTALLED"));
    return;
}

if($USER->IsAuthorized()){

    $arGroupAdmins = $arParams['GROUPS_ADMINS'];
    $arGroupDirectors = $arParams['GROUP_DIRECTORS'];
    $arGroups = $USER->GetUserGroupArray();
    $AccessAdmins = array_intersect($arGroupAdmins, $arGroups);
    $AccessDirectors = array_intersect($arGroupDirectors, $arGroups);

    $arCurUser = getUser($USER->getID());

    if( intval($_GET['type'])>0 ){
        $skipFields = $arParams['PROPERTY_'.$_GET['type']];
    }

    // get iblock property list
    $rsIBLockPropertyList = CIBlockProperty::GetList(array("sort"=>"asc", "id"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"]));

    while ($arProperty = $rsIBLockPropertyList->GetNext())
    {

        if( in_array($arProperty['ID'], $skipFields) ) continue;

        // get list of property enum values
        if ($arProperty["PROPERTY_TYPE"] == "L")
        {
            $rsPropertyEnum = CIBlockProperty::GetPropertyEnum($arProperty["ID"]);
            $arProperty["ENUM"] = array();
            while ($arPropertyEnum = $rsPropertyEnum->GetNext())
            {
                $arProperty["ENUM"][$arPropertyEnum["ID"]] = $arPropertyEnum;
            }
        }

        // get user type propertyes
        if(strlen($arProperty["USER_TYPE"]) > 0 )
        {
            $arUserType = CIBlockProperty::GetUserType($arProperty["USER_TYPE"]);
            if(array_key_exists("GetPublicEditHTML", $arUserType))
                $arProperty["GetPublicEditHTML"] = $arUserType["GetPublicEditHTML"];
            else
                $arProperty["GetPublicEditHTML"] = false;
        }
        else
        {
            $arProperty["GetPublicEditHTML"] = false;
        }

        // add property to edit-list
        $arResult["PROPERTY_LIST"][$arProperty["ID"]] = $arProperty;
    }

    // load element data
    if(intval($_GET['ID'])>0){

        $arFilter['IBLOCK_ID']= $arParams['IBLOCK_ID'];
        $arFilter["ID"] = intval($_GET['ID']);
        // get current iblock element

        $dbResElement = \CIBlockElement::GetList(Array("ID"=>"ASC"), $arFilter, false, false,
            ['IBLOCK_ID', 'ID', 'NAME', 'DATE_CREATE', 'CREATED_BY', 'TIMESTAMP_X', 'PROPERTY_*']
        );

        if ($elOb = $dbResElement->GetNextElement())
        {
            $arElement = $elOb->GetFields();
            $arResult['ELEMENT'] = $arElement;
            $arResult['ELEMENT']['PROPERTIES'] = $elOb->GetProperties();
            $arUser = getUser($arResult['ELEMENT']['CREATED_BY']);
            $arResult['USER'] = $arUser;
        }

    }


    if (check_bitrix_sessid() && (!empty($_POST["btn_submit"]) || !empty($_POST["btn_apply"])))
    {

        $el = new CIBlockElement;

        $arProperties = $_POST["PROPERTY"];

        $arFields = array(
            "IBLOCK_ID"=>$arParams['IBLOCK_ID']
        );

        if(intval($_POST['CREATED_BY'])>0){
            $arFields['CREATED_BY'] = $_POST['CREATED_BY'];
            $arUser = getUser($_POST['CREATED_BY']);
            $departmentID = $arUser['UF_DEPARTMENT'][0];
            $department = $arUser['DEPARTMENT'];
        }else{
            $arFields['CREATED_BY'] = $USER->getID();
            $departmentID = $arCurUser['UF_DEPARTMENT'][0];
            $department =  $arCurUser['DEPARTMENT'];
        }

        if($arProperties[155]>0){
            $arContact = getContact($arProperties[155]);
            if($arContact['ID']>0){
                $arProperties[272] = $arContact['IDENTIFICATOR'];
            }
        }else{
            $arResult["ERRORS"] = getMessage('CLIENT_REQUIRED');
        }

        $arProperties[231] = $department;

        $arFields["PROPERTY_VALUES"] = $arProperties;

        if(intval($_POST['ID'])>0){

            $ID = $_POST['ID'];
            $arFields['MODIFIED_BY'] = $USER->getID();

            $res = $el->Update($ID, $arFields);
            
            if($res){
                $arResult["MESSAGE"] = getMessage('MSG_ADD_SUCCESS');
            }else{
                $arResult["ERRORS"] = $el->LAST_ERROR;
                foreach($arProperties as $key=> $val){                    
                    $arResult['ELEMENT']['PROPERTIES'][$arResult["PROPERTY_LIST"][$key]['CODE']]['ID'] = $key;
                    $arResult['ELEMENT']['PROPERTIES'][$arResult["PROPERTY_LIST"][$key]['CODE']]['VALUE_ENUM_ID'] = $val;
                    $arResult['ELEMENT']['PROPERTIES'][$arResult["PROPERTY_LIST"][$key]['CODE']]['VALUE'] = $val;
                }  
            }
            $id = '&ID='.$ID;

        }else{

            $countContactRequests = getContactRequest($arProperties[155]);
            $arFields['NAME'] = $arContact['IDENTIFICATOR']."/".($countContactRequests+1);

            if( $ID = $el->Add($arFields) ){
                $arResult["MESSAGE"] = getMessage('MSG_ADD_SUCCESS');
            }
            else{
                $arResult["ERRORS"] = $el->LAST_ERROR;
                foreach($arProperties as $key=> $val){
                    $arResult['ELEMENT']['PROPERTIES'][$arResult["PROPERTY_LIST"][$key]['CODE']]['ID'] = $key;
                    $arResult['ELEMENT']['PROPERTIES'][$arResult["PROPERTY_LIST"][$key]['CODE']]['VALUE_ENUM_ID'] = $val;
                    $arResult['ELEMENT']['PROPERTIES'][$arResult["PROPERTY_LIST"][$key]['CODE']]['VALUE'] = $val;
                }
            }

            $id = '';
            if($ID){$id = '&ID='.$ID;}
        }

        if($_REQUEST["btn_submit"] and empty($arResult["ERRORS"]) ){
            $returnUrl = '/realty/client-requests/?type='.$_GET['type'].'&request_list='.$_GET['page'];
            localRedirect($returnUrl);

        }else if( $_REQUEST["btn_apply"] and empty($arResult["ERRORS"]) ){
            localRedirect($APPLICATION->GetCurPageParam().$id);
        }
    }
    

    //access permissions
    if(intval($_GET['ID'])>0){
        $arAccess = setAccessControl($arResult, $arCurUser, $AccessDirectors, $AccessAdmins);
        $arResult['ACCESS'] = $arAccess['access'];
        $arResult['HIDE'] = $arAccess['hide'];
    }else{
        $arResult['ACCESS'] = 'GRANTED';
    }

    $restrictByTime = RestrictByTime();
    if( !empty($restrictByTime) ){
        $arResult['ACCESS'] = RestrictByTime();
    }

    $this->includeComponentTemplate();

}else{
    $APPLICATION->AuthForm('');
}
