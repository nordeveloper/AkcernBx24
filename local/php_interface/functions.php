<?php
function dump($var){
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

function dd($var){
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    die();
}


function DispatcherLeadMsg($leadID, $title){
    echo 'Ձեր տնօրենին նշանակվել է Lead: <a href="/crm/lead/details/'.$leadID.'/">'.$title.'</a>';
}

function DirectorLeadMsg($leadID, $title){
    echo 'Դուք նշանակվել եք Lead: <a href="/crm/lead/details/'.$leadID.'/">'.$title.'</a> պատասխանատու';
}


function ElementFilter($filterData, $arFilter){

    foreach ($filterData as $fieldId => $fieldValue)
    {

        if ((is_array($fieldValue) && empty($fieldValue)) || (is_string($fieldValue) && strlen($fieldValue) <= 0))
        { continue; }

        if(is_string($fieldValue)){
            $fieldValue = trim($fieldValue);
        }

        if (substr($fieldId, -5) == "_from")
        {
            $realFieldId = substr($fieldId, 0, strlen($fieldId)-5);
            if (substr($realFieldId, -2) == "_1")
            {
                $arFilter[$realFieldId] = $fieldValue;
            }
            else
            {
                if (!empty($filterData[$realFieldId."_numsel"]) && $filterData[$realFieldId."_numsel"] == "more")
                    $filterPrefix = ">";
                else
                    $filterPrefix = ">=";
                $arFilter[$filterPrefix.$realFieldId] = trim($fieldValue);
            }
        }
        elseif (substr($fieldId, -3) == "_to")
        {

            $realFieldId = substr($fieldId, 0, strlen($fieldId)-3);
            if (substr($realFieldId, -2) == "_1")
            {
                $realFieldId = substr($realFieldId, 0, strlen($realFieldId)-2);
                $arFilter[$realFieldId."_2"] = $fieldValue;
            }
            else
            {
                if (!empty($filterData[$realFieldId."_numsel"]) && $filterData[$realFieldId."_numsel"] == "less")
                    $filterPrefix = "<";
                else
                    $filterPrefix = "<=";
                $arFilter[$filterPrefix.$realFieldId] = trim($fieldValue);
            }
        }
        else
        {
            if($fieldId=='FIND'){
                $fieldId = '%NAME';
            }

            $arFilter[$fieldId] = $fieldValue;
        }
    }
    //unset($arFilter['CHECK']);
    return $arFilter;
}


function RestrictByTime(){
    global $USER;
    if($USER->isAdmin()){
        $acces = 'GRANTED';
    }else
    if( (date('H')<10 or date('H')>19) ){
        $acces = 'VIEW';
    }

    return $acces;
}

// access permissions
function setAccessControl($arResult, $arCurUser, $idDirectors, $isAdmins){

    global $USER;
    $arAccess['access'] = 'VIEW'; //only view

    if( $arResult['CREATED_BY']!=$USER->getID() ){
        $arAccess['access'] = 'VIEW';
        $arAccess['hide'] = 'HIDE';
    }

    if( $arResult['CREATED_BY']==$USER->getID() ){
        $arAccess['rule'] = 'ADDPHOTO';
    }

    if (!empty($idDirectors) and ($arCurUser['DEPARTMENT'] == $arResult['USER']['DEPARTMENT']) ) {
        $arAccess['access'] = 'GRANTED';
        $arAccess['hide'] = false;
        $arAccess['rule'] = 'ADDPHOTO';
    }

    if( !empty($arResult['DATE_ACTIVE_TO']) and strtotime($arResult['DATE_ACTIVE_TO']) < strtotime(date('Y-m-d H:i:s')) ){
        $arAccess['access'] = 'VIEW';
    }

    if( !empty($isAdmins) ){
        $arAccess['access'] = 'GRANTED';
        $arAccess['hide'] = false;
        $arAccess['groups'] =  'ADMINS';
    }

    return $arAccess;
}


function FloorList(){
    return array('Н'=>'Н','М'=>'М','Б'=>'Б', 'П'=>'П', 'Пп'=>'Пп',
        1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8,
        9=>9, 10=>10, 11=>11, 12=>12, 13=>13, 14=>14, 
        15=>15, 16=>16, 17=>17, 18=>18, 19=>19, 20=>20, 21=>21, 22=>22, 23=>23, 24=>24, 25=>25, 26=>26);
}


function FloorsList(){
    return array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,
        11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19,
        20=>20,21=>21,22=>22,23=>23,24=>24,25=>25,26=>26);
}


function getRealtyPrefix($data){
    $prefix = '';

    if($data[93]==72 and $data[102]>0 and $data[102]<5){
        $prefix = $data[102]; // до 5 комнатные
    }

    if($data[299]>0){
        $prefix = 8; //не стандартный
    }

    if($data[93]>72 and $data[93]<76){
        $prefix = 5; // дом особняк дача
    }

    if($data[93]==76){
        $prefix = 7; // офис
    }

    if($data[93]>76 and $data[93]<79){
        $prefix = 9; // магазин, Ресторан
    }

    if( !empty($data[316]) ){
        $prefix = 7; //Другой вход
    }

    if($data[93]==80){
        $prefix = 6; // земля
    }

    if($data[93]>80 and $data[93]<84){
        $prefix = 9;
    }

    if($data[93]==225){ //Другое
        $prefix = 9;
    }

    //если офис
    if($data[93]==76){
        if(!empty($data[130]) or !empty($data[131]) ){
            $prefix = 9;  //вход или выход с улицы
        }
    }

    return $prefix;
}


