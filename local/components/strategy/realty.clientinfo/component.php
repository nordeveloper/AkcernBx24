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

if (!CModule::IncludeModule('crm'))
{
	ShowError(GetMessage('CRM_MODULE_NOT_INSTALLED'));
	return;
}

global $USER_FIELD_MANAGER;

if (!CModule::IncludeModule('iblock'))
{
	ShowError('MODULE IBLOCK NOT INSTALLED');
	return;
}

if($_GET['id']>0){
    $ID = intval($_GET['id']);

	$arContact = getContact($ID);
	$arResult['CLIENT'] = $arContact;

	$filterData['IBLOCK_ID'] = CLIENT_REQUEST_IBLOCKID;
	$filterData['ACTIVE'] = "Y";
	$filterData['PROPERTY_CLIENTID'] = $ID;

	$res = \CIBlockElement::GetList($sort['sort'], $filterData, false, $nav_params,
		['IBLOCK_ID', 'ID', 'NAME', 'PROPERTY_*']
	);

	while( $ob = $res->GetNextElement() ) {

		$arEl = $ob->GetFields();
		//dump($arEl);
		$arProps = $ob->GetProperties();

		if( !empty($arProps['CITY']['VALUE']) ){
			foreach ($arProps['CITY']['VALUE'] as $ckey=> $region_id){
				$region = getCityById($region_id);
				$arProps['CITY']['VALUE'][$ckey] = $region['name_'.LANGUAGE_ID];
			}
		}

		$arResult['ITEMS'][$arEl['ID']] = $arEl;

		unset($arProps['CLIENTID']);
		if($arProps['CODE']=='CLIENTID') continue;

		$arResult['ITEMS'][$arEl['ID']]['PROPERTIES'] = $arProps;
	}

}
?>

<?$this->IncludeComponentTemplate();?>
