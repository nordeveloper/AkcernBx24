<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Удаленная недвижимость");
?>
<?$APPLICATION->IncludeComponent(
	"strategy:realty.removed", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_ID" => "26",
		"IBLOCK_TYPE" => "CRM_PRODUCT_CATALOG",
		"LINK_EDIT" => "/realty/realtyinfo/",
		"LIST_URL" => "/realty/removed/",
		"GROUPS_ADMINS" => array(
			0 => "1",
			1 => "13",
		),
		"GROUPS_DIRECTORS" => array(
			0 => "10",
		),
		"PROPERTY_72" => array(
			0 => "122",
			1 => "133",
			2 => "134",
			3 => "136",
			4 => "137",
			5 => "145",
			6 => "146",
		),
		"PROPERTY_73" => array(
			0 => "256",
		),
		"PROPERTY_74" => array(
			0 => "256",
		),
		"PROPERTY_75" => array(
			0 => "256",
		),
		"PROPERTY_76" => array(
			0 => "125",
			1 => "133",
			2 => "134",
			3 => "256",
		),
		"PROPERTY_77" => array(
			0 => "122",
			1 => "125",
			2 => "133",
			3 => "134",
			4 => "145",
			5 => "146",
			6 => "256",
		),
		"PROPERTY_78" => array(
			0 => "122",
			1 => "125",
			2 => "133",
			3 => "134",
			4 => "145",
			5 => "146",
			6 => "256",
		),
		"PROPERTY_80" => array(
			0 => "95",
			1 => "99",
			2 => "100",
			3 => "102",
			4 => "103",
			5 => "104",
			6 => "107",
			7 => "108",
			8 => "109",
			9 => "110",
			10 => "111",
			11 => "112",
			12 => "113",
			13 => "114",
			14 => "115",
			15 => "116",
			16 => "117",
			17 => "118",
			18 => "119",
			19 => "120",
			20 => "121",
			21 => "123",
			22 => "124",
			23 => "127",
			24 => "128",
			25 => "129",
			26 => "134",
			27 => "136",
			28 => "243",
			29 => "256",
		),
		"PROPERTY_81" => array(
			0 => "100",
			1 => "122",
			2 => "125",
			3 => "133",
			4 => "134",
			5 => "145",
			6 => "146",
			7 => "256",
		),
		"PROPERTY_82" => array(
			0 => "122",
			1 => "125",
			2 => "133",
			3 => "134",
			4 => "136",
			5 => "145",
			6 => "146",
			7 => "256",
		),
		"PROPERTY_83" => array(
			0 => "122",
			1 => "123",
			2 => "124",
			3 => "125",
			4 => "133",
			5 => "134",
			6 => "136",
			7 => "137",
			8 => "139",
			9 => "256",
		),
		"PROPERTY_79" => array(
			0 => "256",
		)
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>