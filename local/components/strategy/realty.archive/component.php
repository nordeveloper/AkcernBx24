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

use \Bitrix\Main\Loader;
if(!Loader::includeModule('iblock')) {
    ShowError('MODULE IBLOCK NOT INSTALLED');
    return;
}

if(intval(REALTY_IBLOCK_ID)>0){
    $IBLOCK_ID = REALTY_IBLOCK_ID;
}else{
    ShowError('Не правильный инфоблок, В параметрах компонента нужно настроить инфоблок');
    return;
}

use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\UI\Filter\Options as FilterOptions;


\CJSCore::init("sidepanel");
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/jquery.fancybox.min.css');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.fancybox.min.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/script.js');

// User in Group
$arGroups = $USER->GetUserGroupArray();
$arGroupAdmins = $arParams['GROUPS_ADMINS'];
$arGroupDirectors = $arParams['GROUPS_DIRECTORS'];
$AdminAccess = array_intersect($arGroups, $arGroupAdmins);
$DirectorAccess = array_intersect($arGroups, $arGroupDirectors);

$arCurUser = getUser($USER->getID());

if($arCurUser['UF_DEPARTMENT'][0]>0){
    $dep = getDepartment($arCurUser['UF_DEPARTMENT'][0]);
    $arCurUser['DEPARTMENT'] = $dep['XML_ID'];
}

$arUsers = getUsers();
foreach ($arUsers as $arUser){
    $arUsersList[$arUser['ID']] = $arUser['NAME'].' '.$arUser['LAST_NAME'];
}

$arDepartments =  getDepartamentList();
foreach ($arDepartments as $Department){
    $arDepartmentList[$Department['XML_ID']] = $Department['NAME'];
}


// grid options and navigation
$gridID = 'realty_archive';
//unicalize grid filter;
if(intval($_GET['realty_type'])>0){
    $realtyType = $_GET['realty_type'];
    $gridID = 'realty_archive'.$realtyType;
}

