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
	ShowError('Модуль инфоблок не установлен');
	return;
}

$userTypes = array('regions', 'zones', 'floor');

// get Properties for Filter
$filter = array(
    'IBLOCK_ID' => REALTY_IBLOCK_ID,
    'CODE' => array('DEAL_TYPE', 'REALTY_TYPE', 'REGION', 'ZONE', 'ROOMS', 'FLOOR', 'TOTAL_AREA', 'BUILDING_TYPE', 'STATUS')
);
$res = Bitrix\Iblock\PropertyTable::getList(array(
    'filter' => $filter
));

while ($arProperty = $res->Fetch()) {

    // get list of property enum values
    if ($arProperty["PROPERTY_TYPE"] == "L")
    {
        $rsPropertyEnum = CIBlockProperty::GetPropertyEnum($arProperty["ID"]);
        $arProperty["ENUM"] = array();
        while ($arPropertyEnum = $rsPropertyEnum->GetNext())
        {
            $arProperty["ENUM"][$arPropertyEnum["ID"]] = $arPropertyEnum;
        }
    }

    // get user type properties

    if(!empty($arProperty['USER_TYPE'])){
        $arUserType = CIBlockProperty::GetUserType($arProperty["USER_TYPE"]);
        if(array_key_exists("GetPublicEditHTML", $arUserType)){
            $arProperty["GetPublicEditHTML"] = $arUserType["GetPublicEditHTML"];
        }
        else
        {
            unset($arProperty["GetPublicEditHTML"]);
        }
    }

    $arResult["PROPERTY_LIST"][$arProperty["CODE"]] = $arProperty;

}

$arFilter = false;
if(!empty($_GET)){
    $arFilter=$_GET;
}

$arResult['REALTY_AVG'] = Analitic::getRealtyAvG($arFilter);
$arResult['REQUETS_AVG'] = Analitic::getRequestAvg($arFilter);

//Сдано
$arFilter['STATUS'] = 147;
$arResult['REALTY_RENTED']['COUNT'] = Analitic::RealtyCount($arFilter);

//продано
$arFilter['STATUS'] = 148;
$arResult['REALTY_SOLD']['COUNT'] = Analitic::RealtyCount($arFilter);


$arFilter['DEAL_TYPE']=149;
$arResult['REQUEST_RENTS'] = Analitic::RequestsCount($arFilter);


$arFilter['DEAL_TYPE']=150;
$arResult['REQUEST_SALE'] = Analitic::RequestsCount($arFilter);


$this->IncludeComponentTemplate();