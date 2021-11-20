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


$arResult['ACTIVE_REALTY_COUNT'] = getRealtyCount(['ACTIVE'=>'Y', 'ACTIVE_DATE'=>'Y']);
$arResult['ARCHIVE_REALTY_COUNT'] = getRealtyCount(['ACTIVE'=>'Y', '!ACTIVE_DATE'=>'Y']);
$arResult['REMOVED_REALTY_COUNT'] = getRealtyCount(['!ACTIVE'=>'Y']);

$arResult['REQUESTS_ACTIVE'] = getRequestCount(['ACTIVE'=>'Y']);
$arResult['REQUESTS_REMOVED'] = getRequestCount(['!ACTIVE'=>'Y']);

$arResult['LOCKED_COUNT'] = Lockedaddress::getLocked();

$this->IncludeComponentTemplate();