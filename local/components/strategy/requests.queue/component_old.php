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

if (!CModule::IncludeModule("crm"))
    return;

if (!CModule::IncludeModule("bizProc"))
    return;

$arGroups = $USER->GetUserGroupArray();
$arAdminGroups = array(1,13);
$isAdmins = array_intersect($arAdminGroups, $arGroups);
$arResult['isAdmins'] = $isAdmins;

$Queue = new RDQueue('st_requests_queue');

// Катнора ID =60 или XML_ID = 5 не нужно для очереди;
$arResult['DEPARTMENTS']  = getDepartamentList(array("!XML_ID"=>5));


// get New Leads

if( intval($_GET['type']) >0){
    // get New Leads

    $type = $_GET['type'];

    // get Leads NEW
    //Передаем данные (Лиды) очереди в шаблон для левого блока
    $arFilter['ASSIGNED_BY_ID'] = QUEUE_ADMIN; //Отвественный
    $arFilter['STATUS_ID'] = 'NEW';
    $arFilter['UF_CRM_1580368062']= 'CLIENT'; //Тип лида клиент

    if($type==1){ $leadTypeID = 123; } //продажа;
    if($type==2){ $leadTypeID = 124; } //аренда;
    
    $arFilter['UF_CRM_1581403190'] = $leadTypeID;
    $arNewLeads = CCrmLead::getList(Array('DATE_CREATE' => 'ASC'), $arFilter);

    while ($arLead=$arNewLeads->fetch()){
        $arResult['NEW_LEADS'][] = $arLead;
    }

    $monthFilter = date('Y-m');
    if(!empty($_GET['month'])){ $monthFilter = $_GET['month'];}

    //Передаем данные очереди в шаблон
    $arResult['QUEUE'] = $Queue->getList($monthFilter, false, $type);
}



$obDep = new CIBlockSection;
$filter['!XML_ID'] = 5; // Катнора ID =60 или XML_ID = 5 не нужно для очереди;


if(intval($_POST['type'])>0 and  !empty($_POST['department']) and empty($_POST['leads']) ){

    $type = $_POST['type'];

    $QueueCycleCount = $Queue->QueueCycleCount(['TYPE'=>$type])+1;

    foreach($_POST['department'] as $depIdForSkipe){
    
        $depForSkipe = getDepartamentList( array('XML_ID'=>$depIdForSkipe) );

        $dataForSkipe = array(
            'DEPARTMENT'=>$depIdForSkipe, 'DEP_XMLID'=>$depForSkipe[$depIdForSkipe]['ID'], 'LEAD_ID'=>0, 'LEAD_NAME'=>'x', 'USER_ID'=>$depForSkipe[$depIdForSkipe]['UF_HEAD'], 'QUEUE'=>$QueueCycleCount, 'CYCLE'=>$QueueCycleCount, 'TYPE'=>$type
        );

        if($Queue->Add($dataForSkipe)){
            $obDep->Update($depForSkipe[$depIdForSkipe]['ID'], array("UF_REQUESTSCYCLE_".$type=>$QueueCycleCount) );
        }

    }
    
    localRedirect($APPLICATION->GetCurPageParam());
}





if( !empty($_POST['leads']) and  intval($_POST['type'])>0 ){

    $type = intval($_POST['type']);
    $crmlead = new CCrmLead();


    foreach ($_POST['leads'] as $leadID=> $leadTitle){
        
        $nextQueueCount = $Queue->QueueCycleCount(['TYPE'=>$type])+1;

        $arDep = getDepartamentList($filter, array("UF_REQUESTSCYCLE_".$type=>'ASC', "ID"=>'ASC'), 1);
        $arDep = current($arDep);   

        // $arUser = getUser($arDep['UF_HEAD']);

        $resLead = $crmlead->getByID($leadID);
        $data = array(
            'DEPARTMENT'=>$arDep['XML_ID'],
            'DEP_XMLID'=>$arDep['ID'],
            'LEAD_ID'=>$leadID,
            'LEAD_NAME'=>$leadTitle,
            'USER_ID'=>$arDep['UF_HEAD'],
            'QUEUE'=>$nextQueueCount,
            'CYCLE'=>$nextQueueCount,
            "TYPE"=>$type,
            'NAME'=>$resLead['NAME'],
            'COMMENT'=>$resLead['COMMENTS'],
            'OPPORTUNITY'=>$resLead['OPPORTUNITY'],
            'DATE_CREATE'=>$resLead['DATE_CREATE'],
        );
        $resAdd = $Queue->Add($data);


        if(!empty($resAdd)){

            $obDep->Update($depID, Array("UF_REQUESTSCYCLE_".$type=>$nextQueueCount));

            $fields['ASSIGNED_BY_ID'] = $arDep['UF_HEAD'];
            $fields['STATUS_ID'] = 'IN_PROCESS';
            $fields['UF_CRM_1594378997'] = 2; //Тип очереди Запрос
            $fields['UF_CRM_1595494850'] = $arDep['UF_DISPECHER']; //Dispatcher

            $resLUP = $crmlead->update($leadID, $fields);


            if($resLUP){
                $BPres = CBPDocument::StartWorkflow(
                    10,
                    array('crm', 'CCrmDocumentLead', 'LEAD_'.$leadID),
                    array(),
                    $arErrorsTmp
                );
            }

            if($arDep['UF_HEAD']>0)
                sendMessageToChat(QUEUE_ADMIN, $arDep['UF_HEAD'], 'Դուք նշանակվել եք Lead: <a href="/crm/lead/details/'.$leadID.'/">'.$leadID.'</a> պատասխանատու');

            if($arDep['UF_DISPECHER']>0)
                sendMessageToChat(QUEUE_ADMIN, $arDep['UF_DISPECHER'], 'Ձեր տնօրենին նշանակվել է Lead: <a href="/crm/lead/details/'.$leadID.'/">'.$leadID.'</a>');

        }

    }

    localRedirect($APPLICATION->GetCurPageParam());
}



// if($_GET['CLEARQUEUE']=='Y'){
//     foreach ($arResult['DEPARTMENTS'] as $arDepartment){
//         $obDep->Update($arDepartment['ID'], Array("UF_REQUESTSCYCLE_1"=>0, "UF_REQUESTSCYCLE_2"=>0) );
//     }
//     $Queue->ClearQueue();
//     echo '<p class="alert-info">Очеред очищен</p>';
// }

$this->IncludeComponentTemplate();