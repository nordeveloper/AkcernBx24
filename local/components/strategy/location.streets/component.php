<?php
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

use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;

$gridID = 'street_list';

$grid_options = new GridOptions($gridID);
$sort = $grid_options->GetSorting(['sort' => ['id' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
$nav_params = $grid_options->GetNavParams();



/* making filter array */
$arUiFilter = [
    ['id' => 'id', 'name' => 'id', 'type'=>'text', 'default' => true],
    ['id' => 'name_am', 'name' => 'Название на армянском', 'type'=>'text', 'default' => true],
    ['id' => 'name_en', 'name' => 'Название на английском', 'type'=>'text', 'default' => true],
    ['id' => 'zone', 'name' => 'Зона', 'type'=>'list', 'items'=>zonesList(), 'default' => true],
    ['id' => 'region_id', 'name' => 'Регион', 'type'=>'list', 'items'=>regionsList(), 'default' => true],
];


//array filter for get List filtring
$filterOption = new Bitrix\Main\UI\Filter\Options($gridID);
$filterData = $filterOption->getFilter([]);

$arFilter = array();

foreach ($filterData as $fieldId => $fieldValue)
{
    if ((is_array($fieldValue) && empty($fieldValue)) || (is_string($fieldValue) && strlen($fieldValue) <= 0))
    { continue; }

    if(is_string($fieldValue)){
        $fieldValue = trim($fieldValue);
    }

    if($fieldId=='FIND'){
        $fieldId = 'name_ru';
    }

    $arFilter[$fieldId] = $fieldValue;
}

//$allcount = Street::Count();

$nav = new PageNavigation($gridID);
$nav->allowAllRecords(true)
    ->setPageSize($nav_params['nPageSize'])
    ->initFromUri();
if ($nav->allRecordsShown()) {
    $nav_params = false;
    $Limitoffset = '';
} else {
    $navcount = $nav_params['nPageSize'];
    $offset = $nav->getOffset();
    $nav_params['iNumPage'] = $nav->getCurrentPage();
    $LimitOffset = " $offset, $navcount";
}

if($sort){
    $s = array_keys($sort['sort']);
    $orderby = $s[0].' '.$sort['sort'][$s[0]];
}


$arResult = Street::getList($arFilter, $orderby, $LimitOffset);

//dump($arResult);
$nav->setRecordCount($arResult['COUNT']);


foreach ($arResult['ITEMS'] as $arItem){

	$arActions = array();

//	$arActions[] = array(
//		'text'    => 'Редактировать',
//		'default' => false,
//		'onclick' => 'showEdit('.$arItem['id'].');',
//    );

	$arActions[] = array(
		'text'    => 'Удалить',
		'default' => false,
		'onclick' => 'document.location.href="?remove=Y&id='.$arItem['id'].'"',
    );

    $arItem['region'] = '';
	if($arItem['region_id']>0){
        $region = Region::getById($arItem['region_id']);
        $arItem['region'] = $region['name_'.LANGUAGE_ID];
    }

    $arItem['city'] = '';
    if($arItem['city_id']>0){
        $region = City::getById($arItem['city_id']);
        $arItem['city'] = $region['name_'.LANGUAGE_ID];
    }

    $arItem['edit'] = '<button type="button" class="btn" onclick="showEdit('.$arItem['id'].');"><i class="glyphicon glyphicon-pencil"></i></button>';

    $arResult['GRID_DATA'][] = array('data'=>$arItem, 'actions'=>$arActions) ;
}

$columns = [];
$columns[] = ['id' => 'id', 'name' => 'id', 'sort' =>'id', 'default' => true];
$columns[] = ['id' => 'edit', 'name' => 'Edit', 'default' => true];
$columns[] = ['id' => 'name_am', 'name' => 'Название AM', 'sort' =>'name_am', 'default' => true];
$columns[] = ['id' => 'name_ru', 'name' => 'Название Ru', 'sort' =>'name_ru', 'default' => true];
$columns[] = ['id' => 'name_en', 'name' => 'Название EN', 'sort' =>'name_en', 'default' => true];

$columns[] = ['id' => 'zone_code', 'name' => 'Зона', 'default' => true];
$columns[] = ['id' => 'city', 'name' => 'Город/Село', 'sort' =>'city_id', 'default' => true];
$columns[] = ['id' => 'region', 'name' => 'Регион', 'sort' =>'region_id', 'default' => true];

if($_GET['remove']=="Y" and $_GET['id']>0){
    Street::Remove($_GET['id']);
    localRedirect($APPLICATION->getCurPage(false));
}

?>

<h4>Улицы</h4>
<p><a href="" data-toggle="modal" data-target="#ModalAdd" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> Добавить улицу</a></p>

<?$APPLICATION->IncludeComponent('bitrix:main.ui.filter', '', [
    'FILTER_ID' => $gridID,
    'GRID_ID' => $gridID,
    'FILTER' => $arUiFilter,
    'ENABLE_LIVE_SEARCH' => true,
    'ENABLE_LABEL' => true
]);?>

<?
$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
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
        ['NAME' => '100', 'VALUE' => '100'],
        ['NAME' => '200', 'VALUE' => '200'],
        ['NAME' => '500', 'VALUE' => '500'],
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
    'SHOW_ACTION_PANEL'         => false,
    'ALLOW_COLUMNS_SORT'        => true,
    'ALLOW_COLUMNS_RESIZE'      => true,
    'ALLOW_HORIZONTAL_SCROLL'   => true,
    'ALLOW_SORT'                => true,
    'ALLOW_PIN_HEADER'          => true,
    'AJAX_OPTION_HISTORY'       => 'Y',
    'TOTAL_ROWS_COUNT'  => $arResult['COUNT']
]);?>

<script>
	function showEdit(id){
		$.get( "<?=$componentPath?>/edit.php?id="+id, function( data ) {
			$("#EditModal .modal-body" ).html( data );
			$('#EditModal').modal('show');
		});	
	};
</script>

<?
$this->IncludeComponentTemplate();