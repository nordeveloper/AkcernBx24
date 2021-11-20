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

$list_id = 'zone_list';

$grid_options = new GridOptions($list_id);
$sort = $grid_options->GetSorting(['sort' => ['id' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
$nav_params = $grid_options->GetNavParams();

$allcount = Zone::Count();

$nav = new PageNavigation($gridID);
$nav->allowAllRecords(true)
    ->setPageSize($nav_params['nPageSize'])
    ->setRecordCount($allcount)
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

$regions = Region::getList();

$arResult['ITEMS'] = Zone::getList(false, false, $LimitOffset);

foreach ($arResult['ITEMS'] as $arItem){
	$arActions = array();

	$arActions[] = array(
		'text'    => 'Редактировать',
		'default' => false,
		'onclick' => 'showEdit('.$arItem['id'].');',
    );

	$arActions[] = array(
		'text'    => 'Удалить',
		'default' => false,
		'onclick' => 'document.location.href="?remove=Y&id='.$arItem['id'].'"',
    );

    $arItem['edit'] = '<a href="#" onclick="showEdit('.$arItem['id'].');"><i class="glyphicon glyphicon-pencil"></i><a/>';

    $arItem['region'] = $regions[$arItem['region_id']]['name_'.LANGUAGE_ID];
    $arResult['GRID_DATA'][] = array('data'=>$arItem, 'actions'=>$arActions) ;
}

$columns = [];
$columns[] = ['id' => 'id', 'name' => 'ID', 'sort' =>'id', 'default' => true];
$columns[] = ['id' => 'edit', 'name' => 'Edit', 'default' => true];
$columns[] = ['id' => 'sort', 'name' => 'Соротировка', 'sort' =>'sort', 'default' => true];
$columns[] = ['id' => 'active', 'name' => 'Активность', 'sort' =>'active', 'default' => true];
$columns[] = ['id' => 'code', 'name' => 'Зона', 'sort' =>'code', 'default' => true];
$columns[] = ['id' => 'name_am', 'name' => 'Название AM', 'sort' =>'name_am', 'default' => false];
$columns[] = ['id' => 'name_ru', 'name' => 'Название RU', 'sort' =>'name_ru', 'default' => false];
$columns[] = ['id' => 'name_en', 'name' => 'Название EN', 'sort' =>'name_en', 'default' => false];
$columns[] = ['id' => 'region', 'name' => 'Регион', 'sort' =>'region_id', 'default' => false];

if($_GET['remove']=="Y" and $_GET['id']>0){
    Zone::Remove($_GET['id']);
    localRedirect($APPLICATION->getCurPage(false));
}
?>

<h4>Зоны</h4>
<p><a href="#" data-toggle="modal" data-target="#ModalAdd" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> Добавить зону</a></p>

<?
$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
    'GRID_ID' => $list_id,
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
        ['NAME' => '500', 'VALUE' => '500']
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
    'AJAX_OPTION_HISTORY'       => 'Y',
    'TOTAL_ROWS_COUNT'  => $allcount
]);
?>
<script>
	function showEdit(id){
		$.get( "<?=$componentPath?>/edit.php?id="+id, function( data ) {
			$("#ModalEdit .modal-body").html( data );
			$('#ModalEdit').modal('show');
		});	
	};
</script>

<?
$this->IncludeComponentTemplate();