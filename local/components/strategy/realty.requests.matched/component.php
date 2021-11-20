<?php 
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

if (!CModule::IncludeModule('iblock'))
{
    ShowError('MODULE IBLOCK NOT INSTALLED');
    return;
}

use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;


//**** REALTY FILTER FOR MATCHES REQUESTS **/
if( intval($_GET['id'])>0 ){

    $id = intval($_GET['id']);

    $filter['IBLOCK_ID'] = REALTY_IBLOCK_ID;
    $filter['ACTIVE'] ="Y";
    $filter['ID'] = $id;

    $arSelect = array('IBLOCK_ID', 'ID', 'NAME', 'PROPERTY_*');
   // dump($filter);
    $res = CIBlockElement::GetList(array("ID"=>"DESC"), $filter, false, Array("nPageSize"=>1), $arSelect );

    if( $ob = $res->GetNextElement() ) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        //dump($arProps);
    }


    $skipFields = array(
        'OWNERID', 'CLIENTID', 'REALTY_PURPOSE', 'BUILDING_TYPE', 'CITY', 'STREET', 'HOME', 'APARTMENT', 'POSITION', 'FLOORS', 'REPAIRS',
        'CEILING','INTERFLOOR_OVERLAP', 'GARAGE', 'FURNITURE', 'BASEMENT', 'SEMIBASEMENT', 'APPLIANCES', 'CONDITIONER', 'HEATING',
        'ADDIN', 'PARKING', 'STREET_ENTRANCE', 'BALCONY1', 'BALCONY2', 'ATTIC', 'BUILDING_LENGTH', 'LENGTH', 'HEIGHT', 'BUILDING_SATE',
        'CELLAR', 'BUILT_UP', 'BUILT_AT', 'WINDOWSTREET', 'WINDOWOTHER', 'EXITSTREET', 'STREET_OTHER', 'TOSITE', 'D','X','OTHER_PARAMETERS',
        'RENTDATEFROM', 'RENTDATETO', 'MARK', 'LINE', 'RATING', 'STATUS', 'DEPARTMENT', 'PHONE', 'MORE_PHOTO', 'AGENCYID', 'REALTORID',
        'FLOOR'
    );

    //dump($arProps);

    $arFilterRequest['IBLOCK_ID'] = CLIENT_REQUEST_IBLOCKID;
    $arFilterRequest['ACTIVE'] = 'Y';

    foreach($arProps as $arProp){
        //dump($arProps);
        if( in_array( $arProp['CODE'], $skipFields) ) continue;
        if(!empty($arProp['VALUE'])){

            if( $arProp['PROPERTY_TYPE']=='L' ){
                $arFilterRequest['PROPERTY_'.$arProp['CODE'].'_VALUE'] = $arProp['VALUE'];
            }
            else if( in_array($arProp['CODE'], array('PRICE', 'PRICE_MONTH', 'PRICE_DAY', 'ROOMS', 'FLOORS', 'TOTAL_AREA', 'USEFUL_AREA') ) ){
                $arFilterRequest['<= PROPERTY_'.$arProp['CODE'].'_FROM'] = $arProp['VALUE'];
                $arFilterRequest['>= PROPERTY_'.$arProp['CODE'].'_TO'] = $arProp['VALUE'];
            }
            else{
                $arFilterRequest['PROPERTY_'.$arProp['CODE']] = $arProp['VALUE'];
            }
        }
    }

    // for test
//$arFilterRequest['PROPERTY_DEAL_TYPE_VALUE']='Продажа';
//$arFilterRequest['PROPERTY_REALTY_TYPE_VALUE'] = 'Квартира';
//$arFilterRequest['<=PROPERTY_PRICE_FROM'] = 50000;
//$arFilterRequest['>=PROPERTY_PRICE_TO'] = 50000s;
//$arFilterRequest['PROPERTY_ZONA'] = '1/1';
//$arFilterRequest['<=PROPERTY_ROOMS_FROM'] = 5;
//$arFilterRequest['>=PROPERTY_ROOMS_TO'] =5;
}


// for matched data
$gridID = 'MatchedRequestList';
$grid_options = new GridOptions($gridID);
$sort = $grid_options->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
$nav_params = $grid_options->GetNavParams();

$nav = new PageNavigation($gridID);
$nav->allowAllRecords(true)
    ->setPageSize($nav_params['nPageSize'])
    ->initFromUri();
if ($nav->allRecordsShown()) {
    $nav_params = false;
} else {
    $nav_params['iNumPage'] = $nav->getCurrentPage();
}


$defaultFilelds = array('CLIENTID', 'DEAL_TYPE', 'REALTY_TYPE', 'PRICE_FROM', 'PRICE_TO', 'ZONA', 'STREET', 'ROOMS_FROM', 'ROOMS_TO', 'FLOOR', 'FLOORS_FROM', 'FLOORS_TO', 'TOTAL_AREA_FROM', 'TOTAL_AREA_TO');