function GenerateNumeration($type){
    global  $DB;
    $arFields = array(
        "TYPE"=>"'".$type."'"
    );
    $res = $DB->Insert('st_numeration', $arFields);
    return $res;
}


function getUser($id){

    if(intval($id)>0){
        $rsUser = CUser::GetByID($id);
        $arUser = $rsUser->Fetch();
        unset($arUser['PASSWORD'], $arUser['CHECKWORD']);
        $arUser['FULL_NAME'] = $arUser['NAME'].' '.$arUser['LAST_NAME'];

        $resDep = CIBlockSection::GetByID($arUser['UF_DEPARTMENT'][0]);
        $arDep = $resDep->GetNext();
        $arUser['DEPARTMENT'] = $arDep['XML_ID'];
        $arUser['DEPARTMENT_NAME'] = $arDep['NAME'];
        return $arUser;
    }
}


function getUsers(){
    $filter['!EXTERNAL_AUTH_ID']='imconnector';
    $filter['UF_INFILTER'] = 1;

    $rsUsers = CUser::GetList(($by="ID"), ($order="asc"), $filter, array("SELECT"=>array("UF_*")) );
    $is_filtered = $rsUsers->is_filtered;
    $items = array();
    while ($row = $rsUsers->fetch()){
        $items[] = $row;
    }
    return $items;
}


function getDepartment($id){
    if($id>0){
        $resDep = CIBlockSection::GetByID($id);
        if($arDep = $resDep->GetNext()){
            return $arDep;
        }
    }
}


function getDepartmentDispatcher($id){

    $rsSect = CIBlockSection::GetList(
        array('ID'=>'ASC'),
        array('IBLOCK_ID'=>5, 'ID'=>$id),
        false,
        array('ID', 'NAME', "UF_DISPECHER"),
        1
    );

    if($res = $rsSect->fetch()){
        return $res;
    }
    
}


function getDepartamentList($filter=array(), $orderby=false, $TopCount=false){

    $arFilter['IBLOCK_ID'] = DEPARTMENT_IBLOCK_ID;
    $arFilter['SECTION_ID'] = 4;
    if( !empty($filter) and is_array($filter) ){
        foreach ($filter as $k=> $f){
            $arFilter[$k] = $f;
        }
    }

    if($TopCount>0){
        $nTopCount = array('nTopCount'=>$TopCount);
    }

    if( !empty($orderby) and is_array($orderby) ){
        foreach ($orderby as $k=> $f){
            $arOrder[$k] = $f;
        }
    }else{
        $arOrder = array("XML_ID"=>'ASC');
    }

    $rsSect = CIBlockSection::GetList($arOrder, $arFilter, false,
        array('ID', 'NAME', 'XML_ID', "UF_HEAD", "UF_DISPECHER", "UF_REALTYCYCLE_1", "UF_REALTYCYCLE_1", "UF_REQUESTSCYCLE_1", "UF_REQUESTSCYCLE_2", "UF_COLOR"),
        $nTopCount
    );

    while ($arSect = $rsSect->GetNext())
    {
        $arDeps[$arSect['XML_ID']] =  $arSect;
    }

    return $arDeps;
}


function RemoveElement($ELEMENT_ID, $IBLOCK_ID){
    global $DB;
    global $APPLICATION;
    if ( intval($ELEMENT_ID)>0 and CIBlock::GetPermission($IBLOCK_ID) >= 'W') {
        $DB->StartTransaction();
        if (!CIBlockElement::Delete($ELEMENT_ID)) {
            $strWarning .= 'Error!';
            $DB->Rollback();
        } else{
            $DB->Commit();
            return 'Success';
        }
    }
}


function sendMessageToChat($fromUserId, $toUserId, $message)
{
    if (! \Bitrix\Main\Loader::includeModule('im')) {
        throw new \Bitrix\Main\LoaderException('Unable to load IM Chat module');
    }

    if( $fromUserId>0 and $toUserId>0 and !empty($message) ){

        $fields = [
            "TO_USER_ID" => $toUserId, // ID пользователя
            "FROM_USER_ID" => $fromUserId,  // От кого (0 - системное)
            "MESSAGE_TYPE" => "S",
            "NOTIFY_MODULE" => "im",
            "NOTIFY_MESSAGE" => $message, // Текст сообщения
        ];
    
        $msg = new \CIMMessenger();
        $msg->Add($fields);
        
        //сообщение об ошибке
        // if (!$rs = $msg->Add($fields)) {
        //     $e = $GLOBALS['APPLICATION']->GetException();
        //     throw new \Bitrix\Main\SystemException($e->GetString());
        // }else{
        //    return $rs; 
        // }        
    }

}


