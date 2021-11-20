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

if(intval($arParams['IBLOCK_ID'])>0){
    $IBLOCK_ID = $arParams['IBLOCK_ID'];
}else{
    ShowError('Не правильный инфоблок, В параметрах компонента нужно настроить инфоблок');
    return;
}

use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\UI\Filter\Options as FilterOptions;

\CJSCore::init("sidepanel");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/script.js');

$arGroups = $USER->GetUserGroupArray();
$arGroupAdmins = $arParams['GROUPS_ADMINS'];
$arGroupDirectors = $arParams['GROUPS_DIRECTORS'];
$AdminAccess = array_intersect($arGroups, $arGroupAdmins);
$DirectorAccess = array_intersect($arGroups, $arGroupDirectors);


// grig options and navigation
$gritID = 'requests_removed';

if(intval($_REQUEST['type'])>0){
    $gritID = 'requests_removed'.$_REQUEST['type'];
}

$grid_options = new GridOptions($gritID);
$sort = $grid_options->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
$nav_params = $grid_options->GetNavParams();


/* default filter Porperties */
$defaultFilters = array('CREATED_BY', 'DEAL_TYPE', 'PRICE_FROM', 'CITY', 'ZONE', 'ROOMS_FROM', 'ROOMS_TO', 'FLOOR', 'REPAIRS', 'TOTAL_AREA_FROM', 'TOTAL_AREA_TO', 'USEFUL_AREA_FROM', 'USEFUL_AREA_TO', 'GROUND_AREA');
$defaultFilelds = array('CREATED_BY', 'CLIENTID', 'DEAL_TYPE', 'PRICE_FROM', 'PRICE_TO', 'ZONE', 'STREET', 'ROOMS', 'FLOOR', 'ROOMS_FROM', 'ROOMS_TO', 'TOTAL_AREA_FROM', 'TOTAL_AREA_TO');
$customFilterList = array('regions', 'city', 'zones', 'streets', 'floor', 'floors');
$multiFilelds = array('ZONE', 'FLOOR', 'FLOORS', 'REPAIRS', 'BUILDING_TYPE');


// users for filter form
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
    ['id' => 'DATE_CREATE', 'name' => getMessage('DATE_CREATE'), 'type'=>'date', 'default' =>false],
    ['id' => 'TIMESTAMP_X', 'name' => getMessage('TIMESTAMP_X'), 'type'=>'date', 'default' =>false],
];


$propertyList = array();


/* get Property list for Filter */

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
$columns[] = ['id' => 'DATE_CREATE', 'name' => getMessage('DATE_CREATE'), 'sort' =>'NAME', 'default' => true];
$columns[] = ['id' => 'TIMESTAMP_X', 'name' => getMessage('TIMESTAMP_X'), 'sort' =>'TIMESTAMP_X', 'default' => false];
$columns[] = ['id' => 'CREATED_BY', 'name' => getMessage('CREATED_BY'), 'sort' =>'CREATED_BY', 'default' => true];


foreach ($propertyList as $arProp){

    $defField = false;
    if(in_array($arProp['CODE'], $defaultFilelds)) $defField = true;

    $label = false;    
    if(LANGUAGE_ID=='en'){$label = $arProp['HINT'];} else {$label = $arProp['NAME'];} 
    $columns[] = ['id' => $arProp['CODE'], 'name' => $label, 'sort' =>'PROPERTY_'.$arProp['CODE'], 'default' => $defField];
}


// set defaut filter params
$filterOption = new FilterOptions($gritID);
$filterData = $filterOption->getFilter([]);

$arFilter['IBLOCK_ID'] = $IBLOCK_ID;
$arFilter['!ACTIVE'] ="Y";
$arFilter['SHOW_NEW'] = "Y";

if($_REQUEST['type']){
    $arFilter['PROPERTY_REALTY_TYPE'] = intval($_REQUEST['type']);
}


//filter customizing
$elementFilter = ElementFilter($filterData, $arFilter);


//grid pagination
$nav = new PageNavigation($gritID);
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
    $arData['NAME'] = '<a href="'.$arParams['LIST_URL'].'edit/?ID='.$arData['ID'].'&type='.$arElement['PROPERTIES']['REALTY_TYPE']['VALUE_ENUM_ID'].'">'.$arElement['NAME'].'</a>';
    $arData['DATE_CREATE'] = $arElement['DATE_CREATE'];
    $arData['CREATED_BY'] = '<a class="user-link" target="_blank" href="/company/personal/user/'.$arElement['USER']['ID'].'/">'.$arElement['USER']['FULL_NAME'].'</a>';
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
       
        if( ($key=='CLIENTID') and $arProp['VALUE']>0 and $AdminAccess ){
            $arProp['VALUE'] = '<a target="_blank" href="/crm/contact/details/'.$arProp['VALUE'].'/">'.$arProp['VALUE'].'</a>';           
        }

        //change labels for current language
        if(LANGUAGE_ID=='en' and $arProp['PROPERTY_TYPE']=='L' ){
            $arData[$key] = $arProp['VALUE_XML_ID'];
        }else{$arData[$key]= $arProp['VALUE']; }        

        if( is_array($arData[$key]) ){
            $arData[$key] =  implode(',', $arData[$key]);
        };
        
    }

    $arActions = array();

    if( !empty($AdminAccess) ){
        $arActions[] = array(
            'text'    => getMessage('RESTORE'),
            'default' => true,
            'onclick' => 'if(confirm("Вы уверениы что хотите восстановить?")){document.location.href="?op=restore&id='.$arData['ID'].'"}'
        );
    }

    if( !empty($AdminAccess) ){
        $arActions[] = array(
            'text'    => getMessage('REMOVE'),
            'default' => true,
            'onclick' => 'if(confirm("'.getMessage('ARE_YOU_SURE').'")){document.location.href="?op=remove&id='.$arData['ID'].'"}'
        );
    }

    $arResult['GRID_DATA'][] = array('data'=>$arData, 'actions'=>$arActions) ;
}



if( !empty($AdminAccess) and $_GET['op']=='restore' and $_GET['id']>0){
    $ELEMENT_ID = intval($_GET['id']);
    $el = new CIBlockElement;
    $arUpdate['ACTIVE'] = 'Y';
    $res = $el->Update($ELEMENT_ID, $arUpdate);

    localRedirect($APPLICATION->getCurPage(false));
}

if( !empty($AdminAccess) and $_GET['op']=='remove' and $_GET['id']>0) {

    $RemRes = RemoveElement($_GET['id'], $IBLOCK_ID);
    if( !empty($RemRes) ){
        localRedirect($APPLICATION->getCurPage(false));
    }
}
?>

<div class="row">
  <div class="col-sm-12">

    <p class="h3"><?=getMessage('COMPONENT_TITLE')?></p>

    <?$APPLICATION->IncludeComponent('bitrix:main.ui.filter', '',
        ['FILTER_ID' => $gritID,
        'GRID_ID' => $gritID,
        'FILTER' => $arUiFilter,
        'ENABLE_LIVE_SEARCH' => true,
        'ENABLE_LABEL' => true]
    );?>

      <button type="button" class="btn btn-print" onclick="javascript:window.print()"><i class="glyphicon glyphicon-print"></i></button>

    <?$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
        'GRID_ID' => $gritID,
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
        'AJAX_OPTION_HISTORY'       => 'N',
        'TOTAL_ROWS_COUNT'  =>$res->selectedRowsCount()
    ]);?>

</div>
</div>
<?
$this->IncludeComponentTemplate();
