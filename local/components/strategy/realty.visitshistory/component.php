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

use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;

if (!CModule::IncludeModule('iblock'))
{
	ShowError('MODULE IBLOCK NOT INSTALLED');
	return;
}

// User in Group
$arGroups = $USER->GetUserGroupArray();

$arGroupAdmins = array(1,13);
$AdminAccess = array_intersect($arGroups, $arGroupAdmins);

$list_id = 'visits_list';

$grid_options = new GridOptions($list_id);
$sort = $grid_options->GetSorting(['sort' => ['DATE_CREATE' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
$nav_params = $grid_options->GetNavParams();


//making array filter for Form
$arUiFilter = [
    ['id' => 'NAME', 'name' => 'Код недвижимости', 'type'=>'text', 'default' => true],
    ['id' => 'DATE_CREATE', 'name' => getMessage('DATE_CREATE'), 'type'=>'date', 'default' =>true],
];


// get iblock properties list
$arPropertyList = array();
$rsIBLockPropertyList = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "FILTRABLE"=>'Y', "IBLOCK_ID"=>VISITS_HISTORY_IBLOCKID));

while ($arProperty = $rsIBLockPropertyList->GetNext())
{
	// get list of property enum values
	if ($arProperty["PROPERTY_TYPE"] == "L")
	{
		$rsPropertyEnum = CIBlockProperty::GetPropertyEnum($arProperty["ID"]);
		$arProperty["ENUM"] = array();
		while ($arPropertyEnum = $rsPropertyEnum->GetNext())
		{
			$arProperty["ENUM"][$arPropertyEnum["ID"]] = $arPropertyEnum['VALUE'];
		}
	}
	$arProperties["PROPERTY_ENUM_LIST"][$arProperty["ID"]] = $arProperty;
    $arPropertyList[] = $arProperty;

    $filterType = 'string';
    if($arProperty['PROPERTY_TYPE']=='L'){
        $filterType = 'list';
    }

    $label = false;
    if(LANGUAGE_ID=='en'){$label = $arProperty['HINT'];} else {$label = $arProperty['NAME'];}
    $arUiFilter[$arProperty['ID']]=array('id'=>'PROPERTY_'.$arProperty['CODE'], 'name'=>$label, 'type'=>$filterType, 'default' =>true);

    if($arProperty['PROPERTY_TYPE']=='L')
        $arUiFilter[$arProperty['ID']]['items'] = $arProperty['ENUM'];

}


//array filter for get List filtring
$filterOption = new Bitrix\Main\UI\Filter\Options($list_id);
$filterData = $filterOption->getFilter([]);

$arFilter['IBLOCK_ID'] = VISITS_HISTORY_IBLOCKID;
$arFilter['ACTIVE'] ="Y";
$arFilter['SHOW_NEW'] = "Y";

//filter customizing for form to fields
foreach ($filterData as $fieldId => $fieldValue)
{

    if ((is_array($fieldValue) && empty($fieldValue)) || (is_string($fieldValue) && strlen($fieldValue) <= 0))
    { continue; }

    if(is_string($fieldValue)){
        $fieldValue = trim($fieldValue);
    }

    if (substr($fieldId, -5) == "_from")
    {
        $realFieldId = substr($fieldId, 0, strlen($fieldId)-5);
        if (substr($realFieldId, -2) == "_1")
        {
            $arFilter[$realFieldId] = $fieldValue;
        }
        else
        {
            if (!empty($filterData[$realFieldId."_numsel"]) && $filterData[$realFieldId."_numsel"] == "more")
                $filterPrefix = ">";
            else
                $filterPrefix = ">=";
            $arFilter[$filterPrefix.$realFieldId] = trim($fieldValue);
        }
    }
    elseif (substr($fieldId, -3) == "_to")
    {

        $realFieldId = substr($fieldId, 0, strlen($fieldId)-3);
        if (substr($realFieldId, -2) == "_1")
        {
            $realFieldId = substr($realFieldId, 0, strlen($realFieldId)-2);
            $arFilter[$realFieldId."_2"] = $fieldValue;
        }
        else
        {
            if (!empty($filterData[$realFieldId."_numsel"]) && $filterData[$realFieldId."_numsel"] == "less")
                $filterPrefix = "<";
            else
                $filterPrefix = "<=";
            $arFilter[$filterPrefix.$realFieldId] = trim($fieldValue);
        }
    }
    else
    {
        if($fieldId=='FIND'){
            $fieldId = '%NAME';
        }

        $arFilter[$fieldId] = $fieldValue;
    }
}


//foreach($arProperties["PROPERTY_ENUM_LIST"] as $forFilter){
//	$filterData['PROPERTY_'.$forFilter['CODE']] = $filterData[$forFilter['CODE']];
//}



// grid footer
$nav = new PageNavigation($list_id);
$nav->allowAllRecords(true)
    ->setPageSize($nav_params['nPageSize'])
    ->initFromUri();
if ($nav->allRecordsShown()) {
    $nav_params = false;
} else {
    $nav_params['iNumPage'] = $nav->getCurrentPage();
}


// get data form iblock

//dump($arFilter);

$res = \CIBlockElement::GetList($sort['sort'], $arFilter, false, $nav_params,
	['IBLOCK_ID', 'ID', 'NAME', 'DATE_CREATE', 'CREATED_BY', 'PROPERTY_*']
);

$nav->setRecordCount($res->selectedRowsCount());

// columns array for GRID component
$columns = [];
$columns[] = ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => false];
$columns[] = ['id' => 'REALTYID', 'name' => 'Код недвижимости', 'sort' => 'PROPERTY_REALTYID', 'default' => true];
$columns[] = ['id' => 'DATE_CREATE', 'name' => getMessage('DATE_CREATE'), 'sort' => 'DATE_CREATE', 'default' => true];