$grid_options = new GridOptions($gridID);
$sort = $grid_options->GetSorting(['sort' => ['DATE_CREATE' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
$nav_params = $grid_options->GetNavParams();


/* default filter Properties */
$defaultFilters = array('DEAL_TYPE', 'REGION', 'CITY', 'ZONE', 'STREET', 'ROOMS', 'FLOOR', 'REPAIRS', 'PRICE', 'TOTAL_AREA', 'USEFUL_AREA', 'GROUND_AREA', 'TOTAL_GROUND_AREA', 'DEPARTMENT');
$defaultFields = array('OWNERID', 'DEAL_TYPE', 'ZONE', 'STREET', 'ROOMS', 'FLOOR', 'HOME', 'APARTMENT', 'PRICE', 'GROUND_AREA', 'GROUND_STATUS', 'TOTAL_GROUND_AREA');
$customFilterList = array('regions', 'zones', 'city','streets', 'floor', 'floors');
$multiFilelds = array('DEAL_TYPE', 'REALTY_TYPE', 'REALTY_PURPOSE', 'FLOOR', 'FLOORS', 'REPAIRS', 'BUILDING_TYPE', 'BUILDING_SATE', 'GROUND_STATUS');


/* making filter array */
$arUiFilter = [
    ['id' => 'ID', 'name' => 'ID', 'type'=>'text', 'default' => false],
    ['id' => 'NAME', 'name' => getMessage('NAME'), 'type'=>'text', 'default' => true],
    ['id' => 'CREATED_BY', 'name' => getMessage('CREATED_BY'), 'type'=>'list', 'items'=>$arUsersList, 'default' =>true],
    ['id' => 'PROPERTY_DEPARTMENT', 'name' => getMessage('DEPARTMENT'), 'type'=>'list', 'items'=>$arDepartmentList, 'default' =>true],
    ['id' => 'DATE_CREATE', 'name' => getMessage('DATE_CREATE'), 'type'=>'date', 'default' =>false],
    ['id' => 'TIMESTAMP_X', 'name' => getMessage('TIMESTAMP_X'), 'type'=>'date', 'default' =>false],
    ['id' => 'DATE_ACTIVE_TO', 'name' => 'Дата активности', 'type'=>'date', 'default' =>false],    
];


$propertyList = array();


/* get Property list for Filter and Grid Columns*/

$resProps = Bitrix\Iblock\PropertyTable::getList(array(
    'filter' => array('IBLOCK_ID' => REALTY_IBLOCK_ID, "ACTIVE"=>"Y", "FILTRABLE"=>'Y'), 'order'=>array('SORT'=>'ASC')
));

while ($arProperty = $resProps->Fetch())

{
    if($realtyType>0 and in_array($arProperty['ID'], $arParams['PROPERTY_'.$realtyType] ) ) continue;

    $arProperty['id'] = 'PROPERTY_'.$arProperty['CODE'];
    if(LANGUAGE_ID=='en'){$arProperty['name'] = $arProperty['HINT'];} else {$arProperty['name'] = $arProperty['NAME'];}

    if($arProperty["PROPERTY_TYPE"]=="N") {$arProperty['type'] = 'number';}
    if($arProperty["PROPERTY_TYPE"]=="S") {$arProperty['type'] = 'string';}

    if( in_array($arProperty['CODE'], $defaultFilters) ) {
        $arProperty['default'] = true;
    }

    if(in_array( $arProperty['CODE'], $multiFilelds ) ){
        $arProperty['params'] = ['multiple' => 'Y'];
    }

    if ($arProperty["PROPERTY_TYPE"] == "L")
    {
        $rsPropertyEnum = CIBlockProperty::GetPropertyEnum($arProperty["ID"]);
        $arProperty["items"] = array();
        while ($arPropertyEnum = $rsPropertyEnum->GetNext())
        {
            if(LANGUAGE_ID=='en'){
                $arProperty["items"][$arPropertyEnum["ID"]] = html_entity_decode($arPropertyEnum['XML_ID']);
            }
            else{
                $arProperty["items"][$arPropertyEnum["ID"]]= html_entity_decode($arPropertyEnum['VALUE']);
            }
        }

        $arProperty['type'] = 'list';
    }

    // adding properties to filter array
    $arUiFilter[$arProperty['ID']] = $arProperty;

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

            if(in_array( $arProperty['CODE'], $multiFilelds ) ){
                $multiple = ['multiple' => 'Y'];
            }            

            $items = $arProperty["USER_TYPE"].'List';
            $arUiFilter[$arProperty['ID']] = [
                'id'=>'PROPERTY_'.$arProperty['CODE'],
                'name'=>$arProperty['NAME'],
                'type'=>'list',
                'items'=>$items(),
                'params'=>$multiple,
                'default'=>$arProperty['default']
            ];
        }
    }

    $propertyList[] = $arProperty;
}



$columns = [];
$columns[] = ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => false];
if($_GET['EXPORT']!=="Y"){
    $columns[] = ['id' => 'DETAIL_PICTURE', 'name' => getMessage('PICTURE'), 'sort' =>'DETAIL_PICTURE', 'default' => false];
}
$columns[] = ['id' => 'NAME', 'name' => getMessage('NAME'), 'sort' =>'NAME', 'default' => true];
$columns[] = ['id' => 'DATE_CREATE', 'name' => getMessage('DATE_CREATE'), 'sort' =>'DATE_CREATE', 'default' => true];
$columns[] = ['id' => 'CREATED_BY', 'name' => getMessage('CREATED_BY'), 'sort' =>'CREATED_BY', 'default' => true];
$columns[] = ['id' => 'TIMESTAMP_X', 'name' => getMessage('TIMESTAMP_X'), 'sort' =>'TIMESTAMP_X', 'default' => false];
$columns[] = ['id' => 'DATE_ACTIVE_TO', 'name' => 'Дата активности', 'sort' =>'DATE_ACTIVE_TO', 'default' => false];



foreach ($propertyList as $arProp){
    $defField = false;
    if(in_array($arProp['CODE'], $defaultFields)) $defField = true;

    //changeing labels when switch language
    $label = false;
    if(LANGUAGE_ID=='en'){$label = $arProp['HINT'];} else {$label = $arProp['NAME'];}
    $columns[] = ['id' =>$arProp['CODE'], 'name' =>$label, 'sort' =>'PROPERTY_'.$arProp['CODE'], 'default'=>$defField];
}


// set defaut filter params
$filterOption = new FilterOptions($gridID);
$filterData = $filterOption->getFilter([]);

$arFilter['IBLOCK_ID'] = $IBLOCK_ID;
$arFilter['ACTIVE'] ="Y";

// $arFilter['SHOW_NEW'] = "Y";
$arFilter['PROPERTY_MATCHED']=false;

// $arFilter["<DATE_ACTIVE_TO"] = date('d.m.Y');

$arFilter[ "!ACTIVE_DATE" ]= "Y";

if($_REQUEST['realty_type']){
    $arFilter['PROPERTY_REALTY_TYPE'] = intval($_REQUEST['realty_type']);
}

//customizing filter
$elementFilter = ElementFilter($filterData, $arFilter);

// dump($elementFilter);


//grid pagination
$nav = new PageNavigation($gridID);
$nav->allowAllRecords(true)
    ->setPageSize($nav_params['nPageSize'])
    ->initFromUri();