function getContact($ID){

    if (!CModule::IncludeModule('crm')) {
        return false;
    }

    if(intval($ID)>0){

        $dbRes = CCrmContact::getList(array(), array('=ID' => $ID),
            array(
                'ID', 'FULL_NAME',  'NAME', 'SECOND_NAME', 'LAST_NAME', 'ASSIGNED_BY',
                'UF_CRM_1593888387', 'UF_CRM_1593937101', 'UF_CRM_1593900593', 'UF_CRM_1599808888', 'UF_CRM_1593888851','UF_CRM_1593888917'
            )
        );
        $recCont = $dbRes->Fetch();

        if($recCont['ID']>0){

            $dbResMultiFields = CCrmFieldMulti::GetList( array(), array('ENTITY_ID' => 'CONTACT', 'ELEMENT_ID' => $ID) );

            while($arMultiFields = $dbResMultiFields->Fetch())
            {
                $arMulti[$arMultiFields['TYPE_ID']] = $arMultiFields['VALUE'];
            }

            $arContactFields['ID'] = $recCont['ID'];
            $arContactFields['FULL_NAME'] = $recCont['FULL_NAME'];
            $arContactFields['CONTACT'] = $arMulti;
            $arContactFields['IDENTIFICATOR'] = $recCont['UF_CRM_1593888387'];
            $arContactFields['X'] = $recCont['UF_CRM_1593937101'];
            $arContactFields['ACTIVE'] = $recCont['UF_CRM_1593900593'];
            $arContactFields['RATING'] = $recCont['UF_CRM_1599808888'];
            $arContactFields['VISIT_DATE'] = $recCont['UF_CRM_1593888851'];
            $arContactFields['VISITS_COUNT'] = $recCont['UF_CRM_1593888917'];
            $arContactFields['ASSIGNED_BY'] = $recCont['ASSIGNED_BY'];
            $arContactFields['ASSIGNED_BY_LOGIN'] = $recCont['ASSIGNED_BY_LOGIN'];
            $arContactFields['ASSIGNED_BY_NAME'] = $recCont['ASSIGNED_BY_NAME'];
            $arContactFields['ASSIGNED_BY_LAST_NAME'] = $recCont['ASSIGNED_BY_LAST_NAME'];
            $arContactFields['ASSIGNED_BY_SECOND_NAME'] = $recCont['ASSIGNED_BY_SECOND_NAME'];

            return $arContactFields;
        }
    }
}


function getContacts($q=false, $user_id=false){

    if (!CModule::IncludeModule('crm')) {
        return false;
    }

    $arFilter = array('ENTITY_ID' => 'CONTACT', 'TYPE_ID'=>'PHONE', '%VALUE'=>$q);

    $dbResMultiFields = CCrmFieldMulti::GetListEx(array(), $arFilter, false, array('nTopCount'=>30) );

    $arMulti = array();
    while($arMultiFields = $dbResMultiFields->Fetch() ) {
        $arMulti[$arMultiFields['ID']]['CONTACT_ID'] = $arMultiFields['ELEMENT_ID'];
        $arMulti[$arMultiFields['ID']]['PHONE'] = $arMultiFields['VALUE'];
        $dbRes = CCrmContact::getList(array(), array('ID'=>$arMultiFields['ELEMENT_ID']), array('ID', 'NAME', 'LAST_NAME', 'FULL_NAME'), 1);
        if( $rowCont = $dbRes->Fetch() ){
            $arMulti[$arMultiFields['ID']]['FULL_NAME'] = $rowCont['NAME'].' '.$rowCont['LAST_NAME'];
        }
    }

    return $arMulti;
}


function getPhoneDuplicate($q){

    if (!CModule::IncludeModule('crm')) {
        return false;
    }

    $arFilter = array('ENTITY_ID' => 'CONTACT', 'TYPE_ID'=>'PHONE', 'VALUE'=>$q);

    $dbResMultiFields = CCrmFieldMulti::GetListEx(array(), $arFilter, false, false );

    $arMulti = array();
    while($arMultiFields = $dbResMultiFields->Fetch() ) {
        $arMulti[$arMultiFields['ID']]['CONTACT_ID'] = $arMultiFields['ELEMENT_ID'];
        $arMulti[$arMultiFields['ID']]['PHONE'] = $arMultiFields['VALUE'];
        $dbRes = CCrmContact::getList(array(), array('ID'=>$arMultiFields['ELEMENT_ID']), array('ID', 'NAME', 'LAST_NAME', 'FULL_NAME'), 1);
        if( $rowCont = $dbRes->Fetch() ){
            $arMulti[$arMultiFields['ID']]['FULL_NAME'] = $rowCont['NAME'].' '.$rowCont['LAST_NAME'];
        }
    }

    return $arMulti;
}


function getContactByName($name){

    if(\Bitrix\Main\Loader::IncludeModule('crm')){
        global $DB;
        if(!empty($name)){
            $sql = "SELECT ID, NAME, ASSIGNED_BY_ID, CREATED_BY_ID FROM b_crm_contact WHERE NAME='".$DB->forSQL($name)."'";
            $dbRes = $DB->Query($sql);

            if( $rowCont = $dbRes->Fetch() ){
                $contID = $rowCont['ID'];
                $dbUfRes = $DB->Query("SELECT * FROM b_uts_crm_contact WHERE VALUE_ID='$contID'");

                if( $UFContIdent = $dbUfRes->Fetch() ){
                    $rowCont['IDENTIFICATOR'] = $UFContIdent['UF_CRM_1593888387'];
                }
                return $rowCont;
            }
        }
    }
}


function getContactByIdent($q){

    $res = array();
    $dbRes = CCrmContact::getList(array(), array("%UF_CRM_1593888387"=>$q));
    while( $rowCont = $dbRes->Fetch() ){
        $res[]=$rowCont;
    }
    return $res;
}



function getContactByIdentificator($ident){

    if(\Bitrix\Main\Loader::IncludeModule('crm')){
        global $DB;
        if(!empty($ident)){
            $dbRes = $DB->Query("SELECT * FROM b_uts_crm_contact WHERE UF_CRM_1593888387='$ident'");

            if( $rowCont = $dbRes->Fetch() ){
                return $rowCont;
            }
        }
    }
}


function getRealtyCount($arFilter=array()){

    $arFilter['IBLOCK_ID'] = REALTY_IBLOCK_ID;
    $arFilter['SHOW_NEW'] = 'Y';

    $res = \CIBlockElement::GetList(array("ID" => "ASC"), $arFilter, false, false,
        ['IBLOCK_ID', 'ID']
    );

    return $res->selectedRowsCount();
}


