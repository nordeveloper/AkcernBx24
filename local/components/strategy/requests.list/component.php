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
?>

<?php

use \Bitrix\Main\Loader;
if(!Loader::includeModule('iblock')) {
    ShowError('MODULE IBLOCK NOT INSTALLED');
    return;
}

if(intval(CLIENT_REQUEST_IBLOCKID)>0){
    $IBLOCK_ID = CLIENT_REQUEST_IBLOCKID;
}else{
    ShowError('Не правильный инфоблок, В параметрах компонента нужно настроить инфоблок');
    return;
}

use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\UI\Filter\Options as FilterOptions;


\CJSCore::init("sidepanel");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/script.js');


// User in Group
$arGroups = $USER->GetUserGroupArray();

$arGroupAdmins = $arParams['GROUPS_ADMINS'];
$arGroupDirectors = $arParams['GROUPS_DIRECTORS'];
$AdminAccess = array_intersect($arGroups, $arGroupAdmins);
$DirectorAccess = array_intersect($arGroups, $arGroupDirectors);

$arCurUser = getUser($USER->getID());


// grid options and navigation
$gridID = 'requests_list';

if(intval($_REQUEST['type'])>0){
    $gridID = 'requests_list'.$_REQUEST['type'];
}

$grid_options = new GridOptions($gridID);
$sort = $grid_options->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
$nav_params = $grid_options->GetNavParams();


/* default filter Porperties*/
$defaultFilters = array('CREATED_BY', 'CLIENT_CODE', 'DEAL_TYPE', 'PRICE_FROM', 'CITY', 'ZONE', 'ROOMS_FROM', 'ROOMS_TO', 'FLOOR', 'REPAIRS', 'TOTAL_AREA_FROM', 'TOTAL_AREA_TO', 'USEFUL_AREA_FROM', 'USEFUL_AREA_TO', 'GROUND_AREA');
$defaultFields= array('CREATED_BY', 'CLIENT_CODE', 'DEAL_TYPE', 'PRICE_FROM', 'PRICE_TO', 'ZONE', 'STREET', 'ROOMS', 'FLOOR', 'ROOMS_FROM', 'ROOMS_TO', 'TOTAL_AREA_FROM', 'TOTAL_AREA_TO');
$customFilterList = array('regions', 'city', 'zones', 'streets', 'floor', 'floors');
$multiFilelds = array('REALTY_TYPE', 'DEAL_TYPE', 'FLOOR', 'FLOORS', 'REPAIRS', 'BUILDING_TYPE');



$arUsers = getUsers();
foreach ($arUsers as $arUser){
    $arUsersList[$arUser['ID']] = $arUser['NAME'].' '.$arUser['LAST_NAME'];
}

$arDepartments =  getDepartamentList();
foreach ($arDepartments as $Department){
    $arDepartmentList[$Department['XML_ID']] = $Department['NAME'];
}

/* making filter array */
$arUiFilter = [
    ['id' => 'NAME', 'name' => getMessage('IDENTIFICATOR'), 'type'=>'text', 'default' => true],
    ['id' => 'CREATED_BY', 'name' => getMessage('CREATED_BY'), 'type'=>'list', 'items'=>$arUsersList, 'default' =>true],
    ['id' => 'PROPERTY_DEPARTMENT', 'name' => getMessage('DEPARTMENT'), 'type'=>'list', 'items'=>$arDepartmentList, 'default' =>true],
    ['id' => 'DATE_CREATE', 'name' => getMessage('DATE_CREATE'), 'type'=>'date', 'default' =>true],
    ['id' => 'TIMESTAMP_X', 'name' => getMessage('TIMESTAMP_X'), 'type'=>'date', 'default' =>false]
];


$propertyList = array();


if(intval($_GET['type'])>0){
    $skipeFields = $arParams['PROPERTY_'.$_GET['type']];
}


/* get Property list for FIRLTER*/

$resProps = Bitrix\Iblock\PropertyTable::getList(array(
    'filter' => array('IBLOCK_ID' => $IBLOCK_ID, "ACTIVE"=>"Y", "FILTRABLE"=>'Y'), 'order'=>array('SORT'=>'ASC')
));

