<?
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

use \Bitrix\Main\Loader;
if(!Loader::includeModule('iblock')) {
    ShowError('MODULE IBLOCK NOT INSTALLED');
    return;
}

if(intval($arParams['IBLOCK_ID'])>0){
    $IBLOCK_ID = $arParams['IBLOCK_ID'];
}else{
    ShowError('Не правильный инфоблок, В параметрах компонента нужно настроить инфоблок');
    return;
}

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/jquery.fancybox.min.css');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.fancybox.min.js');

if( $USER->IsAuthorized() ){

    $arGroups = $USER->GetUserGroupArray();
    $arAdminGroups = $arParams['GROUPS_ADMINS'];
    $arDirectorsGroup = $arParams['GROUPS_DIRECTORS'];

    $isAdmins = array_intersect($arAdminGroups, $arGroups);
    $isDirector = array_intersect($arDirectorsGroup, $arGroups);

    $MatchedAddress = getMessage('MATCHED_WITH_ADDRESS');
    $MatchedRemoved = getMessage('MATCHED_IN_REMOVED');


    $arCurUser = getUser($USER->GetID());

    $skipFields = false;

    if( !empty($_GET['type']) ){
        $skipFields = $arParams['PROPERTY_'.strtoupper($_GET['type'])];
    }

    if(empty($isDirector)){
        $skipFields[] = 239;        
    }
    $skipFields[] = 325; // Улица для Веб STREETWEB

    // get iblock properties list
    $rsIBLockPropertyList = CIBlockProperty::GetList(array("sort"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID));

    while ($arProperty = $rsIBLockPropertyList->GetNext())
    {
        if( in_array($arProperty['ID'], $skipFields) ) continue;

        if ($arProperty["PROPERTY_TYPE"] == "L")
        {
            $rsPropertyEnum = CIBlockProperty::GetPropertyEnum($arProperty["ID"]);
            $arProperty["ENUM"] = array();
            while ($arPropertyEnum = $rsPropertyEnum->GetNext())
            {
                $arProperty["ENUM"][$arPropertyEnum["ID"]] = $arPropertyEnum;
            }
        }

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

        $arResult["PROPERTY_LIST"][$arProperty["ID"]] = $arProperty;
    }


    if(intval($_GET['ID'])>0 ){

        $arFilter['IBLOCK_ID']= $IBLOCK_ID;
        $arFilter["ID"] = $_GET['ID'];

        $dbResElement = \CIBlockElement::GetList(Array("ID"=>"ASC"), $arFilter, false, false,
            ['IBLOCK_ID', 'ACTIVE', 'ID', 'NAME', 'DETAIL_PICTURE', 'DATE_CREATE', 'DATE_ACTIVE_FROM', 'DATE_ACTIVE_TO', 'CREATED_BY', 'TIMESTAMP_X', 'PROPERTY_*']
        );

        if ($elOb = $dbResElement->GetNextElement())
        {
            $arElement = $elOb->GetFields();

            $arUser = getUser($arElement['CREATED_BY']);
            $arElement['USER'] = $arUser;

            $arAccess = setAccessControl($arElement, $arCurUser, $isDirector, $isAdmins);
            $arResult['ACCESS'] = $arAccess['access'];
            $arResult['HIDE'] = $arAccess['hide'];
            $arResult['RULE'] = $arAccess['rule'];
            $arResult['GROUPS'] = $arAccess['groups'];


            if($arElement['ACTIVE']=='N' and empty($isAdmins) ){
                $access = 'denied';
            }

            $resLocked = Lockedrealty::getByCode($arElement['NAME']);

            if (!empty($resLocked) and !$USER->isAdmin() ) {
                $arResult['ACCESS'] = 'VIEW';
            }

            $arResult['ELEMENT'] = $arElement;
            
            $detail_picture['ID'] = $arElement['DETAIL_PICTURE'];
            $detail_picture['SRC'] = CFile::GetPath($arElement['DETAIL_PICTURE']);
            $arResult['ELEMENT']['DETAIL_PICTURE'] = $detail_picture;

            $arProp = $elOb->GetProperties();

            if( !empty($arResult['HIDE']) and !empty($arProp['HOME']['VALUE']) ){
                $arProp['HOME']['VALUE'] = 'Информация скрыта';
            }
            if( !empty($arResult['HIDE']) and !empty($arProp['APARTMENT']['VALUE']) ){
                $arProp['APARTMENT']['VALUE'] = 'Информация скрыта';
            }

            if(!empty($arProp['MATCHED']['VALUE']) and empty($isAdmins)){
                $arResult['ACCESS']='VIEW';
            }

            $arResult['ELEMENT']['PROPERTIES'] = $arProp;
        }
    }


    if (check_bitrix_sessid() && (!empty($_POST["btn_submit"]) || !empty($_POST["btn_apply"])))
    {

        $id = '';
        $msg = '';
        $el = new CIBlockElement;

        $arFields = array();
        $arFields["IBLOCK_ID"] = $IBLOCK_ID;
        $arProperties = $_POST["PROPERTY"];
        $arFields['MODIFIED_BY'] = $USER->getID();
        $arProperties['90'] = str_replace(' ', '', $_POST["PROPERTY"][90]);


        //Свйоства OWNER_CODE - код контакта для фильтра по коду(Идентификатора контакта)
        if( intval($arProperties[88])>0 ){
            $arContact = getContact($arProperties[88]);
            if( $arContact['ID']>0 ){
                $arProperties['256'] = $arContact['IDENTIFICATOR'];
            }
        }

        //Автоматический стави галочку свойства D если заполнен Занята от и Занята до
        if( !empty($arProperties[143]) and !empty($arProperties[144])){
            $arProperties[147] = array(145);
        }else{
            $arProperties[147] = false;
        }

        /*** Если ID элемента есть то обновляем ***/
        if(intval($_POST['ID'])>0){

            $ID = $_POST['ID'];
            
            //Свойства CRON обнуляем если что то редактировали
            $arProperties['275'] = 1;

            if(intval($_POST['CREATED_BY'])>0){
                $arUser = getUser($_POST['CREATED_BY']);
                $arFields['CREATED_BY'] = $arUser['ID'];
                $department = $arUser['DEPARTMENT'];
                $arProperties['153'] = $department;
                $departmentID = $arUser['UF_DEPARTMENT'][0];
            }


            $arFields['PROPERTY_VALUES'] = $arProperties;

            // Обновляем дата активности 
            $arFields['DATE_ACTIVE_FROM'] = date('d.m.Y');
            $arFields['DATE_ACTIVE_TO'] = date('d.m.Y', strtotime( date('d.m.Y') ."+18 month") );


            $res = $el->Update($ID, $arFields);

            //upload documents file
            if( !empty($_FILES['CERTDOC']['name'][0]) ){                
                $arFiles = makeFilesArray($_FILES,'CERTDOC');
                CIBlockElement::SetPropertyValues($ID, REALTY_IBLOCK_ID, $arFiles, 'CERTDOC');
            }

            // update street for web
            if( !empty($arProperties[98]) ){
                $streetWebid = getStreetIDForWeb($arProperties[98]);
                if($streetWebid>0){
                    CIBlockElement::SetPropertyValuesEx($ID, REALTY_IBLOCK_ID, array('STREETWEB' =>$streetWebid));
                }               
            }

            $addedRealtyLink = '<a href="/realty/realtyinfo/?type='.$_GET['type'].'&ID='.$ID.'">'.$_POST['ident'].'</a> ';

            //после одобрения диспечера или директора, отпавляем опоавещение что проверено и одобрено
            if( empty($arProperties[239]) ){                
                sendMessageToChat($USER->GetID(), $arFields['CREATED_BY'], $addedRealtyLink.getMessage('MSG_VERIFAED'));    
            }

            
            // отпавляем опоавещение что совпадение проверено и одобрено
            if( empty($arProperties[301]) and $_REQUEST['matched']=='Y'){

                sendMessageToChat($USER->GetID(), $arFields['CREATED_BY'], $addedRealtyLink.getMessage('MSG_MATCHED'));
                
                $director = CIntranetUtils::GetDepartmentManagerID($departmentID);                
                $dispatcher = getDepartmentDispatcher($departmentID);

                sendMessageToChat($USER->getID(), $director, $addedRealtyLink.getMessage('MSG_MATCHED'));
                sendMessageToChat($USER->getID(), $dispatcher['UF_DISPECHER'], $addedRealtyLink.getMessage('MSG_MATCHED'));                             
            }

            if($res){
                
                $_SESSION['notifymsg'] = getMessage('MSG_EDIT_SUCCESS');
                $_SESSION['notifytype'] = "success";

            }else{

                $arResult["ERRORS"] = $el->LAST_ERROR;
                foreach($arProperties as $key=> $val){
                    $arResult['ELEMENT']['PROPERTIES'][$arResult["PROPERTY_LIST"][$key]['CODE']]['ID'] = $key;
                    $arResult['ELEMENT']['PROPERTIES'][$arResult["PROPERTY_LIST"][$key]['CODE']]['VALUE_ENUM_ID'] = $val;
                    $arResult['ELEMENT']['PROPERTIES'][$arResult["PROPERTY_LIST"][$key]['CODE']]['VALUE'] = $val;
                }
            }

        }else{
            /** Если ID элемента нет то добавляем **/

            if(intval($_POST['CREATED_BY'])>0){
                $arUser = getUser($_POST['CREATED_BY']);
                $arFields['CREATED_BY'] = $arUser['ID'];
                $DepartamnetID = $arUser['UF_DEPARTMENT'][0];
                $department = $arUser['DEPARTMENT'];
            }else{
                $arFields['CREATED_BY'] = $USER->GetID();
                $DepartamnetID = $arCurUser['UF_DEPARTMENT'][0];
                $department =  $arCurUser['DEPARTMENT'];
            }

            $arProperties['153'] = $department;

            // проверяем блокировку по этом адресу
            if( !empty($arProperties[96]) and !empty($arProperties[97]) and !empty($arProperties[98]) and !empty($arProperties[99])  ){

                $filter = array(
                    'ACTIVE'=>1, 'REGION'=>$arProperties[96], 'ZONE'=>$arProperties[97],'STREET'=>$arProperties[98], 'HOME'=>trim($arProperties[99]), 'APARTMENT'=>$arProperties[100]
                );
                $lockedAddress = Lockedaddress::getList($filter);

                //если пользовтель или директор иил диспечер другой то ввовдим ошибку
                if( !empty($lockedAddress) ){

                    $lockedUserDepartment = getUser($lockedAddress[0]['USER_ID']);

                    if( !empty($isDirector) and $lockedUserDepartment['DEPARTMENT']!==$department ){
                        $arResult["ERRORS"] = getMessage('ADDRESS_IS_BLOCKED').$lockedAddress[0]['USER_NAME'];
                    }else if( empty($isDirector) and $lockedAddress[0]['USER_ID']!== $USER->GetID()){
                        $arResult["ERRORS"] = getMessage('ADDRESS_IS_BLOCKED').$lockedAddress[0]['USER_NAME'];
                    }
                }
            }


            if( empty($arResult["ERRORS"]) ){

                // Проверяем если ли такой недвижимость по этому адресу
                if(
                    !empty($arProperties[92]) and !empty($arProperties[96]) and !empty($arProperties[97]) and !empty($arProperties[98]) and !empty($arProperties[99])
                ){
                    $Filter['IBLOCK_ID']= $IBLOCK_ID;
                    $Filter['PROPERTY_92'] = $arProperties[92]; //Тип сделки
                    $Filter['PROPERTY_96'] = $arProperties[96]; //Регион
                    //$Filter['PROPERTY_97'] = $arProperties[97]; //Зона
                    $Filter['PROPERTY_98'] = $arProperties[98]; //Улица
                    $Filter['PROPERTY_99'] = trim($arProperties[99]);  //Дом

                    //Квартира
                    if( !empty($arProperties[100]) ){
                        $Filter['PROPERTY_100'] = trim($arProperties[100]);
                    }

                    $resRealty = getRealty($Filter);

                    //если есть совпадение с адресом
                    if( !empty($resRealty) and $resRealty['ACTIVE']=='Y' and $arProperties[93]==$resRealty['PROPERTY_93_ENUM_ID'] ){
                        $dublicate['msg'] = $resRealty['NAME'].' '. getMessage('ALREADY_EXIST');
                        $arProperties[301]=230; //свойтва совпадение
                        $dublicate['type']='active';
                    }

                    //если есть совпадение с адресом, но тип недвижимость другой
                    if( !empty($resRealty) and $resRealty['PROPERTY_93_ENUM_ID']!==$arProperties[93]){
                        $arProperties[301]=230; //свойтва совпадение
                        $dublicate['msg'] = $resRealty['NAME'].' '. getMessage('ALREADY_EXIST');
                        $dublicate['type']='active';
                    }

                    //если есть совпадение с удаленной недвижитью
                    if( !empty($resRealty) and $resRealty['ACTIVE'] == 'N'){
                        $arProperties[301]=230; //свойтва совпадение
                        $dublicate['msg'] = $resRealty['NAME'].' '. getMessage('ALREADY_EXIST');
                        $dublicate['type'] = 'removed';
                    }
                }


                $lastRealtyId = getRealtyCount()+1;
                $prefix = getRealtyPrefix($arProperties);

                $arFields['DATE_ACTIVE_FROM'] = date('d.m.Y');
                $arFields['DATE_ACTIVE_TO'] = date('d.m.Y', strtotime( date('d.m.Y') ."+18 month") );
                $arFields['NAME'] = 'N'.$department.'+'.$prefix.$lastRealtyId;

                //устанавливаем свойства поумолчанию Не проверен если, это не дириектор или не диспечер;
                if( empty($isDirector) ){
                    $arProperties[239]=207;
                }

                $arFields["PROPERTY_VALUES"] = $arProperties;

                //если не привязан контакт(Владелец) свойстава OWNERID
                if( empty($arProperties[88]) and !empty($arProperties[305])){
                    $arFields["PROPERTY_VALUES"][302]=231;
                    $noContact = 'Y';
                }

                if( $ID = $el->Add($arFields) ){

                    if( !empty($_FILES['CERTDOC']) ){
                        $arFiles = makeFilesArray($_FILES,'CERTDOC');
                        CIBlockElement::SetPropertyValuesEx($ID, REALTY_IBLOCK_ID, array('CERTDOC'=>$arFiles));
                    }

                    // update street for web
                    if( !empty($arProperties[98]) ){
                        $streetWebid = getStreetIDForWeb($arProperties[98]);
                        if($streetWebid>0){
                            CIBlockElement::SetPropertyValuesEx($ID, REALTY_IBLOCK_ID, array('STREETWEB' =>$streetWebid));
                        }               
                    }

                    $realtyLink = ' <a target="_blank" href="/realty/realtyinfo/?type='.$arProperties[93].'&ID='.$ID.'">'.$arFields['NAME'].'</a>';

                    $director = CIntranetUtils::GetDepartmentManagerID($DepartamnetID);
                    $department = getDepartmentDispatcher($DepartamnetID);

                    // send to user
                    sendMessageToChat($USER->GetID(), $USER->GetID(), getMessage('ADDED_NEW_REALTY').$realtyLink);
                    
                    //send to director
                    sendMessageToChat($USER->GetID(), $director, getMessage('ADDED_NEW_REALTY').$realtyLink);
                    // send to dispatcher
                    sendMessageToChat($USER->GetID(), $department['UF_DISPECHER'], getMessage('ADDED_NEW_REALTY').$realtyLink);
                    

                    if( !empty($noContact) ){
                        sendMessageToChat($USER->getID(), INFO_MANAGER, getMessage('NOTCONTACT').$realtyLink);
                        sendMessageToChat($USER->getID(), INFO_MANAGER2, getMessage('NOTCONTACT').$realtyLink);
                    }


                    // dublicate width removed
                    if( !empty($dublicate) and $dublicate['type']=='removed'){

                        $MatchedRemoved.=' удаленный <a target="_blank" href="/realty/realtyinfo/?type='.$_GET['type'].'&ID='.$resRealty['ID'].'">'.$resRealty['NAME'].'</a>';
                        $MatchedRemoved.=' новый '.$realtyLink;

                        //send to managers
                        sendMessageToChat($USER->getID(), INFO_MANAGER, $MatchedRemoved);
                        sendMessageToChat($USER->getID(), INFO_MANAGER2, $MatchedRemoved);

                        //send to user realtor
                        sendMessageToChat($USER->getID(), $arFields['CREATED_BY'], $MatchedRemoved);
                        
                        //send to director
                        sendMessageToChat($USER->getID(), $director, $MatchedRemoved);
                        // send to dispatcher
                        sendMessageToChat($USER->getID(), $department['UF_DISPECHER'], $MatchedRemoved);
                    }


                    // dublicate width active
                    if( !empty($dublicate) and $dublicate['type']=='active' ){

                        $MatchedAddress.=' новый '.$realtyLink;
                        $MatchedAddress.=' Совпадение c <a target="_blank" href="/realty/realtyinfo/?type='.$resRealty['PROPERTY_93_ENUM_ID'].'&ID='.$resRealty['ID'].'">'.$resRealty['NAME'].'</a>';

                        sendMessageToChat($USER->getID(), INFO_MANAGER, $MatchedAddress);
                        sendMessageToChat($USER->getID(), INFO_MANAGER2, $MatchedAddress);    
                        sendMessageToChat($USER->getID(), $USER->getID(), $MatchedAddress);                    
                        sendMessageToChat($USER->getID(), $director, $MatchedAddress);
                        sendMessageToChat($USER->getID(), $department['UF_DISPECHER'], $MatchedAddress);
                    }
                    

                    if($ID){$id = '&ID='.$ID; $_SESSION['notifymsg'] = getMessage('MSG_ADD_SUCCESS'); $_SESSION['notifytype'] = "success";  }

                }else{ $arResult["ERRORS"] = $el->LAST_ERROR; }
            }


            if( !empty($arResult["ERRORS"]) ){

                foreach($arProperties as $key=> $val){
                    $arResult['ELEMENT']['PROPERTIES'][$arResult["PROPERTY_LIST"][$key]['CODE']]['ID'] = $key;
                    $arResult['ELEMENT']['PROPERTIES'][$arResult["PROPERTY_LIST"][$key]['CODE']]['VALUE_ENUM_ID'] = $val;
                    $arResult['ELEMENT']['PROPERTIES'][$arResult["PROPERTY_LIST"][$key]['CODE']]['VALUE'] = $val;
                }
            }
        }

        
        if(isset($_POST["btn_submit"]) and empty($arResult["ERRORS"]) ){

            if( !empty($_SESSION['BACK_URL']) ){
                localRedirect($_SESSION['BACK_URL']);
            }else{
                localRedirect('/realty/list/');
            }        

        }else if(isset( $_POST["btn_apply"]) and empty($arResult["ERRORS"]) ){

            localRedirect($APPLICATION->GetCurPageParam().$id.$msg);
        }
    }


    if( empty($_GET['ID']) ){
        $arResult['ACCESS'] = 'GRANTED';
    }

    // $restrictByTime = RestrictByTime();
    // if( !empty($restrictByTime) ){
    //     $arResult['ACCESS'] = RestrictByTime();
    // }

    if(empty($access)){
        $this->includeComponentTemplate();
    }else{
        echo '<p class="access-denied">'.getMessage('ACCESS_DENIED').'</p>';
    }

}else{
    $APPLICATION->AuthForm('');
}