function getRequestCount($arFilter=array()){

    $arFilter['IBLOCK_ID'] = CLIENT_REQUEST_IBLOCKID;
    $arFilter['SHOW_NEW'] = 'Y';

    $res = \CIBlockElement::GetList(array("ID" => "ASC"), $arFilter, false, false,
        ['IBLOCK_ID', 'ID']
    );
    return $res->selectedRowsCount();
}


function getContactRequest($clientID){
    if( $clientID>0 ){
        $arFilter['PROPERTY_CLIENTID'] = $clientID;
        $arFilter['IBLOCK_ID'] = CLIENT_REQUEST_IBLOCKID;
        $arFilter['SHOW_NEW'] = 'Y';
        $dbres = CIBlockElement::GetList(array("ID"=>"DESC"), $arFilter, false, false,
            ['IBLOCK_ID', 'ID', 'PROPERTY_CLIENTID']
        );
        return $dbres->selectedRowsCount();
    }
}


// for main.ui.filter
function regionsList(){
    $regionList  = array();
    foreach (Region::getList() as $arRegion){
        $regionList[$arRegion['id']] = $arRegion['name_'.LANGUAGE_ID];
    }
    return $regionList;
}

// for main.ui.filter
function cityList(){
    $regionList  = array();
    foreach (City::getList() as $arRegion){
        $regionList[$arRegion['id']] = $arRegion['name_'.LANGUAGE_ID];
    }
    return $regionList;
}

// for main.ui.filter
function zonesList(){
    $zoneList = array();
    $zones = Zone::getList();
    foreach ($zones as $arZone){
        $zoneList[$arZone['code']] = $arZone['code'];
    }
    return $zoneList;
}


// for main.ui.filter
function StreetsList(){
    $streetList = array();
    $streets = Street::getList();
    foreach ($streets as $arStreet){
        $streetList[$arStreet['id']] = $arStreet['name_'.LANGUAGE_ID];
    }
    return $streetList;
}


function makeFilesArray($requestFiles,$inpname){

    $arFiles = array();
    for($i = 0; $i < count($requestFiles[$inpname]['name']); $i++){
        $file = Array('name' => $requestFiles[$inpname]['name'][$i],
            'size' => $requestFiles[$inpname]['size'][$i],
            'tmp_name' => $requestFiles[$inpname]['tmp_name'][$i],
            'type' => $requestFiles[$inpname]['type'][$i]
        );
        $arFiles[] = array('VALUE' => $file, 'DESCRIPTION' => $i);
    }
    return $arFiles;
}


function restCommand($url,$Data)
{
    $arData = http_build_query($Data);
    $headers = array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.8) Gecko/20061025 Firefox/1.5.0.8");

    $curl = curl_init($url);

    curl_setopt_array($curl, array(
        CURLOPT_POST           => 1,
//        CURLOPT_HEADER         => 1,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION  =>1,
        CURLOPT_URL            => $url,
        CURLOPT_POSTFIELDS     => $arData,
    ));

    $response['result'] = curl_exec($curl);

    if (curl_errno($curl) > 0) {
        $response['error'] = curl_error($curl);
        $response['info'] = curl_getinfo($curl);
    }else{
        $response['info'] = curl_getinfo($curl);
    }

    curl_close($curl);
    return $response; 
}



function ExportRealtyCsv($selectedColumns, $columns, $arData){

    foreach ($columns as $colName){
        if($colName['id']=='DETAIL_PICTURE') continue;

        if(in_array($colName['id'], $selectedColumns)){
            $colsHead[$colName['id']]= $colName['name'];
        }
    }

    $expStr = "\xEF\xBB\xBF".implode(';',$colsHead)."\n";

    foreach ($arData as $expData){
        unset($expData['actions']);
        foreach ($expData['data'] as $ckey=> $expCol){
            if(in_array($ckey, $selectedColumns) ){
                $expRow[$ckey]= " ".$expCol." ";
            }
        }
        $expStr.= '"'.implode('";"', $expRow).'"'."\n";
    }

    global $APPLICATION;
    $APPLICATION->restartBuffer();

    echo $expStr;
    Header('Content-Type: text/csv');
    Header('Content-Disposition: attachment;filename=realty.csv');
    Header('Content-Type: application/octet-stream');
    Header('Content-Transfer-Encoding: binary');
    die();
}


function getRealty($Filter=array()){

    $dbRes = \CIBlockElement::GetList(Array("ID"=>"ASC"), $Filter, false, false,
        array('IBLOCK_ID', 'ID', 'NAME','ACTIVE',
            "PROPERTY_92", "PROPERTY_93", "PROPERTY_96", "PROPERTY_97", "PROPERTY_98", "PROPERTY_99", "PROPERTY_100"
        )
    );

    if($row = $dbRes->fetch()){
        return $row;
    }
}



