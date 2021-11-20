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

// users array data for Filter form;
$arUsers = getUsers();
foreach ($arUsers as $arUser){
    $arUsersList[$arUser['ID']] = $arUser['NAME'].' '.$arUser['LAST_NAME'];
}
$arResult['arUsersList'] = $arUsersList;

$regionList = array();
foreach (Region::getList() as $arRegion){
    $regionList[$arRegion['id']] = $arRegion['name_'.LANGUAGE_ID];
}
$arResult['regionList'] = $regionList;

$cityList = array();
foreach (City::getList() as $arCity){
    $cityList[$arCity['id']] = $arCity['name_'.LANGUAGE_ID];
}
$arResult['cityList'] = $cityList;


// zone items for filter form;
$zoneList = array();
foreach (Zone::getList() as $arZone){
    $zoneList[$arZone['code']] = $arZone['code'];
}
$arResult['zoneList'] = $zoneList;


// street items for filter filter form
$streetList = array();
$arStreetRes = Street::getList();
foreach ($arStreetRes['ITEMS'] as $arStreet){
    $streetList[$arStreet['id']] = $arStreet['name_'.LANGUAGE_ID];
}
$arResult['streetList'] = $streetList;


$gridID = 'LockedList';
use Bitrix\Main\UI\Filter\Options as FilterOptions;
$filterOption = new FilterOptions($gridID);
$filterData = $filterOption->getFilter([]);


if(!empty($filterData)){
    foreach ($filterData as $filter) {
        if(!empty($filterData['FIND']) ) {$filterData['REALTY_CODE'] = trim($filterData['FIND']);}
        unset($filterData['FIND']);
        unset($filterData['PRESET_ID']);
        unset($filterData['FILTER_ID']);
        unset($filterData['FILTER_APPLIED']);
    }
}


$arResult['ITEMS'] = Lockedaddress::getList($filterData);


foreach ($arResult['ITEMS'] as $arItem){

    if($arItem['ACTIVE']==1){ $arItem['ACTIVE']='Да';} else {$arItem['ACTIVE'] = 'Нет';}
    $arItem['REALTY_ID'] = $arItem['REALTY_CODE'];
    $arItem['STREET'] = $streetList[$arItem['STREET']];
    $arItem['REGION'] = $regionList[$arItem['REGION']];
    
    if($arItem['CITY']>0){
        $arItem['CITY'] = $cityList[$arItem['CITY']];
    }else{ $arItem['CITY'] = false;}


    if($arItem['DEAL_TYPE']==70){
        $arItem['DEAL_TYPE'] = 'Аренда';
    }
    if($arItem['DEAL_TYPE']==71){
        $arItem['DEAL_TYPE'] = 'Продажа';
    }

    $arActions = array();

    $arActions[] = array(
        'text'    => getMessage('REMOVE'),
        'default' => true,
        'onclick' => 'if(confirm("Точно?")){document.location.href="?op=remove&id='.$arItem['ID'].'"}'
    );

    $arResult['GRID_DATA'][] = array('data'=>$arItem, 'actions'=>$arActions);
}

if( $_GET['op']=='remove' and $_GET['id']>0){
    Lockedaddress::RemoveByID($_GET['id']);
    localRedirect($APPLICATION->GetCurPage());
}


if( !empty($_POST['BTN_ADD']) and !empty($_POST['PROPERTY']) ){

    $arData['deal_type'] = $_POST['PROPERTY']['deal_type'];
    $arData['user_id']= intval($_POST['user_id']);
    $arData['region']= $_POST['PROPERTY']['region_id'];
    $arData['city']= $_POST['PROPERTY']['city_id'];
    $arData['zone']= $_POST['PROPERTY']['zone'];
    $arData['street']= $_POST['PROPERTY']['street_id'];
    $arData['home']= $_POST['PROPERTY']['home'];
    $arData['apartment']= $_POST['PROPERTY']['apartment'];

    $rs = Lockedaddress::getLocked($arData);

    $usrlokcount = Lockedaddress::getUserLockedCount($arData);


    $filterR['IBLOCK_ID']= REALTY_IBLOCK_ID;
    $filterR['ACTIVE'] = 'Y';

    $filterR['PROPERTY_92'] = $_POST['PROPERTY']['deal_type']; //Тип сделки
    $filterR['PROPERTY_96'] = $_POST['PROPERTY']['region_id']; //Регион
    $filterR['PROPERTY_97'] = $_POST['PROPERTY']['zone']; //Зона
    $filterR['PROPERTY_98'] = $_POST['PROPERTY']['street_id']; //Улица
    $filterR['PROPERTY_99'] = trim($_POST['PROPERTY']['home']); //Дом

    if( !empty($_POST['PROPERTY']['apartment']) ){
        $filterR['PROPERTY_100'] = trim($_POST['PROPERTY']['apartment']);
    }

    $resRealty = getRealty($filterR);
    if(!empty($resRealty)){
        $arResult['MESSAGE']['ERROR'] = 'Этот адрес уже сушествует';
        $error = 'Этот адрес уже сушествует';
    }

//    dump($resRealty);
//    file_put_contents(__DIR__.'/LockAddress.log', print_r($resRealty, true), FILE_APPEND);

    if( empty($rs) and empty($error) ) {

        if($usrlokcount['COUNT']<2){
            $arResult['MESSAGE'] = Lockedaddress::Add($arData);
        }else{
            $arResult['MESSAGE']['ERROR'] = 'Нельзя блокировать 3 день подряд';
        }

        if( !empty($arResult['MESSAGE']['SUCCESS']) ){
            localRedirect($APPLICATION->getCurPage().'?success=Y');
        }

    }else if( !empty($rs) ){
        $arResult['MESSAGE']['ERROR'] = 'Этот адрес уже блокирован';
    }

}

$this->IncludeComponentTemplate();