<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\ModuleManager;

if(!CModule::IncludeModule("iblock"))
	return;

$mediaProperty = array(
	"" => GetMessage("MAIN_NO"),
);

$propertyList = CIBlockProperty::GetList(
	array("sort"=>"asc", "name"=>"asc"),
	array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"])
);
while ($property = $propertyList->Fetch())
{
	$id = $property["CODE"]? $property["CODE"]: $property["ID"];
	if ($property["PROPERTY_TYPE"] == "S")
	{
		$mediaProperty[$id] = "[".$id."] ".$property["NAME"];
	}
	if ($property["PROPERTY_TYPE"] == "F")
	{
		$sliderProperty[$id] = "[".$id."] ".$property["NAME"];
	}
}

$arTemplateParameters = array(
	"DISPLAY_DATE" => array(
		"NAME" => GetMessage("TP_BND_DISPLAY_DATE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_NAME" => array(
		"NAME" => GetMessage("TP_BND_DISPLAY_NAME"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PICTURE" => array(
		"NAME" => GetMessage("TP_BND_DISPLAY_PICTURE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PREVIEW_TEXT" => array(
		"NAME" => GetMessage("TP_BND_DISPLAY_PREVIEW_TEXT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"MEDIA_PROPERTY" => array(
		"NAME" => GetMessage("TP_BND_MEDIA_PROPERTY"),
		"TYPE" => "LIST",
		"VALUES" => $mediaProperty,
	),
);