function PrintData($ids, $gridID, $iblockID){

    if(empty($ids) and empty($gridID) and empty($iblockID)) return;


	global $USER,$APPLICATION;

	$arGroups = $USER->GetUserGroupArray();

	$arGroupAdmins = array(1,13);
	$arGroupDirectors = array(10);

	$AdminAccess = array_intersect($arGroups, $arGroupAdmins);
	$DirectorAccess = array_intersect($arGroups, $arGroupDirectors);

	$arCurUser = getUser($USER->getID());


    $grid_options = new \Bitrix\Main\Grid\Options($gridID);
    $sort = $grid_options->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);

    $defaultFields = array('OWNERID', 'DEAL_TYPE', 'ZONE', 'STREET', 'ROOMS', 'FLOOR', 'HOME', 'APARTMENT', 'PRICE', 'TOTAL_AREA');
    $customFilterList = array('regions', 'city', 'zones', 'streets', 'floor', 'floors');


    $arFilter['IBLOCK_ID'] = $iblockID;
    $arFilter['ID'] = explode(',',$ids);


    //get data form Realty Iblock with filter;
    $res = \CIBlockElement::GetList($sort['sort'], $arFilter, false, false,
        ['IBLOCK_ID', 'ID', 'NAME', 'DATE_CREATE', 'CREATED_BY', 'TIMESTAMP_X', 'DATE_ACTIVE_TO', 'DETAIL_PICTURE', 'PROPERTY_*']
    );

    // $nav->setRecordCount($res->selectedRowsCount());

    if($iblockID==REALTY_IBLOCK_ID){

        while( $ob = $res->GetNextElement() ) {

            $arElement = $ob->GetFields();
            $arElement['PROPERTIES'] = $ob->GetProperties();
            $arElement['USER'] = getUser($arElement['CREATED_BY']);
    
   
            $arData['ID'] = $arElement['ID'];    

            $style = '';

            if( !empty($arElement['DATE_ACTIVE_TO']) and strtotime($arElement['DATE_ACTIVE_TO'])-strtotime(date('d.m.Y'))<0){
                // echo 'sadas';
                $style = 'style="background:#cecaca"';
                $arData['NAME'] = '<div '.$style.'>'.$arElement['NAME'].'</div>';
            }else{
                $arData['NAME'] = $arElement['NAME'];
            }            
            
            $arData['CREATED_BY'] = $arElement['USER']['FULL_NAME'];
    
            $arData['DATE_CREATE'] = $arElement['DATE_CREATE'];
            $arData['TIMESTAMP_X'] = $arElement['TIMESTAMP_X'];              
    
            foreach($arElement['PROPERTIES'] as $key=> $arProp){
    
                if($arProp['PROPERTY_TYPE']=='F') continue;
    
                if($arProp['CODE']=='REGION' and !empty($arProp['VALUE']) ){
                    $region = Region::getById($arProp['VALUE']);
                    $arProp['VALUE'] = $region['name_'.LANGUAGE_ID];
                }
    
                if($arProp['CODE']=='CITY' and !empty($arProp['VALUE']) ){
                    $region = City::getById($arProp['VALUE']);
                    $arProp['VALUE'] = $region['name_'.LANGUAGE_ID];
                }
    
                if($arProp['CODE']=='STREET' and !empty($arProp['VALUE']) ){
                    $street = Street::getByID($arProp['VALUE']);
                    $arProp['VALUE'] = $street['name_'.LANGUAGE_ID];
                }
    
                //change values label for current language
                if($arProp["PROPERTY_TYPE"]=='L' and LANGUAGE_ID=='en'){
                    $arProp['VALUE'] = $arProp['VALUE_XML_ID'];
                }       
    
                if( is_array($arProp['VALUE']) ){
                    $arProp['VALUE'] =  implode(', ', $arProp['VALUE']);            			
                }
    
                if( empty($AdminAccess) AND (empty($DirectorAccess) and $arElement['CREATED_BY']!=$USER->getID()
                    AND ($arProp['CODE']=='APARTMENT' or $arProp['CODE']=='HOME' or $arProp['CODE']=='PHONE'))
                    OR (($DirectorAccess AND $arElement['PROPERTIES']['DEPARTMENT']['VALUE'] != $arCurUser['DEPARTMENT'])
                        AND ($arProp['CODE']=='APARTMENT' or $arProp['CODE']=='HOME' or $arProp['CODE']=='PHONE')
                    )
                ){
                    $arProp['VALUE'] = 'Инфо скрыта';
                }
    
    
                $ownercode = false;
                if($key=='OWNER_CODE' and !empty($arProp['VALUE']) ){
                    $ownercode = $arProp['VALUE'];
                }
    
                if( $key=='OWNERID' and $arProp['VALUE']>0 ){
                    if(empty($ownercode)){
                        $arContact = getContact($arProp['VALUE']);
                        if( !empty($arContact['IDENTIFICATOR']) ) {$ownercode = $arContact['IDENTIFICATOR'];} else{$ownercode =$arProp['VALUE']; }
                    }
    
                    if( !empty($AdminAccess) and $_GET['EXPORT']!=="Y" ){
                        $arProp['VALUE'] = '<a class="contact-link" target="_blank" href="/crm/contact/details/'.$arProp['VALUE'].'/">'.$ownercode.'</a>';
                    }else{
                        $arProp['VALUE'] = $ownercode;
                    }
                }
    
                $arData[$key]= $arProp['VALUE'];
            }

    
            $arResult['GRID_DATA'][] = array('data'=>$arData, 'actions'=>'');
        }

    }else{

        while( $ob = $res->GetNextElement() ) {

            $arElement = $ob->GetFields();
            $arElement['PROPERTIES'] = $ob->GetProperties();
        
            $arElement['USER'] = getUser($arElement['CREATED_BY']);
        
            $arData['ID'] = $arElement['ID'];
        
            $arData['NAME'] = $arElement['NAME'];
            $arData['DATE_CREATE'] = $arElement['DATE_CREATE'];
            $arData['CREATED_BY'] = $arElement['USER']['FULL_NAME'];
            $arData['CREATED_BY'] = $arElement['USER']['FULL_NAME'];
            $arData['TIMESTAMP_X'] = $arElement['TIMESTAMP_X'];
        
            foreach($arElement['PROPERTIES'] as $key=> $arProp){
        
                if($arProp['PROPERTY_TYPE']=='F') continue;
        
                if( ($key=='CLIENT_CODE') and !empty($arProp['VALUE']) ){
                    if(!empty($AdminAccess)){
                        $arProp['VALUE'] = $arProp['VALUE'];
                    }
                }
        
                if( is_array($arProp['VALUE']) ){
                    $arProp['VALUE'] = implode(', ', $arProp['VALUE']);
                };
        
                if($arProp['CODE']=='REGION' and !empty($arProp['VALUE']) ){
                    $region = Region::getById($arProp['VALUE']);
                    $arProp['VALUE'] = $region['name_'.LANGUAGE_ID];
                }
        
                if($arProp['CODE']=='CITY' and !empty($arProp['VALUE']) ){
                    $region = City::getById($arProp['VALUE']);
                    $arProp['VALUE'] = $region['name_'.LANGUAGE_ID];
                }
        
                if($arProp['CODE']=='STREET' and !empty($arProp['VALUE']) ){
                    $street = Street::getByID($arProp['VALUE']);
                    $arProp['VALUE'] = $street['name_'.LANGUAGE_ID];
                }
        
                //change labels for current language
                if(LANGUAGE_ID=='en' and $arProp['PROPERTY_TYPE']=='L' ){
                    $arData[$key] = $arProp['VALUE_XML_ID'];
                }else{
                    $arData[$key]= $arProp['VALUE'];
                }        
                $arData[$key]= $arProp['VALUE'];                
            }
        
            $arResult['GRID_DATA'][] = array('data'=>$arData, 'actions'=>'');
        }
        
    }



    // making array for GRID columns
    $columns = [];
    $columns[] = ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => false];
    $columns[] = ['id' => 'NAME', 'name' => 'Идент', 'sort' =>'NAME', 'default' => true];
    $columns[] = ['id' => 'DATE_CREATE', 'name' => 'Дата', 'sort' =>'DATE_CREATE', 'default' => false];
    $columns[] = ['id' => 'CREATED_BY', 'name' => 'Риелтор', 'sort' =>'CREATED_BY', 'default' => true];
    $columns[] = ['id' => 'TIMESTAMP_X', 'name' => 'Обновления', 'sort' =>'TIMESTAMP_X', 'default' => false];

    $propertyList = array();

    /* get Property list for Filter and Grid Columns*/
    $filter = array('IBLOCK_ID' => $iblockID, "ACTIVE"=>"Y", "FILTRABLE"=>'Y');


    $resProps = Bitrix\Iblock\PropertyTable::getList(array(
        'filter' => $filter, 'order'=>array('SORT'=>'ASC')
    ));
    
    
    while ($arProperty = $resProps->Fetch())
    {
    
        $arProperty['id'] = 'PROPERTY_'.$arProperty['CODE'];
        if(LANGUAGE_ID=='en'){$arProperty['name'] = $arProperty['HINT'];} else {$arProperty['name'] = $arProperty['NAME'];}
    
        if($arProperty["PROPERTY_TYPE"]=="N") {$arProperty['type'] = 'number';}
        if($arProperty["PROPERTY_TYPE"]=="S") {$arProperty['type'] = 'string';}

    
        if ($arProperty["PROPERTY_TYPE"] == "L")
        {
            $rsPropertyEnum = CIBlockProperty::GetPropertyEnum($arProperty["ID"]);
            $arProperty["items"] = array();
            while ($arPropertyEnum = $rsPropertyEnum->GetNext())
            {
                if(LANGUAGE_ID=='en'){
                    $arProperty["items"][$arPropertyEnum["ID"]] = $arPropertyEnum['XML_ID'];
                }
                else{
                    $arProperty["items"][$arPropertyEnum["ID"]]=html_entity_decode($arPropertyEnum['VALUE']);
                }					
            }
    
            $arProperty['type'] = 'list';
        }
    
        $arUiFilter[$arProperty['ID']] = $arProperty;
        unset($arUiFilter[$arProperty['ID']]['OWNERID']);
    
        //custom fields
        if( in_array($arProperty["USER_TYPE"], $customFilterList) ){
            if($arProperty["USER_TYPE"]=='zones' OR $arProperty["USER_TYPE"]=='city' OR  $arProperty["USER_TYPE"]=='streets'){
                $arUiFilter[$arProperty['ID']] =
                    array(
                        'id'=>'PROPERTY_'.$arProperty['CODE'],
                        'type'=>'custom_entity',
                        'name'=>$arProperty['NAME'],
                        'params'=>['multiple' => 'Y'],
                        'default'=>true
                    );
            }else{
                $items = $arProperty["USER_TYPE"].'List';
                $arUiFilter[$arProperty['ID']] = [
                    'id'=>'PROPERTY_'.$arProperty['CODE'],
                    'name'=>$arProperty['NAME'],
                    'type'=>'list',
                    'items'=>$items(),
                    'params'=>['multiple' => 'Y'],
                ];
            }
        }
    
        $propertyList[] = $arProperty;
    }
    


    foreach ($propertyList as $arProp){
        $defField = false;
        if(in_array($arProp['CODE'], $defaultFields)) $defField = true;

        //change language labels for grid column names
        $label = false;
        if(LANGUAGE_ID=='en'){$label = $arProp['HINT'];} else {$label = $arProp['NAME'];}
        $columns[] = ['id' =>$arProp['CODE'], 'name' =>$label, 'sort' =>'PROPERTY_'.$arProp['CODE'], 'default'=>$defField];
    }


    $APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
        'GRID_ID' => $gridID,
        'COLUMNS' => $columns,
        'ROWS' => $arResult['GRID_DATA'],
        'SHOW_ROW_CHECKBOXES' => false,
        // 'NAV_OBJECT' => $nav,
        'AJAX_MODE' => 'N',
        // 'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
        // 'PAGE_SIZES' =>  [
        //     ['NAME' => '20', 'VALUE' => '20'],
        //     ['NAME' => '30', 'VALUE' => '30'],
        //     ['NAME' => '50', 'VALUE' => '50'],
        //     ['NAME' => '100', 'VALUE' => '100']
        // ],
        'AJAX_OPTION_JUMP'          => 'N',
        'SHOW_CHECK_ALL_CHECKBOXES' => false,
        'SHOW_ROW_ACTIONS_MENU'     => false,
        'SHOW_GRID_SETTINGS_MENU'   => false,
        'SHOW_NAVIGATION_PANEL'     => true,
        'SHOW_PAGINATION'           => false,
        'SHOW_SELECTED_COUNTER'     => false,
        'SHOW_TOTAL_COUNTER'        => true,
        'SHOW_PAGESIZE'             => true,
        'SHOW_ACTION_PANEL'         => false,
        'ALLOW_COLUMNS_SORT'        => false,
        'ALLOW_COLUMNS_RESIZE'      => false,
        'ALLOW_HORIZONTAL_SCROLL'   => false,
        'ALLOW_SORT'                => false,
        'ALLOW_PIN_HEADER'          => false,
        'AJAX_OPTION_HISTORY'       => 'N',
        'TOTAL_ROWS_COUNT'  =>$res->selectedRowsCount()
    ]);

    echo '<script>';
    echo 'javascript:window.print();';
    echo '</script>';

    /*
    if(!in_array('NAME',$selectedColumns) ){
        $arHaad[0] = 'Идентификатор';
    }

    foreach ($columns as $col){

        if($col['id']=='DETAIL_PICTURE'){
            unset($col);
        }

        if($col['id']=='ID'){
            unset($col);
        }

        if(in_array($col['id'],$selectedColumns)){
            $arHaad[$col['id']] = $col['name'];
        }
    }
    echo '<table class="table-print">';
    echo '<tr>';
    foreach ($arHaad as $hk=> $head){
        echo '<th><span class="print-col">'.$head.'</span></th>';
    }
    echo '</tr>';

    foreach ($arData as $arItem){
        echo '<tr>';
        echo '<td><span class="print-col">'.$arItem['NAME'].'</span></td>';

        if(in_array('DATE_CREATE',$selectedColumns)){
            echo '<td><span class="print-col">'.$arItem['DATE_CREATE'].'</span></td>';
        }

        if( in_array('CREATED_BY',$selectedColumns)){
            $fio = '';
            if($arItem['CREATED_BY']>0){
                $user = getUser($arItem['CREATED_BY']);
                $fio = $user['NAME'].' '.$user['LAST_NAME'];
            }
            echo '<td><span class="print-col">'.$fio.'</span></td>';
        }

        if(in_array('TIMESTAMP_X',$selectedColumns)){
            echo '<td><span class="print-col">'.$arItem['TIMESTAMP_X'].'</span></td>';
        }


        foreach ($arItem['PROPERTIES'] as $arProp){


            if( in_array($arProp['CODE'],$selectedColumns) ){

                if($arProp['CODE']=='REGION' and $arProp['VALUE']>0){
                    $region = Region::getByID($arProp['VALUE']);
                    $arProp['VALUE'] = $region['name_'.LANGUAGE_ID];
                }

                if($arProp['CODE']=='STREET' and $arProp['VALUE']>0){
                    $street = Street::getByID($arProp['VALUE']);
                    $arProp['VALUE'] = $street['name_'.LANGUAGE_ID];
                }

                if($arProp['CODE']=='OWNERID' and $arProp['VALUE']>0){
                    $contact = getContact($arProp['VALUE']);
                    $arProp['VALUE'] = $contact['IDENTIFICATOR'];
                }

//                dump($DirectorAccess);


                if( empty($AdminAccess) AND (empty($DirectorAccess) and $arItem['CREATED_BY']!=$USER->getID()
                        AND ($arProp['CODE']=='APARTMENT' or $arProp['CODE']=='HOME' or $arProp['CODE']=='PHONE'))
                    OR (($DirectorAccess AND $arItem['PROPERTIES']['DEPARTMENT']['VALUE'] != $arCurUser['DEPARTMENT'])
                        AND ($arProp['CODE']=='APARTMENT' or $arProp['CODE']=='HOME' or $arProp['CODE']=='PHONE')
                    )
                ){
                    $arProp['VALUE'] = 'Скрыто';
                }

                if( is_array($arProp['VALUE']) ){
                    echo '<td><span class="print-col">'.implode(',',$arProp['VALUE']).'</span></td>';
                }else{
                    echo '<td><span class="print-col">'.$arProp['VALUE'].'</span></td>';
                }
            }
        }
        echo '</tr>';
    }
    echo '</table>';
    */
}