// makein array for grid component
$columns = [];
$columns[] = ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => false];
$columns[] = ['id' => 'NAME', 'name' => getMessage('NAME'), 'sort' =>'NAME', 'default' => true];
$columns[] = ['id' => 'DATE_CREATE', 'name' => getMessage('DATE_CREATE'), 'sort' =>'NAME', 'default' => false];
$columns[] = ['id' => 'TIMESTAMP_X', 'name' => getMessage('TIMESTAMP_X'), 'sort' =>'TIMESTAMP_X', 'default' => false];
$columns[] = ['id' => 'CREATED_BY', 'name' => getMessage('CREATED_BY'), 'sort' =>'CREATED_BY', 'default' => true];


/* get Property list for FIRLTER*/
$rsIBLockPropertyList = CIBlockProperty::GetList(array("sort"=>"asc", "id"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arFilterRequest['IBLOCK_ID']));
while ($arProperty = $rsIBLockPropertyList->GetNext())
{
    if($arProperty["PROPERTY_TYPE"]=='F') continue;

//    if($_GET['type']){
//        $pid = intval($_GET['type']);
//        if( in_array($arProperty['ID'], $arParams['PROPERTY_'.$pid] ) ) continue;
//    }

    $propertyList[] = $arProperty;

    $defField = false;
    if(in_array($arProperty['CODE'], $defaultFilelds)) $defField = true;

    $label = false;
    if(LANGUAGE_ID=='en'){$label = $arProperty['HINT'];} else {$label = $arProperty['NAME'];}
    $columns[] = ['id' => $arProperty['CODE'], 'name' => $label, 'sort' => 'PROPERTY_'.$arProperty['CODE'], 'default' => $defField];
}


//dump($arFilterRequest);
$arResult['GRID_DATA'] = array();

if(!empty($arFilterRequest) ){

    $res = \CIBlockElement::GetList($sort['sort'], $arFilterRequest, false, $nav_params,
        ['IBLOCK_ID', 'ID', 'NAME', 'DATE_CREATE', 'TIMESTAMP_X', 'CREATED_BY', 'PROPERTY_*']
    );
    $nav->setRecordCount($res->selectedRowsCount());

    while( $ob = $res->GetNextElement() ) {

        $arElement = $ob->GetFields();
        $arElement['PROPERTIES'] = $ob->GetProperties();
        $arElement['USER'] = getUser($arElement['CREATED_BY']);

        $formType = strtolower($arElement['PROPERTIES']['REALTY_TYPE']['VALUE_ENUM_ID']);

        $arData['ID'] = $arElement['ID'];
        $arData['NAME'] = '<a href="/realty/client-requests/edit/?ID='.$arElement['ID'].'?type='.$formType.'">'.$arElement['NAME'].'</a>';
        $arData['DATE_CREATE'] = $arElement['DATE_CREATE'];
        $arData['CREATED_BY'] = $arElement['USER']['FULL_NAME'];
        $arData['TIMESTAMP_X'] = $arElement['TIMESTAMP_X'];

        foreach($arElement['PROPERTIES'] as $key=> $arProp){
            //dump($arProp['CODE']);
            if($arProp['MULTIPLE']=='Y'){
                $arData[$key] =  implode(',', $arProp['VALUE']);
            }else{
                $arData[$key] =  $arProp['VALUE'];
            }
        }

        $arActions = array();

        $arActions[] = array(
            'text'    => 'Посмотреть запрос',
            'default' => false,
            'onclick' => 'document.location.href="/realty/client-requests/edit/?ID='.$arData['ID'].'"',
        );

        $arResult['GRID_DATA'][] = array('data'=>$arData, 'actions'=>$arActions);
    }
}
?>

<div class="row">
    <div class="col-sm-12 text-center"><br>
    <p class="h4">Совпадении недвижимости <?=$arFields['NAME']?> с запросами клентов</p><br>
    </div>

    <div class="col-sm-12">
    <?$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
        'GRID_ID' => $gridID,
        'COLUMNS' => $columns,
        'ROWS' => $arResult['GRID_DATA'],
        'SHOW_ROW_CHECKBOXES' => false,
        'NAV_OBJECT' => $nav,
        'AJAX_MODE' => 'Y',
        'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
        'PAGE_SIZES' =>  [
			['NAME' => '20', 'VALUE' => '20'],
			['NAME' => '30', 'VALUE' => '30'],
            ['NAME' => '50', 'VALUE' => '50'],
            ['NAME' => '100', 'VALUE' => '100']
        ],
        'AJAX_OPTION_JUMP'          => 'N',
        'SHOW_CHECK_ALL_CHECKBOXES' => false,
        'SHOW_ROW_ACTIONS_MENU'     => true,
        'SHOW_GRID_SETTINGS_MENU'   => true,
        'SHOW_NAVIGATION_PANEL'     => true,
        'SHOW_PAGINATION'           => true,
        'SHOW_SELECTED_COUNTER'     => false,
        'SHOW_TOTAL_COUNTER'        => true,
        'SHOW_PAGESIZE'             => true,
        'SHOW_ACTION_PANEL'         => true,
        'ALLOW_COLUMNS_SORT'        => true,
        'ALLOW_COLUMNS_RESIZE'      => true,
        'ALLOW_HORIZONTAL_SCROLL'   => true,
        'ALLOW_SORT'                => true,
        'ALLOW_PIN_HEADER'          => true,
        'AJAX_OPTION_HISTORY'       => 'N'
]);?>
    </div>
</div>

<?
$this->IncludeComponentTemplate();