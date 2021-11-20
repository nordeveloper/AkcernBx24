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

if (!CModule::IncludeModule('iblock'))
{
    ShowError('MODULE IBLOCK NOT INSTALLED');
    return;
}

use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;

$grid_id = 'blog';
$grid_options = new GridOptions($grid_id);
$sort = $grid_options->GetSorting(['sort' => ['DATE_CREATE' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
$nav_params = $grid_options->GetNavParams();


// grid footer
$nav = new PageNavigation($grid_id);
$nav->allowAllRecords(true)
    ->setPageSize($nav_params['nPageSize'])
    ->initFromUri();
if ($nav->allRecordsShown()) {
    $nav_params = false;
} else {
    $nav_params['iNumPage'] = $nav->getCurrentPage();
}

// columns array for GRID component
$columns = [];
$columns[] = ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => false];
$columns[] = ['id' => 'NAME', 'name' => 'Title', 'sort' => 'NAME', 'default' => true];
$columns[] = ['id' => 'DATE_CREATE', 'name' => getMessage('DATE_CREATE'), 'sort' => 'DATE_CREATE', 'default' => true];

$filterData['IBLOCK_ID'] = 32;

// get data form iblock
$res = \CIBlockElement::GetList($sort['sort'], $filterData, false, $nav_params,
    ['IBLOCK_ID', 'ID', 'NAME', 'DATE_CREATE', 'CREATED_BY', 'PROPERTY_*']
);


while( $ob = $res->GetNextElement() ) {

    $arElement = $ob->GetFields();
//    $arElement['PROPERTIES'] = $ob->GetProperties();
//    $arData['ID'] = $arElement['ID'];
//    $arData['DATE_CREATE'] = $arElement['DATE_CREATE'];

    $arActions = array();

    $arActions[] = array(
        'text' => getMessage('ADD'),
        'default' => false,
        'onclick' => '/blog/form/?action=add'
    );

    $arActions[] = array(
        'text' => getMessage('EDIT'),
        'default' => false,
        'onclick' => '/blog/form/?action=edit'
    );

    $arActions[] = array(
        'text'    => getMessage('REMOVE'),
        'default' => true,
        'onclick' => 'if(confirm("Точно?")){document.location.href="'.$APPLICATION->getCurPage(false).'?op=delete&id='.$arElement['ID'].'"}'
    );

    $arResult['GRID_DATA'][] = array('data'=>$arElement, 'actions'=>$arActions);
}?>

<h3>Blog</h3>
<?$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
    'GRID_ID' => $grid_id,
    'COLUMNS' => $columns,
    'ROWS' => $arResult['GRID_DATA'],
    'SHOW_ROW_CHECKBOXES' => false,
    'NAV_OBJECT' => $nav,
    'AJAX_MODE' => 'Y',
    'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
    'PAGE_SIZES' =>  [
        ['NAME' => '20', 'VALUE' => '20'],
        ['NAME' => '30', 'VALUE' => '20'],
        ['NAME' => '50', 'VALUE' => '50'],
        ['NAME' => '100', 'VALUE' => '100'],
        ['NAME' => '200', 'VALUE' => '200'],
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