function getPrintData($iblockid, $ids){

    $arFilter['IBLOCK_ID'] = $iblockid;
    $arFilter['ID'] = explode(',',$ids);

    $res = \CIBlockElement::GetList(array('ID'=>'asc'), $arFilter, false, false,
        ['IBLOCK_ID', 'ID', 'NAME', 'DATE_CREATE', 'CREATED_BY', 'TIMESTAMP_X', 'PROPERTY_*']
    );

    while( $ob = $res->GetNextElement() ) {

        $arElement = $ob->GetFields();
        $arElement['PROPERTIES'] = $ob->GetProperties();
        $arResult[] = $arElement;
    }

    return $arResult;
}


function getDisctictByZone($zone){
   global $DB;

   $zones = array(
       "1/1"=>1,
       "1/2"=>1,
       "1/3"=>1,
       "1/4"=>1,

       "2/1"=>2,
       "2/2"=>2,
       "2/3"=>2,

       "3/1"=>4,
       "3/2"=>3,

       "4/1"=>5,
       "4/2"=>5,
       "4/3"=>6,
       "4/4"=>6,

       "5/1"=>7,

       "6/1"=>8,
       "6/2"=>8,
       "6/3"=>8,
       "6/4"=>8,
       "6/5"=>8,

       "7/1"=>9,
       "7/2"=>9,
       "7/3"=>9,
       "7/4"=>10,
       "7/5"=>'',

       "8/1"=>11,
       "8/2"=>11,
       "8/3"=>11,
       "8/4"=>11,
       "8/5"=>12,
       "9/1"=>13,
       "9/2"=>13,
       "9/3"=>13,
       "9/4"=>13,
       "9/7"=>""
   );


   if($zones[$zone]){
        $sql = "SELECT * FROM st_zones_root WHERE id = '$zones[$zone]'";
        $res = $DB->Query($sql);
    
        if ($row = $res->fetch()){
            return $row['id'];
        }
   }
}


