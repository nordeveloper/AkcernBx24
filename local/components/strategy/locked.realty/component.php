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

// users array data for Filter form;
$arUsers = getUsers();
foreach ($arUsers as $arUser){
    $arUsersList[$arUser['ID']] = $arUser['NAME'].' '.$arUser['LAST_NAME'];
}
$arResult['arUsersList'] = $arUsersList;


$gridID = 'LockedRealtyList';
use Bitrix\Main\UI\Filter\Options as FilterOptions;
$filterOption = new FilterOptions($gridID);
$filterData = $filterOption->getFilter([]);

foreach ($filterData as $filter) {
    if(!empty($filterData['FIND']) ) {$filterData['REALTY_CODE'] = trim($filterData['FIND']);}
    unset($filterData['FIND']);
    unset($filterData['PRESET_ID']);
    unset($filterData['FILTER_ID']);
    unset($filterData['FILTER_APPLIED']);
}


$arResult['ITEMS'] = Lockedrealty::getList($filterData);

foreach ($arResult['ITEMS'] as $arItem){

    if($arItem['ACTIVE']==1){ $arItem['ACTIVE']='Да';} else {$arItem['ACTIVE'] = 'Нет';}
    $arItem['REALTY_ID'] = $arItem['REALTY_CODE'];

    $arActions = array();

    $arActions[] = array(
        'text'    => getMessage('REMOVE'),
        'default' => true,
        'onclick' => 'if(confirm("Точно?")){document.location.href="?op=remove&id='.$arItem['ID'].'"}'
    );

    $arResult['GRID_DATA'][] = array('data'=>$arItem, 'actions'=>$arActions);
}

if( $_GET['op']=='remove' and $_GET['id']>0){
    Lockedrealty::Remove($_GET['id']);
    localRedirect($APPLICATION->GetCurPage());
}


if( !empty($_POST['BTN_ADD']) ){

    if(intval($_POST['user_id'])<1){
        $arResult['MESSAGE']['ERROR'] = 'Риелтор обязательно для заполнения';
    }
    if( empty( $_POST['realty_code'] ) ){
        $arResult['MESSAGE']['ERROR'] = 'Код недвижимости обязательно для блокировки';
    }

    $usrlokcount = Lockedrealty::getUserLockedCount($_POST['user_id'], $_POST['realty_code']);

    if($usrlokcount['COUNT']>0){
        $arResult['MESSAGE']['ERROR'] = 'У этого пользователя на сегодня уже есть одна блакировака по коду '.$_POST['realty_code'];
    }

    if( empty($arResult['MESSAGE']['ERROR']) ){
        $rs = Lockedrealty::getLocked($_POST['user_id'], $_POST['realty_code']);

        if($rs['count']<2 and $usrlokcount['COUNT']<1 ){

            $res = Lockedrealty::Add($_POST);

            if( empty($res['ERROR']) ){
//                $arResult['MESSAGE']['SUCCESS'] = $arResult['SUCCESS'];
                localRedirect($APPLICATION->getCurPage().'?success=Y');
            }else{
                $arResult['MESSAGE']['ERROR'] = $res['ERROR'];
            }
        }else{
            $arResult['MESSAGE']['ERROR'] = 'У этого пользователя уже есть 2 блокировки 2 дня подряд по коду '.$_POST['realty_code'];
        }
    }else{
        $arResult['MESSAGE']['ERROR'] = $res['ERROR'];
    }

}

$this->IncludeComponentTemplate();