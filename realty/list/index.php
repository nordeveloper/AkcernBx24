<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Недвижимость");
?>
<?$APPLICATION->IncludeComponent(
	"strategy:realty.list", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_ID" => "26",
		"IBLOCK_TYPE" => "CRM_PRODUCT_CATALOG",
		"LINK_EDIT" => "/realty/realtyinfo/",
		"LIST_URL" => "/realty/list/",
		"GROUPS_ADMINS" => array(
			0 => "1",
			1 => "13",
		),
		"GROUPS_DIRECTORS" => array(
			0 => "10",
		),
		"PROPERTY_72" => array(
			0 => "125",
			1 => "134",
			2 => "136",
			3 => "256",
			4 => "275",
			5 => "301",
			6 => "314",
		),
		"PROPERTY_73" => array(
			0 => "244",
			1 => "245",
			2 => "256",
			3 => "301",
			4 => "314",
		),
		"PROPERTY_74" => array(
			0 => "256",
			1 => "275",
			2 => "301",
			3 => "314",
		),
		"PROPERTY_75" => array(
			0 => "256",
			1 => "301",
			2 => "314",
		),
		"PROPERTY_76" => array(
			0 => "256",
			1 => "275",
			2 => "301",
		),
		"PROPERTY_77" => array(
			0 => "256",
			1 => "275",
			2 => "301",
		),
		"PROPERTY_78" => array(
			0 => "256",
			1 => "275",
			2 => "301",
		),
		"PROPERTY_80" => array(
			0 => "91",
			1 => "95",
			2 => "102",
			3 => "103",
			4 => "104",
			5 => "107",
			6 => "108",
			7 => "109",
			8 => "111",
			9 => "114",
			10 => "115",
			11 => "116",
			12 => "117",
			13 => "119",
			14 => "120",
			15 => "121",
			16 => "123",
			17 => "124",
			18 => "127",
			19 => "128",
			20 => "129",
			21 => "132",
			22 => "134",
			23 => "136",
			24 => "139",
			25 => "243",
			26 => "244",
			27 => "245",
			28 => "256",
			29 => "299",
			30 => "301",
			31 => "314",
			32 => "316",
		),
		"PROPERTY_81" => array(
			0 => "256",
			1 => "275",
			2 => "301",
		),
		"PROPERTY_82" => array(
			0 => "256",
			1 => "275",
			2 => "301",
		),
		"PROPERTY_83" => array(
			0 => "256",
			1 => "275",
			2 => "301",
		),
		"PROPERTY_79" => array(
			0 => "256",
		)
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>