function addWatermark($imgPath, $watermarksrc, $newWidth=1000, $position=array(75, 50)){

    $imgData = exif_read_data($imgPath);

    $logoImage = imagecreatefrompng( $watermarksrc );

    switch($imgData['MimeType']) {
        case 'image/jpg':
            $ext = 'jpg';
            $image = imagecreatefromjpeg($imgPath);
        break;
        case 'image/jpeg':
            $image = imagecreatefromjpeg($imgPath);
            $ext = 'jpg';
        break;        
        case 'image/png':
            $image = imagecreatefrompng($imgPath);
            $ext = 'png';
        break;
    }

    if($imgData['Orientation']==6){
        $image = imagerotate($image,-90,0);
    }

    if( imagesx($image) > $newWidth){
        $img = imagescale($image, $newWidth, -1, IMG_NEAREST_NEIGHBOUR);
    }else{
        $img = $image;
    }

    imagealphablending( $logoImage, true );
    
    $imageWidth=imagesx($img);
    $imageHeight=imagesy($img); 
    $logoWidth=imagesx($logoImage);
    $logoHeight=imagesy($logoImage);
    
    imagecopy(
      $img,
      $logoImage,
      $imageWidth-$logoWidth-$position[0], $imageHeight-$logoHeight-$position[1],
      0, 0,
      $logoWidth, $logoHeight
    );

    imagecopy(
        $img,
        $logoImage,
        $imageWidth-$logoWidth-$imageWidth+150, $imageHeight-$logoHeight-$imageHeight+90,
        0, 0,
        $logoWidth, $logoHeight
    );
    
    $filePath = $_SERVER['DOCUMENT_ROOT'].'/upload/tmp/'.(time()+mt_rand()).'.'.$ext;
    
    switch($imgData['MimeType']) {
        case 'image/jpg':
            imagejpeg($img,  $filePath);
        break;
        case 'image/jpeg':
            imagejpeg($img,  $filePath);
        break;        
        case 'image/png':
            imagepng( $img,  $filePath);/* save image with watermark */
        break;
    }

    // Release memory
    @unlink($imgPath);
    imagedestroy( $img );
    return $filePath;
}