foreach ($arPropertyList as $arProp){
    $label = '';
    if(LANGUAGE_ID=='en'){$label = $arProp['HINT'];} else {$label = $arProp['NAME'];}
    $columns[] = ['id' => $arProp['CODE'], 'name' => $label, 'sort' => $arProp['CODE'], 'default' => true];
}

while( $ob = $res->GetNextElement() ) {

	$arElement = $ob->GetFields();
	$arElement['PROPERTIES'] = $ob->GetProperties();

    $arData['ID'] = $arElement['ID'];
    $arData['DATE_CREATE'] = $arElement['DATE_CREATE'];

	foreach($arElement['PROPERTIES'] as $key=> $arProp){
        $arData[$key] =  $arProp['VALUE'];

	    if($key =='REALTYID'){
			$arData[$key] = '<a target="_blank" href="/realty/realtyinfo/?ID='.$arProp['VALUE'].'&type='.$arElement['PROPERTIES']['TYPE']['VALUE'].'">'.$arElement['NAME'].'</a>';
		}

	    if($key=='CLIENTID'){
	        $arContact = getContact($arProp['VALUE']);
            $arData[$key] = '<a class="contact-link" href="/crm/contact/details/'.$arContact['ID'].'/">'.$arContact['IDENTIFICATOR'].'</a>';
        }
	}

	$arActions = array();

	if($arElement['CREATED_BY']==$USER->getID()){
        $arActions[] = array(
            'text'    => getMessage('EDIT'),
            'default' => false,
            'onclick' => 'showEdit('.$arData['ID'].');'
        );
    }


	if( $AdminAccess ){
		$arActions[] = array(
			'text'    => getMessage('REMOVE'),
			'default' => true,
			'onclick' => 'if(confirm("Точно? удалить")){document.location.href="'.$APPLICATION->getCurPage(false).'?op=delete&id='.$arData['ID'].'"}'
		);
	}

	$arResult['GRID_DATA'][] = array('data'=>$arData, 'actions'=>$arActions);
}?>

<?

if( $_GET['op']=='delete' and intval($_GET['id'])>0){

    if(CIBlock::GetPermission(VISITS_HISTORY_IBLOCKID)>='W')
    {
        $DB->StartTransaction();
        if(!CIBlockElement::Delete($_GET['id']))
        {
            $strWarning .= 'Error!';
            $DB->Rollback();
        }
        else{
            $DB->Commit();
            localRedirect($APPLICATION->getCurPage(false));
        }

    }
}
?>

<button class="btn btn-info add-history" data-toggle="modal" data-target="#HistoryModal"><i class="glyphicon glyphicon-plus"></i> Регистрация посещения</button>

<?$APPLICATION->IncludeComponent('bitrix:main.ui.filter', '', [
        'FILTER_ID' => $list_id,
        'GRID_ID' => $list_id,
        'FILTER' => $arUiFilter,
        'ENABLE_LIVE_SEARCH' => true,
        'ENABLE_LABEL' => true
]);?>

