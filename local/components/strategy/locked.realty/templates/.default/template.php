<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
$this->setFrameMode(false);
//$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/js/chosen/chosen.css');
//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/chosen/chosen.jquery.min.js');

$form = new Forms();

use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;

$gridID = 'LockedRealtyList';
$arUiFilter = array();
$columns = array();

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

//making array filter for Form
$arUiFilter = [
    ['id' => 'ID', 'name' => 'ID', 'type'=>'string', 'default' => false],
    ['id' => 'REALTY_CODE', 'name'=>getMessage('REALTY_CODE'), 'type'=>'string', 'default' => true],
    ['id' => 'USER_ID', 'name' => getMessage('REALTOR'), 'type'=>'list', 'items'=>$arResult['arUsersList'], 'default' =>true],
    ['id' => 'DATE_CREATED', 'name' => getMessage('DATE'), 'type'=>'date', 'default' =>true],
];
?>

<h3 class="text-center page-title"><?=getMessage('COMPONENT_TITLE')?></h3>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="lockedrealty-tab" data-toggle="tab" href="#lockedrealty" role="tab" aria-controls="lockedrealty" aria-selected="true">Блокировка по коду</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/realty/blocked/">Блокировка по адресу</a>
    </li>
</ul>

<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="lockedrealty" role="tabpanel" aria-labelledby="lockedrealty-tab">

        <div class="info-box">
            <?php
            if($_GET['success']=='Y'){
                echo '<p class="alert alert-success">Блокировка успешно добавлен</p>';

                echo '<script>
                setTimeout(function() {
                   location.href="/realty/blocked/locked-realty";
                },2000);
               </script>';

            }else{
                if(!empty($arResult['MESSAGE']['ERROR'])) echo '<p class="alert alert-danger">'.$arResult['MESSAGE']['ERROR'].'</p>';
            }
            ?>
        </div>

        <button class="btn btn-info btn-add" data-toggle="modal" data-target="#ModalAdd"><i class="glyphicon glyphicon-plus"></i> <?=GetMessage('BTN_ADD')?></button>

        <?$APPLICATION->IncludeComponent('bitrix:main.ui.filter', '',
            ['FILTER_ID' => $gridID,
                'GRID_ID' => $gridID,
                'FILTER' => $arUiFilter,
                'ENABLE_LIVE_SEARCH' => true,
                'ENABLE_LABEL' => true]
        );?>

        <?
        $columns[] = ['id' =>'ID', 'name' =>'ID', 'sort' =>'ID', 'default'=>true];
        $columns[] = ['id' =>'ACTIVE', 'name' =>getMessage('ACTIVE'), 'sort' =>'ACTIVE', 'default'=>true];
        $columns[] = ['id' =>'USER_NAME', 'name' =>getMessage('REALTOR'), 'sort' =>'USER_ID', 'default'=>true];
        $columns[] = ['id' =>'REALTY_CODE', 'name' =>getMessage('REALTY_CODE'), 'sort' =>'REALTY_CODE', 'default'=>true];
        //$columns[] = ['id' =>'DEAL_TYPE', 'name' =>getMessage('DEAL_TYPE'), 'sort' =>'REALTY_ID', 'default'=>true];
        $columns[] = ['id' =>'DATE_CREATED', 'name' =>getMessage('DATE'), 'sort' =>'DATE', 'default'=>true];
        ?>

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
            'ALLOW_SORT'                => false,
            'ALLOW_PIN_HEADER'          => true,
            'AJAX_OPTION_HISTORY'       => 'N'
        ]);?>

    </div>
</div>


<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="ModalAdd" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Блокировка по коду</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" class="form-locked">
                    <div class="form-group col-md-12">
                        <label>Риелтор</label>
                        <?$APPLICATION->IncludeComponent("bitrix:main.user.selector", "",
                            array("INPUT_NAME"=>'user_id',
                                "LIST"=>array(),
                                "BUTTON_SELECT_CAPTION"=>getMessage('CHANGE'),
                                "SELECTOR_OPTIONS" => array('disableLast' => 'Y')
                            ),  false);?>
                    </div>

                    <div class="form-group col-md-12">
                        <input type="text" class="form-control realty-code"  name="realty_code" placeholder="Код недвижимости">
                    </div>
                    <div class="form-group col-md-12">
                        <button type="submit" class="btn btn-success" name="BTN_ADD" value="ADD"><i class="glyphicon glyphicon-floppy-disk"></i> <?=getMessage('SAVE')?></button>
                    </div>
                </form>


            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {

        $('.form-locked').submit(function () {

            if($('.realty-code').val()==''){
                $('.realty-code').addClass('read-border');
                return false;
            }else{
                $('.realty-code').removeClass('read-border');
                return true;
            }

        });

    });
</script>