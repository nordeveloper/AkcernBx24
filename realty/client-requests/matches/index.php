<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Совпадений");
?>
<?
if( intval($_GET['id'])>0 ){
	$id = intval($_GET['id']);

	$filter['IBLOCK_ID'] = CLIENT_REQUEST_IBLOCKID;
	$filter['ACTIVE'] ="Y";
	$filter['ID'] = $id;
	$arSelect = array('IBLOCK_ID', 'ID', 'NAME', 'PROPERTY_*');
	
	$res = CIBlockElement::GetList(
		array("ID"=>"DESC"), 
		$filter, 
		false, 
		false,
		$arSelect
	);
	
	if( $ob = $res->GetNextElement() ) {
		$arEl  = $ob->getFields();
		$arProps = $ob->GetProperties();	
	}
}
?>
<div class="row">
	<div class="col-12 page-title text-center">
		<p class="h4">Варинаты недвижимостьи по запросу։ <?=$arEl['NAME']?></p>
	</div>
</div>
<?

// properties for skipe filter
$skipFields = array(
	"CLIENTID", "REALTY_PURPOSE", "CITY", "POSITION", "HOME", "APARTMENT", "HOUSE_FROM", "HOUSE_TO", "WINDOWSTREET", "WINDOWOTHER", 
	"WINDOW_COUNT_TO", "BUILT_UP_FROM", "BUILT_UP_TO", "ATTIC_FROM", "ATTIC_TO", "LINE", "HEIGHT", "LENGTH", "BUILT_AT",
	"STREET_OTHER", "H_HOUSE", "I_O", "WINDOW1", "WINDOW2", "EXITSTREET", "VISITS", "MARK", "OTHER_PARAMETERS", "AGENCYID", "REALTORID", "CLIENT_NAME"
);

foreach($arProps as $key=> $arProp){

	if( in_array($key, $skipFields) ) continue;

	if(!empty($arProp['VALUE'])){

		$propcode = '';

		if (substr($arProp['CODE'], -5) == "_FROM")
		{
			$propcode = substr($arProp['CODE'], 0,-5);
			$arProperty['>=PROPERTY_'.$propcode] = $arProp['VALUE'];	
		}
		else
		if (substr($arProp['CODE'], -3) == "_TO")
		{
			$propcode = substr($arProp['CODE'], 0,-3);
			$arProperty['<=PROPERTY_'.$propcode] = $arProp['VALUE'];

		}
		else
		if($arProp['PROPERTY_TYPE']=='L')
		{
			$arProperty['PROPERTY_'.$arProp['CODE'].'_VALUE'] = $arProp['VALUE'];
		}
		else {
			$arProperty['PROPERTY_'.$arProp['CODE']] = $arProp['VALUE'];
		}
	}	
};


global $arFilter; 
$arFilter = $arProperty;
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"matches_requests", 
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "360000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "N",
		"DETAIL_URL" => "",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "N",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"FIELD_CODE" => array(
			0 => "DETAIL_PICTURE",
			1 => "",
		),
		"FILTER_NAME" => "arFilter",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "26",
		"IBLOCK_TYPE" => "CRM_PRODUCT_CATALOG",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"INCLUDE_SUBSECTIONS" => "Y",
		"NEWS_COUNT" => "30",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "round",
		"PAGER_TITLE" => "Новости",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROPERTY_CODE" => array(
			0 => "OWNER_CODE",
			1 => "PRICE",
			2 => "PRICE_DAY",
			3 => "DAILY_RENT",
			4 => "DEAL_TYPE",
			5 => "REALTY_TYPE",
			6 => "REALTY_PURPOSE",
			7 => "BUILDING_TYPE",
			8 => "REGION",
			9 => "CITY",
			10 => "ZONE",
			11 => "STREET",
			12 => "HOME",
			13 => "APARTMENT",
			14 => "POSITION",
			15 => "ROOMS",
			16 => "FLOOR",
			17 => "FLOORS",
			18 => "REPAIRS",
			19 => "TOTAL_AREA",
			20 => "USEFUL_AREA",
			21 => "CEILING",
			22 => "INTERFLOOR_OVERLAP",
			23 => "GARAGE",
			24 => "FURNITURE",
			25 => "BASEMENT",
			26 => "SEMIBASEMENT",
			27 => "APPLIANCES",
			28 => "CONDITIONER",
			29 => "HEATING",
			30 => "ADDIN",
			31 => "PARKING",
			32 => "BALCONY1",
			33 => "BALCONY2",
			34 => "ATTIC",
			35 => "FACADE",
			36 => "BUILDING_FACADE",
			37 => "BUILDING_LENGTH",
			38 => "LENGTH",
			39 => "HEIGHT",
			40 => "BUILDING_STATE",
			41 => "WINDOWSTREET",
			42 => "WINDOWOTHER",
			43 => "EXITSTREET",
			44 => "STREET_ENTRANCE",
			45 => "GROUND_STATUS",
			46 => "GROUND_AREA",
			47 => "TOTAL_GROUND_AREA",
			48 => "TOTAL_AREA_BUILDING",
			49 => "LINE",
			50 => "STREET_OTHER",
			51 => "HOUSE_DEMOLITION",
			52 => "BUILT_UP",
			53 => "BUILT_AT",
			54 => "MARK",
			55 => "RENTDATEFROM",
			56 => "RENTDATETO",
			57 => "STATUS",
			58 => "DEPARTMENT",
			59 => "",
			60 => "",
			61 => "",
			62 => "",
			63 => "",
			64 => "",
			65 => "",
			66 => "",
			67 => "",
			68 => "",
			69 => "",
			70 => "",
			71 => "",
			72 => "",
			73 => "",
			74 => "",
			75 => "",
			76 => "",
			77 => "",
			78 => "",
			79 => "",
			80 => "",
			81 => "",
			82 => "",
			83 => "",
			84 => "",
			85 => "",
			86 => "",
			87 => "",
			88 => "",
			89 => "",
			90 => "",
			91 => "",
			92 => "",
			93 => "",
			94 => "",
			95 => "",
			96 => "",
			97 => "",
			98 => "",
			99 => "",
			100 => "",
			101 => "",
			102 => "",
			103 => "",
			104 => "",
			105 => "",
			106 => "",
			107 => "",
			108 => "",
			109 => "",
			110 => "",
			111 => "",
			112 => "",
		),
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SORT_BY1" => "ID",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "DESC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "N",
		"COMPONENT_TEMPLATE" => "matches_requests",
		"MEDIA_PROPERTY" => "",
		"MESSAGE_404" => ""
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>