if ($nav->allRecordsShown()) {
    $nav_params = false;
} else {
    $nav_params['iNumPage'] = $nav->getCurrentPage();
}


//get data from Realty Iblock with filter with pagination;
$res = \CIBlockElement::GetList($sort['sort'], $elementFilter, false, $nav_params,
    ['IBLOCK_ID', 'ID', 'NAME', 'DATE_CREATE', 'CREATED_BY', 'TIMESTAMP_X', "DATE_ACTIVE_FROM", "DATE_ACTIVE_TO", 'DETAIL_PICTURE', 'PROPERTY_*']
);
$nav->setRecordCount($res->selectedRowsCount());


while( $ob = $res->GetNextElement() ) {

    $arElement = $ob->GetFields();
    $arElement['PROPERTIES'] = $ob->GetProperties();

    $arElement['USER'] = getUser($arElement['CREATED_BY']);

    $formType = strtolower($arElement['PROPERTIES']['REALTY_TYPE']['VALUE_ENUM_ID']);

    $arData['ID'] = $arElement['ID'];

    if($_GET['EXPORT']!=="Y"){
        $arData['DETAIL_PICTURE'] = '<img class="realty-image" data-elID="'.$arElement['ID'].'" style="width:150px" src="'.CFile::GetPath($arElement['DETAIL_PICTURE']).'">';
        $arData['NAME'] = '<a href="/realty/realtyinfo/?type='.$formType.'&ID='.$arElement['ID'].'&page=archive&&realty_list=page-'.$nav_params['iNumPage'].'">'.$arElement['NAME'].'</a>';
        $arData['DATE_CREATE'] = $arElement['DATE_CREATE'];
        $arData['DATE_ACTIVE_TO'] = $arElement['DATE_ACTIVE_TO'];
        
        $arData['CREATED_BY'] = '<a class="user-link" target="_blank" href="/company/personal/user/'.$arElement['USER']['ID'].'/">'.$arElement['USER']['FULL_NAME'].'</a>';
    }else{
        unset($arElement['DETAIL_PICTURE']);
        $arData['NAME'] = $arElement['NAME'];
        $arData['DATE_CREATE'] = $arElement['DATE_CREATE'];
        $arData['CREATED_BY'] = $arElement['USER']['FULL_NAME'];
    }

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

        //change labels for current language
        if($arProp["PROPERTY_TYPE"]=='L' and LANGUAGE_ID=='en'){
            $arProp['VALUE'] = $arProp['VALUE_XML_ID'];
        }

        if( is_array($arProp['VALUE']) ){
            $arProp['VALUE'] =  implode(',', $arProp['VALUE']);
        }

        if( empty($AdminAccess) AND (empty($DirectorAccess) and $arElement['CREATED_BY']!=$USER->getID()
                AND ($arProp['CODE']=='APARTMENT' or $arProp['CODE']=='HOME' or $arProp['CODE']=='PHONE'))
            OR (($DirectorAccess AND $arElement['PROPERTIES']['DEPARTMENT']['VALUE'] != $arCurUser['DEPARTMENT'])
                AND ($arProp['CODE']=='APARTMENT' or $arProp['CODE']=='HOME' or $arProp['CODE']=='PHONE')
            )
        ){
            $arProp['VALUE'] = getMessage('ACCESS_DENIED');
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

        if($key=='PRICE' and !empty($arProp['VALUE']) ){
            $arProp['VALUE'] = number_format($arProp['VALUE'], 0, '', ' ');
        }

        $arData[$key]= $arProp['VALUE'];
    }


    $arActions = array();

    if( !empty($AdminAccess) ){

        $arActions[] = array(
            'text'    => getMessage('EDIT'),
            'default' => false,
            'onclick' => 'document.location.href="/realty/realtyinfo/?type='.$formType.'&ID='.$arData['ID'].'"',
        );

        $arActions[] = array(
            'text'    => getMessage('RESTORE'),
            'default' => true,
            'onclick' => 'if(confirm("Вы уверены что хотите восстановить?")){document.location.href="?op=restore&id='.$arData['ID'].'"}'
        );
    }


    $SelfAccess = false;
    if(!empty($DirectorAccess) and $arElement['PROPERTIES']['DEPARTMENT']['VALUE'] == $arCurUser['DEPARTMENT']
        OR ( $arElement['CREATED_BY'] == $USER->getID() )
    ){
        $SelfAccess = 'Y';
    }

    if( !empty($AdminAccess) OR  $SelfAccess=='Y' ) {
        $arActions[] = array(
            'text'    => getMessage('REMOVE'),
            'default' => true,
            'onclick' => 'if(confirm("'.getMessage('ARE_YOU_SURE').'")){document.location.href="?op=remove&id='.$arData['ID'].'"}'
        );
    }

    $arResult['GRID_DATA'][] = array('data'=>$arData, 'actions'=>$arActions) ;
}