while ($arProperty = $resProps->Fetch())
{
    $arProperty['id'] = 'PROPERTY_'.$arProperty['CODE'];
    if(LANGUAGE_ID=='en'){$arProperty['name'] = $arProperty['HINT'];} else {$arProperty['name'] = $arProperty['NAME'];}

    if($arProperty["PROPERTY_TYPE"]=="N") {$arProperty['type'] = 'number';}
    if($arProperty["PROPERTY_TYPE"]=="S") {$arProperty['type'] = 'string';}

    if( in_array($arProperty['CODE'], $defaultFilters) ) {
        $arProperty['default'] = true;
    }

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
				$arProperty["items"][$arPropertyEnum["ID"]]= html_entity_decode($arPropertyEnum['VALUE']);
			}					
		}

        $arProperty['type'] = 'list';
	}

    if(in_array( $arProperty['CODE'], $multiFilelds ) ){
        $arProperty['params'] = ['multiple' => 'Y'];
    }

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

$columns = [];
$columns[] = ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => false];
$columns[] = ['id' => 'NAME', 'name' => getMessage('IDENTIFICATOR'), 'sort' =>'NAME', 'default' => true];
$columns[] = ['id' => 'DATE_CREATE', 'name' => getMessage('DATE_CREATE'), 'sort' =>'DATE_CREATE', 'default' => true];
$columns[] = ['id' => 'TIMESTAMP_X', 'name' => getMessage('TIMESTAMP_X'), 'sort' =>'TIMESTAMP_X', 'default' => false];
$columns[] = ['id' => 'CREATED_BY', 'name' => getMessage('CREATED_BY'), 'sort' =>'CREATED_BY', 'default' => true];

foreach ($propertyList as $arProp){
    $defField = false;
    if(in_array($arProp['CODE'], $defaultFields)) $defField = true;

    $label = false;
    if(LANGUAGE_ID=='en'){$label = $arProp['HINT'];} else {$label = $arProp['NAME'];}
    $columns[] = ['id' => $arProp['CODE'], 'name' => $label, 'sort' => 'PROPERTY_'.$arProp['CODE'], 'default' => $defField];
}


// set defaut filter params
$filterOption = new FilterOptions($gridID);
$filterData = $filterOption->getFilter([]);

$arFilter['IBLOCK_ID'] = $IBLOCK_ID;
$arFilter['ACTIVE'] ="Y";
$arFilter['SHOW_NEW'] = "Y";

if($_REQUEST['type']){
    $arFilter['PROPERTY_REALTY_TYPE'] = intval($_REQUEST['type']);
}


//filter customizing
$elementFilter = ElementFilter($filterData, $arFilter);


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


//get data from requests Iblock with filter;


$res = \CIBlockElement::GetList($sort['sort'], $elementFilter, false, $nav_params,
    ['IBLOCK_ID', 'ID', 'NAME', 'DATE_CREATE', 'CREATED_BY', 'TIMESTAMP_X', 'PROPERTY_*']
);

$nav->setRecordCount($res->selectedRowsCount());


