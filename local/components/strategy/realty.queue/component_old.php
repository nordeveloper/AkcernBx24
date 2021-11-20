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

$Queue = new RDQueue('st_realty_queue');

// Катнора ID = 60 или XML_ID = 5 не нужно для очереди;
$arResult['DEPARTMENTS']  = getDepartamentList(array("!XML_ID"=>5));

if( intval($_GET['region'])>0 ){  
    // get New Leads

    $region = intval($_GET['region']);

    // get Leads NEW
    //Передаем данные очереди в шаблон для левого блока
    $arFilter['ASSIGNED_BY_ID'] = QUEUE_ADMIN; //Отвественный info@akcern.am
    $arFilter['STATUS_ID'] = 'NEW';
    $arFilter['UF_CRM_1580368062']= 'SUPPLIER';

    if($region==1){ $arFilter['UF_CRM_1580469855'] = 114; } //Ереван
    if($region==2){ $arFilter['UF_CRM_1580469855'] = 115; } //регионы

    $arNewLeads = CCrmLead::getList(Array('DATE_CREATE' => 'ASC'), $arFilter);

    while ($arLead = $arNewLeads->fetch()){
        $arResult['NEW_LEADS'][] = $arLead;
    }

    $monthFilter = date('Y-m');
    if(!empty($_GET['month'])){ $monthFilter = $_GET['month'];}

    //Передаем данные очереди в шаблон
    $arResult['QUEUE'] = $Queue->getList($monthFilter, $region);
}



$obDep = new CIBlockSection;
$filter['!XML_ID'] = 5;   // Катнора ID =60 или XML_ID = 5 не нужно для очереди;

if(intval($_POST['region'])>0 and !empty($_POST['department']) and empty($_POST['leads']) ){

    $region = $_POST['region'];

    $QueueCycleCount = $Queue->QueueCycleCount(['REGION'=>$region])+1;


    foreach($_POST['department'] as $depIdForSkipe){
    
        $depForSkipe = getDepartamentList( array('XML_ID'=>$depIdForSkipe) );

        $dataForSkipe = array('DEPARTMENT'=>$depIdForSkipe, 'DEP_XMLID'=>$depForSkipe[$depIdForSkipe]['ID'], 'LEAD_ID'=>0, 'LEAD_NAME'=>'x', 'USER_ID'=>$depForSkipe[$depIdForSkipe]['UF_HEAD'], 'QUEUE'=>$QueueCycleCount, 'CYCLE'=>$QueueCycleCount, 'REGION'=>$region);

        if($Queue->Add($dataForSkipe)){
            $obDep->Update($depForSkipe[$depIdForSkipe]['ID'], array("UF_REALTYCYCLE_".$region=>$QueueCycleCount) );
        }

    }
    
    localRedirect($APPLICATION->GetCurPageParam());
}



if( !empty($_POST['leads']) and intval($_POST['region'])>0 ){

    $region = $_POST['region'];
    $crmlead = new CCrmLead();


    foreach ($_POST['leads'] as $leadID=> $leadTitle) {
        
        $nextQueueCount = $Queue->QueueCycleCount(['REGION'=>$region])+1;

        $arDep = getDepartamentList($filter, array("UF_REALTYCYCLE_".$region=>'ASC', "ID"=>'ASC'), 1);
        $arDep = current($arDep);   

        $arUser = getUser($arDep['UF_HEAD']);

        $resLead = $crmlead->getByID($leadID);

        // adding queue to db
        $QueueData = array(
            'DEPARTMENT'=>$arDep['XML_ID'],
            'DEP_XMLID'=>$arDep['ID'],
            'LEAD_ID'=>$leadID,
            'LEAD_NAME'=>$leadTitle,
            'USER_ID'=>$arUser['ID'],
            'QUEUE'=>$nextQueueCount,
            'CYCLE'=>$nextQueueCount,
            'REGION'=>$region,
            'NAME'=>$resLead['NAME'],
            'COMMENTS'=>$resLead['COMMENTS'],
            'OPPORTUNITY'=>$resLead['OPPORTUNITY'],
            'DATE_CREATE'=>$resLead['DATE_CREATE'],
            'URL'=>$_SERVER['REQUEST_URI'],
            'ASSIGNED_FULLNAME'=>$arUser['FULL_NAME']
        );

        // dump($QueueData);

        $resAd = $Queue->Add($QueueData);

        if($resAd){


            $obDep->Update($arDep['ID'], array("UF_REALTYCYCLE_".$region=>$nextQueueCount) );

            $fields['ASSIGNED_BY_ID'] = $arDep['UF_HEAD'];
            $fields['STATUS_ID'] = 'IN_PROCESS';
            $fields['UF_CRM_1594378997'] = 1; //Тип очереди недвижимость
            $fields['UF_CRM_1595494850'] = $arDep['UF_DISPECHER']; //Dispatcher
            $resLUP = $crmlead->update($leadID, $fields);

            // dump($fields);

            if($resLUP){
                $BPres = CBPDocument::StartWorkflow(
                    10,
                    array('crm', 'CCrmDocumentLead', 'LEAD_'.$leadID),
                    array(),
                    $arErrorsTmp
                );

                // dump('BizProCRuned');
            }

            if($arDep['UF_HEAD']>0)
                sendMessageToChat(QUEUE_ADMIN, $arDep['UF_HEAD'], 'Ձեր տնօրենին նշանակվել է Lead: <a href="/crm/lead/details/'.$leadID.'/">'.$title.'</a>' );

            if( $arDep['UF_DISPECHER'] >0)
                sendMessageToChat(QUEUE_ADMIN, $arDep['UF_DISPECHER'], 'Ձեր տնօրենին նշանակվել է Lead: <a href="/crm/lead/details/'.$leadID.'/">'.$title.'</a>');
        }
        
    }
    
    localRedirect($APPLICATION->GetCurPageParam());
}



/*
if($_GET['CLEARQUEUE']=='Y'){
    foreach ($arResult['DEPARTMENTS'] as $arDepartment){
        $obDep->Update($arDepartment['ID'], Array("UF_REALTYCYCLE_1"=>0, "UF_REALTYCYCLE_2"=>0) );
    }
    $Queue->ClearQueue();
    echo '<p class="alert-info">Очеред очищен</p>';
}*/

$this->IncludeComponentTemplate();