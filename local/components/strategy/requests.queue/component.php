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


// Собираем в масив список Лидов для вывода на фронте и распределение
if( intval($_GET['type']) >0){

    $type = $_GET['type'];

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

    //Передаем масив лидов в шаблон для форнта
    $arResult['QUEUE'] = $Queue->getList($monthFilter, false, $type);
}


//если нажата кнопка рапределения
if( !empty($_REQUEST['submit']) ){

    if( intval($_POST['lead']) and intval($_POST['department'])>0 and intval($_POST['type'])>0 ){

        $leadID = $_POST['lead'];
        $departmentID = $_POST['department'];
        $type = $_POST['type'];
    
        $arDep = getDepartamentList(array('XML_ID'=>$departmentID), false, 1);
        $arDep = current($arDep);
   
        $crmlead = new CCrmLead();
        $resLead = $crmlead->getByID($leadID);
    
        if( !empty($resLead) ){

            $arData = array(
                'DEPARTMENT'=>$arDep['XML_ID'],
                'DEP_XMLID'=>$arDep['ID'],
                'LEAD_ID'=>$resLead['ID'],
                'LEAD_NAME'=>$resLead['TITLE'],
                'USER_ID'=>$arDep['UF_HEAD'],
                "TYPE"=>$type,
                'NAME'=>$resLead['NAME'],
                'COMMENT'=>$resLead['COMMENTS'],
                'OPPORTUNITY'=>$resLead['OPPORTUNITY'],
                'DATE_CREATE'=>$resLead['DATE_CREATE'],
            );
            $resAdd = $Queue->Add($arData);

            if($resAdd){

                $fields['ASSIGNED_BY_ID'] = $arDep['UF_HEAD'];
                $fields['STATUS_ID'] = 'IN_PROCESS';
                $fields['UF_CRM_1594378997'] = 2; //Тип очереди Запрос
                $fields['UF_CRM_1595494850'] = $arDep['UF_DISPECHER']; //Dispatcher
        
                $resUPL = $crmlead->update($leadID, $fields);

                if($resUPL){               
                    CBPDocument::StartWorkflow(
                        10,
                        array('crm', 'CCrmDocumentLead', 'LEAD_'.$leadID),
                        array(),
                        $arErrorsTmp
                    );
            
                    if($arDep['UF_HEAD']>0)
                        sendMessageToChat(QUEUE_ADMIN, $arDep['UF_HEAD'], 'Դուք նշանակվել եք Lead: <a href="/crm/lead/details/'.$leadID.'/">'.$leadID.'</a> պատասխանատու');
            
                    if($arDep['UF_DISPECHER']>0)
                        sendMessageToChat(QUEUE_ADMIN, $arDep['UF_DISPECHER'], 'Ձեր տնօրենին նշանակվել է Lead: <a href="/crm/lead/details/'.$leadID.'/">'.$leadID.'</a>');                
                }
            }
        }
    
    }else{
        $_SESSION['notifymsg'] = 'Подразделение и Лид обязательно для распределение';
        $_SESSION['notifytype'] = "error";
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