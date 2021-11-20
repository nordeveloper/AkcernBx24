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

$gridID = 'regions_list';
$arResult['ITEMS'] = Region::getList();


foreach ($arResult['ITEMS'] as $arItem){
    $arActions = array();

//    $arActions[] = array(
//        'text'    => 'Редактировать',
//        'default' => false,
//        'onclick' => 'showEdit('.$arItem['id'].');',
//    );

    $arActions[] = array(
        'text'    => 'Удалить',
        'default' => false,
        'onclick' => 'document.location.href="?remove=Y&id='.$arItem['id'].'"',
    );

    $arItem['edit'] = '<a href="#" onclick="showEdit('.$arItem['id'].');"><i class="glyphicon glyphicon-pencil"></i><a/>';

    $arResult['GRID_DATA'][] = array('data'=>$arItem, 'actions'=>$arActions) ;
}


$columns = [];
$columns[] = ['id' => 'id', 'name' => 'ID', 'default' => true];
$columns[] = ['id' => 'edit', 'name' => 'Edit', 'default' => true];
$columns[] = ['id' => 'name_am', 'name' => 'Название AM', 'default' => true];
$columns[] = ['id' => 'name_ru', 'name' => 'Название Ru', 'default' => true];
$columns[] = ['id' => 'name_en', 'name' => 'Название EN', 'default' => true];

if($_GET['remove']=="Y" and intval($_GET['id'])>0){
    Region::Remove($_GET['id']);
    localRedirect($APPLICATION->getCurPage(false));
}
?>

<h4>Регионы</h4>
<p><button data-toggle="modal" data-target="#ModalAdd" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> Добавить регион</button></p>

<?
$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
    'GRID_ID' => $gridID,
    'COLUMNS' => $columns,
    'ROWS' => $arResult['GRID_DATA'],
    'SHOW_ROW_CHECKBOXES' => false,
//    'NAV_OBJECT' => $nav,
    'AJAX_MODE' => 'Y',
    'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
    'AJAX_OPTION_JUMP'          => 'N',
    'SHOW_CHECK_ALL_CHECKBOXES' => false,
    'SHOW_ROW_ACTIONS_MENU'     => true,
    'SHOW_GRID_SETTINGS_MENU'   => true,
    'SHOW_NAVIGATION_PANEL'     => false,
    'SHOW_PAGINATION'           => false,
    'SHOW_SELECTED_COUNTER'     => true,
    'SHOW_TOTAL_COUNTER'        => false,
    'SHOW_PAGESIZE'             => false,
    'SHOW_ACTION_PANEL'         => false,
    'ALLOW_COLUMNS_SORT'        => false,
    'ALLOW_COLUMNS_RESIZE'      => true,
    'ALLOW_HORIZONTAL_SCROLL'   => true,
    'ALLOW_SORT'                => true,
    'ALLOW_PIN_HEADER'          => true,
    'AJAX_OPTION_HISTORY'       => 'N',
    //'TOTAL_ROWS_COUNT'=>$dbres->SelectedRowsCount()
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