if( $_GET['op']=='restore' and $_GET['id']>0){
    $ELEMENT_ID = intval($_GET['id']);
    $el = new CIBlockElement;
    $arUpdate['ACTIVE'] = 'Y';
    $arUpdate['ACTIVE_FROM'] = date('d.m.Y');
    $arUpdate['ACTIVE_TO'] = date('d.m.Y', strtotime( date('d.m.Y') ."+18 month") );

    $res = $el->Update($ELEMENT_ID, $arUpdate);
    localRedirect($APPLICATION->getCurPage(false));
}


if( $_GET['op']=='remove' and intval($_GET['id'])>0){
    $el = new CIBlockElement;
    $arUpdate['ACTIVE'] = 'N';
    $res = $el->Update($_GET['id'], $arUpdate);
    localRedirect($APPLICATION->getCurPage(false));
}

$_SESSION['PRINT_ID'] = 'USED_COLUMNS_'.$gridID;
$_SESSION[$_SESSION['PRINT_ID']] = $grid_options->getUsedColumns();
$_SESSION['COLUMNS'] = $columns;

$_SESSION['BACK_URL'] = $APPLICATION->GetCurPageParam();
?>

    <div class="row">
        <div class="col-sm-12">

            <p class="h3"><?=getMessage('COMPONENT_TITLE')?></p>

            <?$APPLICATION->IncludeComponent('bitrix:main.ui.filter', '',
                ['FILTER_ID' => $gridID,
                    'GRID_ID' => $gridID,
                    'FILTER' => $arUiFilter,
                    'ENABLE_LIVE_SEARCH' => true,
                    'ENABLE_LABEL' => true]
            );?>

            <button type="button" class="btn btn-print" onclick="javascript:window.print()"><i class="glyphicon glyphicon-print"></i></button>
            <a class="btn btn-outline-info btn-export" href="?EXPORT=Y"><i class="glyphicon glyphicon-import"></i></a>

            <?$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
                'GRID_ID' => $gridID,
                'COLUMNS' => $columns,
                'ROWS' => $arResult['GRID_DATA'],
                'SHOW_ROW_CHECKBOXES' => true,
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
                'SHOW_CHECK_ALL_CHECKBOXES' => true,
                'SHOW_ROW_ACTIONS_MENU'     => true,
                'SHOW_GRID_SETTINGS_MENU'   => true,
                'SHOW_NAVIGATION_PANEL'     => true,
                'SHOW_PAGINATION'           => true,
                'SHOW_SELECTED_COUNTER'     => false,
                'SHOW_TOTAL_COUNTER'        => true,
                'SHOW_PAGESIZE'             => true,
                'SHOW_ACTION_PANEL'         => true,
                'ACTION_PANEL'=> [
                    'GROUPS' => [
                        'TYPE' => [
                            'ITEMS' => [
                                [
                                    'TYPE' => \Bitrix\Main\Grid\Panel\Types::BUTTON,
                                    'ID' => "apply_button",
                                    'CLASS' => "apply",
                                    'TEXT' => 'Печать',
                                    'ONCHANGE' => [
                                        [
                                            'ACTION' => Bitrix\Main\Grid\Panel\Actions::CALLBACK,
                                            'DATA' => array(
                                                array(
                                                    'JS' => "doPrint(BX.Main.gridManager.getById('".$gridID."').instance.rows.getSelectedIds())",
                                                )
                                            )
                                        ]
                                    ]
                                ],
                            ],
                        ]
                    ]
                ],
                'ALLOW_COLUMNS_SORT'        => true,
                'ALLOW_COLUMNS_RESIZE'      => true,
                'ALLOW_HORIZONTAL_SCROLL'   => true,
                'ALLOW_SORT'                => true,
                'ALLOW_PIN_HEADER'          => true,
                'AJAX_OPTION_HISTORY'       => 'Y',
                'TOTAL_ROWS_COUNT'  =>$res->selectedRowsCount()
            ]);?>

        </div>
    </div>

<?
$this->IncludeComponentTemplate();
if($_GET['EXPORT']=='Y'){
    ExportRealtyCsv($grid_options->getUsedColumns(), $columns, $arResult['GRID_DATA']);
}
?>

<script>
    function doPrint(data){
        location.href='https://<?=SITE_SERVER_NAME?>/realty/print?gridID=<?=$gridID?>&iblockID=<?=REALTY_IBLOCK_ID?>&ids='+data;
    }
</script>