function getImageStatus($image_id){
    if($image_id>0){
        global $DB;
        $res  = $DB->Query("SELECT * FROM realty_images_hide WHERE IMAGE_ID='$image_id'");    
        if($row = $res->fetch()){
            return $row;
        }
    }
}


function HideImageFromSite($image_id, $realty_id){
    
    if($image_id>0 and $realty_id>0){
        global $DB;

        $arFields = array(
           "IMAGE_ID" => "'".intval($image_id)."'",
           "REALTY_ID" => "'".intval($realty_id)."'",
        );
    
        $ID = $DB->Insert("realty_images_hide", $arFields, $err_mess.__LINE__);
    
        return $ID;
    }

}

function ShowImageFromSite($image_id){

    if($image_id>0){
        global $DB;
        $res  = $DB->Query("DELETE FROM realty_images_hide WHERE IMAGE_ID='$image_id'");    
        return $res->result;
    }
}


function getStreetWeb($name){
    global $DB;

    $sql = "SELECT * FROM street_forweb WHERE name_ru like '%$name%' order by id asc";
    $dbres = $DB->query($sql);
    if($row = $dbres->fetch()){
        return $row;
    }
}



function getStreetIDForWeb($street_id){

    if( intval($street_id)>0 ){
    
        $street = Street::getById($street_id);   
        $arStreet = explode(' ', $street['name_ru']);
   
        if( in_array('переулок', $arStreet) ){
            $streetweb = getStreetWeb($arStreet[0]);
            return $streetweb['id'];
        }

        if( in_array('тупик', $arStreet) ){
            $streetweb = getStreetWeb($arStreet[0]);
            return $streetweb['id'];
        }

        if( in_array('проезд', $arStreet) ){
            $streetweb = getStreetWeb($arStreet[0]);
            return $streetweb['id'];
        }        
    }    
}
