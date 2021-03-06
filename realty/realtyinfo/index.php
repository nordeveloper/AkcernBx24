<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Информация о недвижимости");
?>
<?$APPLICATION->IncludeComponent(
	"strategy:realty.addedit", 
	".default", 
	array(
		"GROUPS_ADMINS" => array(
			0 => "1",
			1 => "13",
		),
		"IBLOCK_ID" => "26",
		"IBLOCK_TYPE" => "CRM_PRODUCT_CATALOG",
		"LIST_URL" => "/realty/list/",
		"BLOCK_TOP" => array(
			0 => "92",
			1 => "93",
			2 => "94",
			3 => "95",
		),
		"BLOCKLEFT" => array(
			0 => "88",
			1 => "143",
			2 => "144",
			3 => "150",
			4 => "239",
			5 => "244",
			6 => "245",
			7 => "249",
			8 => "301",
			9 => "305",
		),
		"GROUPS_DIRECTORS" => array(
			0 => "10",
		),
		"GROUPS_OPERATORS" => array(
			0 => "18",
		),
		"COMPONENT_TEMPLATE" => ".default",
		"PROPERTY_72" => array(
			0 => "109",
			1 => "122",
			2 => "123",
			3 => "124",
			4 => "125",
			5 => "126",
			6 => "127",
			7 => "128",
			8 => "129",
			9 => "130",
			10 => "133",
			11 => "134",
			12 => "136",
			13 => "137",
			14 => "138",
			15 => "139",
			16 => "145",
			17 => "149",
			18 => "153",
			19 => "251",
			20 => "256",
			21 => "275",
			22 => "305",
			23 => "316",
		),
		"PROPERTY_73" => array(
			0 => "94",
			1 => "95",
			2 => "108",
			3 => "109",
			4 => "119",
			5 => "120",
			6 => "126",
			7 => "128",
			8 => "129",
			9 => "130",
			10 => "131",
			11 => "133",
			12 => "137",
			13 => "138",
			14 => "146",
			15 => "149",
			16 => "153",
			17 => "251",
			18 => "256",
			19 => "275",
			20 => "305",
			21 => "316",
		),
		"PROPERTY_74" => array(
			0 => "94",
			1 => "95",
			2 => "108",
			3 => "109",
			4 => "119",
			5 => "120",
			6 => "126",
			7 => "128",
			8 => "129",
			9 => "130",
			10 => "131",
			11 => "133",
			12 => "137",
			13 => "138",
			14 => "146",
			15 => "149",
			16 => "153",
			17 => "251",
			18 => "256",
			19 => "275",
			20 => "305",
			21 => "316",
		),
		"PROPERTY_75" => array(
			0 => "94",
			1 => "95",
			2 => "108",
			3 => "109",
			4 => "119",
			5 => "120",
			6 => "126",
			7 => "128",
			8 => "129",
			9 => "130",
			10 => "131",
			11 => "133",
			12 => "137",
			13 => "138",
			14 => "146",
			15 => "149",
			16 => "153",
			17 => "251",
			18 => "256",
			19 => "275",
			20 => "305",
			21 => "316",
		),
		"PROPERTY_76" => array(
			0 => "102",
			1 => "108",
			2 => "109",
			3 => "117",
			4 => "119",
			5 => "120",
			6 => "121",
			7 => "122",
			8 => "125",
			9 => "127",
			10 => "133",
			11 => "134",
			12 => "138",
			13 => "139",
			14 => "145",
			15 => "146",
			16 => "149",
			17 => "153",
			18 => "243",
			19 => "251",
			20 => "256",
			21 => "275",
			22 => "305",
			23 => "316",
		),
		"PROPERTY_77" => array(
			0 => "102",
			1 => "108",
			2 => "109",
			3 => "117",
			4 => "119",
			5 => "120",
			6 => "121",
			7 => "122",
			8 => "125",
			9 => "127",
			10 => "133",
			11 => "134",
			12 => "138",
			13 => "139",
			14 => "145",
			15 => "146",
			16 => "149",
			17 => "153",
			18 => "243",
			19 => "251",
			20 => "256",
			21 => "275",
			22 => "305",
			23 => "316",
		),
		"PROPERTY_78" => array(
			0 => "102",
			1 => "108",
			2 => "109",
			3 => "117",
			4 => "119",
			5 => "120",
			6 => "121",
			7 => "122",
			8 => "125",
			9 => "127",
			10 => "131",
			11 => "133",
			12 => "137",
			13 => "138",
			14 => "139",
			15 => "145",
			16 => "146",
			17 => "149",
			18 => "153",
			19 => "243",
			20 => "251",
			21 => "256",
			22 => "275",
			23 => "305",
			24 => "316",
		),
		"PROPERTY_80" => array(
			0 => "91",
			1 => "95",
			2 => "102",
			3 => "103",
			4 => "104",
			5 => "105",
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
			23 => "126",
			24 => "127",
			25 => "128",
			26 => "129",
			27 => "130",
			28 => "131",
			29 => "132",
			30 => "134",
			31 => "136",
			32 => "139",
			33 => "149",
			34 => "153",
			35 => "243",
			36 => "251",
			37 => "256",
			38 => "275",
			39 => "305",
			40 => "316",
		),
		"PROPERTY_81" => array(
			0 => "102",
			1 => "108",
			2 => "109",
			3 => "117",
			4 => "119",
			5 => "120",
			6 => "121",
			7 => "122",
			8 => "125",
			9 => "127",
			10 => "133",
			11 => "134",
			12 => "138",
			13 => "139",
			14 => "145",
			15 => "146",
			16 => "149",
			17 => "153",
			18 => "243",
			19 => "251",
			20 => "256",
			21 => "275",
			22 => "305",
			23 => "316",
		),
		"PROPERTY_82" => array(
			0 => "102",
			1 => "108",
			2 => "109",
			3 => "117",
			4 => "119",
			5 => "120",
			6 => "121",
			7 => "122",
			8 => "125",
			9 => "127",
			10 => "133",
			11 => "134",
			12 => "138",
			13 => "139",
			14 => "145",
			15 => "146",
			16 => "149",
			17 => "153",
			18 => "243",
			19 => "251",
			20 => "256",
			21 => "275",
			22 => "305",
			23 => "316",
		),
		"PROPERTY_83" => array(
			0 => "102",
			1 => "108",
			2 => "109",
			3 => "117",
			4 => "119",
			5 => "120",
			6 => "121",
			7 => "122",
			8 => "125",
			9 => "127",
			10 => "133",
			11 => "134",
			12 => "138",
			13 => "139",
			14 => "145",
			15 => "146",
			16 => "149",
			17 => "153",
			18 => "243",
			19 => "251",
			20 => "256",
			21 => "275",
			22 => "305",
			23 => "316",
		),
		"PROPERTY_225" => array(
			0 => "102",
			1 => "108",
			2 => "117",
			3 => "119",
			4 => "120",
			5 => "121",
			6 => "122",
			7 => "125",
			8 => "127",
			9 => "133",
			10 => "134",
			11 => "138",
			12 => "139",
			13 => "149",
			14 => "151",
			15 => "153",
			16 => "243",
			17 => "251",
			18 => "256",
			19 => "275",
			20 => "305",
			21 => "316",
		)
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>