while( $ob = $res->GetNextElement() ) {

    $arElement = $ob->GetFields();
    $arElement['PROPERTIES'] = $ob->GetProperties();

    $arElement['USER'] = getUser($arElement['CREATED_BY']);

    $arData['ID'] = $arElement['ID'];

    $arData['NAME'] = '<a href="/realty/client-requests/edit/?ID='.$arData['ID'].'&type='.$arElement['PROPERTIES']['REALTY_TYPE']['VALUE_ENUM_ID'].'&page=page-'.$nav_params['iNumPage'].'">'.$arElement['NAME'].'</a>';
    $arData['DATE_CREATE'] = $arElement['DATE_CREATE'];
    $arData['CREATED_BY'] = $arElement['USER']['FULL_NAME'];
    $arData['CREATED_BY'] = '<a class="user-link" target="_blank" href="/company/personal/user/'.$arElement['USER']['ID'].'/">'.$arElement['USER']['FULL_NAME'].'</a>';
    $arData['TIMESTAMP_X'] = $arElement['TIMESTAMP_X'];

    foreach($arElement['PROPERTIES'] as $key=> $arProp){

        if($arProp['PROPERTY_TYPE']=='F') continue;

        if( ($key=='CLIENT_CODE') and !empty($arProp['VALUE']) ){
            if(!empty($AdminAccess)){
                $arProp['VALUE'] = '<a target="_blank" class="contact-link" href="/crm/contact/details/'.$arElement['PROPERTIES']['CLIENTID']['VALUE'].'/">'.$arProp['VALUE'].'</a>';
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


    $arActions = array();

    $arActions[] = array(
        'text'    => getMessage('VIEW_MATCHES'),
        'default' => false,
        'onclick' => 'document.location.href="/realty/client-requests/matches/?id='.$arData['ID'].'"',
    );

    $arActions[] = array(
        'text'    => getMessage('EDIT'),
        'default' => false,
        'onclick' => 'document.location.href="/realty/client-requests/edit/?ID='.$arData['ID'].'&type='.$arElement['PROPERTIES']['REALTY_TYPE']['VALUE_ENUM_ID'].'&page=page-'.$nav_params['iNumPage'].'"',
    );


    $SelfAccess = false;

    if(!empty($DirectorAccess) and $arElement['PROPERTIES']['DEPARTMENT']['VALUE'] == $arCurUser['DEPARTMENT']
        OR ( $arElement['CREATED_BY'] == $USER->getID() )
    ){
        $SelfAccess = 'Y';
    }

    if(!empty($AdminAccess) OR $SelfAccess=='Y'){
        $arActions[] = array(
            'text'    => getMessage('REMOVE'),
            'default' => true,
            'onclick' => 'if(confirm("'.getMessage('ARE_YOU_SURE').'")){document.location.href="?op=remove&id='.$arData['ID'].'"}'
        );
    }

    $arResult['GRID_DATA'][] = array('data'=>$arData, 'actions'=>$arActions) ;
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
?>

<div class="row">
  <div class="col-sm-12 grid-content">

    <p class="h3"><?=getMessage('COMPONENT_TITLE')?></p>

    <?$APPLICATION->IncludeComponent('bitrix:main.ui.filter', '',
        ['FILTER_ID' => $gridID,
        'GRID_ID' => $gridID,
        'FILTER' => $arUiFilter,
        'ENABLE_LIVE_SEARCH' => true,
        'ENABLE_LABEL' => true]
    );?>

    <? if( !empty($AdminAccess) or !empty($DirectorAccess) ):?>
        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
            <div class="btn-group btn-add pull-right" role="group">
                <button id="btnGroupDrop1" type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="glyphicon glyphicon-plus"></i> <?=getMessage('BTN_ADD')?>
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <a class="dropdown-item" href="<?=$arParams['LINK_EDIT']?>edit/?type=151"><?=getMessage('LINK_APARTMENT')?></a>
                    <a class="dropdown-item" href="<?=$arParams['LINK_EDIT']?>edit/?type=152"><?=getMessage('LINK_HOME')?></a>
                    <a class="dropdown-item" href="<?=$arParams['LINK_EDIT']?>edit/?type=153"><?=getMessage('LINK_MANSION')?></a>
                    <a class="dropdown-item" href="<?=$arParams['LINK_EDIT']?>edit/?type=154"><?=getMessage('LINK_DACHA')?></a>
                    <a class="dropdown-item" href="<?=$arParams['LINK_EDIT']?>edit/?type=155"><?=getMessage('LINK_OFFICE')?></a>
                    <a class="dropdown-item" href="<?=$arParams['LINK_EDIT']?>edit/?type=156"><?=getMessage('LINK_RESTAURANT')?></a>
                    <a class="dropdown-item" href="<?=$arParams['LINK_EDIT']?>edit/?type=157"><?=getMessage('LINK_SHOP')?></a>
                    <a class="dropdown-item" href="<?=$arParams['LINK_EDIT']?>edit/?type=159"><?=getMessage('LINK_LAND')?></a>
                    <a class="dropdown-item" href="<?=$arParams['LINK_EDIT']?>edit/?type=160"><?=getMessage('LINK_AUTOSERVICE')?></a>
                    <a class="dropdown-item" href="<?=$arParams['LINK_EDIT']?>edit/?type=161"><?=getMessage('LINK_HOTEL')?></a>
                    <a class="dropdown-item" href="<?=$arParams['LINK_EDIT']?>edit/?type=162"><?=getMessage('LINK_BARBERSHOP')?></a>
                    <a class="dropdown-item" href="<?=$arParams['LINK_EDIT']?>edit/?type=226"><?=getMessage('LINK_OTHER')?></a>
                </div>
            </div>
        </div>
    <? endif ?>

      <button type="button" class="btn btn-print" onclick="javascript:window.print()"><i class="glyphicon glyphicon-print"></i></button>

    <?$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
        'GRID_ID' => $gridID,
        'COLUMNS' => $columns,
        'ROWS' => $arResult['GRID_DATA'],
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
        'SHOW_ROW_CHECKBOXES' => true,
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

    <script>
        function doPrint(data){
            location.href='https://<?=SITE_SERVER_NAME?>/realty/print?gridID=<?=$gridID?>&iblockID=<?=CLIENT_REQUEST_IBLOCKID?>&ids='+data;
            target = "_blank";
        }
    </script>

<?
$this->IncludeComponentTemplate();