<?$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
	'GRID_ID' => $list_id,
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

 <div class="modal fade" id="HistoryModal" role="dialog" aria-labelledby="HistoryModalTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Регистрация посещения</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
          <form id="form-history"  style="max-width: 400px; margin: 0 auto">
              <div class="info-box"></div>
              <div class="form-group">
                  <label style="display: block">Клиент <span class="starrequired">*</span></label>
                  <select name="CLIENTID" class="select2-contact form-control"></select>
              </div>

              <div class="form-group">
                  <label>Тип сделки</label>
                  <select name="DEAL_TYPE" class="form-control" id="deal-type">
                      <option value=""></option>
                      <option data-val="70" value="214">Аренда</option>
                      <option data-val="71" value="215">Продажа</option>
                  </select>
              </div>

              <div class="form-group">
                  <label>Код недвижимости (мин 3 сив․) <span class="starrequired">*</span></label>
                  <select class="select2-realty form-control" name="REALTYID"></select>
              </div>

              <div class="form-group">
                  <label>Оценка</label>
                  <select name="RATING" class="form-control">
                      <option value=""></option>
                      <option value="201">Понравился</option>
                      <option value="202">Не понравился</option>
                  </select>
              </div>
              <div class="form-group">
                  <label>Статус</label>
                  <select name="STATUS" class="form-control">
                      <option value=""></option>
                      <option value="203">Посетил</option>
                      <option value="204">Не посетил</option>
                      <option value="205">Ожидает</option>
                      <option value="206">В процессе</option>
                  </select>
              </div>

              <div class="form-group" class="form-control">
                  <input type="hidden" class="realty-ident" name="NAME">
                  <input type="hidden" name="add" value="Y">
                  <input type="submit" name="submit" class=" btn btn-success" value="Сохранить">
              </div>
          </form>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="HistoryEditModal" tabindex="-1" role="dialog" aria-labelledby="HistoryModalTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Редактировать журнал посещений</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">

	  </div>
    </div>
  </div>
</div>

<?php
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/select2.min.css');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/select2.min.js')
?>

<style>
    .select2-container{width: 100%; min-width: 320px;}
</style>

<script>
	function showEdit(id){		

		$.get( "/local/ajax/history.php?edit=Y&id="+id, function( data ) {
			$( "#HistoryEditModal .modal-body" ).html( data );
			$('#HistoryEditModal').modal('show');
		});	
	};

	$(document).ready(function () {

	     $('#form-history').submit(function () {
            let frm = $(this);
            $.ajax({
                url: '/local/ajax/history.php',
                type: "post",
                dataType: 'json',
                data:frm.serialize(),
                success:function (data) {
                    if(data.ID){
                        frm.find('.info-box').text('Успешно добавлено').addClass('alert alert-success');
                        setTimeout(function () {
                            location.reload();
                        },1000);
                    }else{
                        frm.find('.info-box').text('Незаполнены обязательны поля').addClass('alert alert-danger');
                    }
                }
            });

            return false;
        });


        $('body').on('click', '#form-history-edit .btn', function () {
            let frm = $('#form-history-edit');
            $.ajax({
                url: '/local/ajax/history.php',
                type: "post",
                dataType: 'json',
                data:frm.serialize(),
                success:function (data) {
                    frm.find('.info-box').text('Данные успешно сохранены').addClass('alert alert-success');
                    setTimeout(function () {
                        location.reload();
                    },1000)
                }
            });
            return false;
        });


        $(".select2-realty").select2({
            minimumInputLength: 3,
            ajax: {
                url: '/local/ajax/getRealtylist.php',
                type: "post",
                dataType: 'json',
                delay: 50,
                data: function(params) {

                    var dealtype = '';
                    if($('#deal-type option:selected').attr('data-val')!=='NaN'){
                        dealtype = $('#deal-type option:selected').attr('data-val');
                    }

                    return {
                    name: params.term, DEAL_TYPE:dealtype
                    }
                },
                processResults: function(response) {
                    return {
                        results: response
                    };
                }
            }
        });


        $('.select2-contact').select2({
            minimumInputLength: 2,
            ajax: {
                url: '/local/ajax/getContactByIdent.php',
                dataType: 'json',
                data: function (params) {
                    var query = {q: params.term }
                    return query;
                },
                processResults: function(response) {
                    return {
                        results: response
                    };
                }
            }
        });

        $('.select2-realty').change(function () {
            $('.realty-ident').val($('option:selected',this).text());
        });

        $('.select2-contact').change(function () {
            $('.client-ident').val($(this).text());
        });

    });

</script>

<? 
$this->IncludeComponentTemplate();