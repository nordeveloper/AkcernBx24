<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Редактировать");
?><?$APPLICATION->IncludeComponent(
	"strategy:request.addedit", 
	".default", 
	array(
		"IBLOCK_ID" => "28",
		"IBLOCK_TYPE" => "CRM_PRODUCT_CATALOG",
		"LIST_URL" => "/realty/client-requests/",
		"COMPONENT_TEMPLATE" => ".default",
		"GROUPS" => "",
		"BLOCKLEFT" => array(
			0 => "155",
			1 => "230",
		),
		"PROPERTY_151" => array(
			0 => "178",
			1 => "179",
			2 => "181",
			3 => "183",
			4 => "186",
			5 => "187",
			6 => "188",
			7 => "189",
			8 => "190",
			9 => "191",
			10 => "192",
			11 => "194",
			12 => "195",
			13 => "196",
			14 => "197",
			15 => "198",
			16 => "199",
			17 => "200",
			18 => "204",
			19 => "206",
			20 => "207",
			21 => "208",
			22 => "209",
			23 => "213",
			24 => "217",
			25 => "218",
			26 => "220",
			27 => "225",
			28 => "226",
			29 => "228",
			30 => "231",
			31 => "237",
			32 => "272",
			33 => "273",
		),
		"PROPERTY_152" => array(
			0 => "178",
			1 => "179",
			2 => "181",
			3 => "188",
			4 => "189",
			5 => "190",
			6 => "191",
			7 => "192",
			8 => "194",
			9 => "195",
			10 => "197",
			11 => "199",
			12 => "200",
			13 => "204",
			14 => "206",
			15 => "207",
			16 => "208",
			17 => "217",
			18 => "218",
			19 => "220",
			20 => "225",
			21 => "226",
			22 => "228",
			23 => "231",
			24 => "237",
			25 => "272",
			26 => "273",
		),
		"PROPERTY_153" => array(
			0 => "178",
			1 => "179",
			2 => "181",
			3 => "188",
			4 => "189",
			5 => "190",
			6 => "191",
			7 => "192",
			8 => "194",
			9 => "195",
			10 => "197",
			11 => "199",
			12 => "200",
			13 => "204",
			14 => "206",
			15 => "207",
			16 => "208",
			17 => "217",
			18 => "218",
			19 => "220",
			20 => "225",
			21 => "226",
			22 => "228",
			23 => "231",
			24 => "237",
			25 => "272",
			26 => "273",
		),
		"PROPERTY_154" => array(
			0 => "178",
			1 => "179",
			2 => "181",
			3 => "188",
			4 => "189",
			5 => "190",
			6 => "191",
			7 => "192",
			8 => "194",
			9 => "195",
			10 => "197",
			11 => "199",
			12 => "200",
			13 => "204",
			14 => "206",
			15 => "207",
			16 => "208",
			17 => "217",
			18 => "218",
			19 => "220",
			20 => "225",
			21 => "226",
			22 => "228",
			23 => "231",
			24 => "237",
			25 => "272",
			26 => "273",
		),
		"PROPERTY_155" => array(
			0 => "164",
			1 => "165",
			2 => "167",
			3 => "171",
			4 => "176",
			5 => "178",
			6 => "179",
			7 => "181",
			8 => "183",
			9 => "186",
			10 => "187",
			11 => "188",
			12 => "189",
			13 => "190",
			14 => "191",
			15 => "192",
			16 => "194",
			17 => "195",
			18 => "196",
			19 => "197",
			20 => "198",
			21 => "199",
			22 => "200",
			23 => "206",
			24 => "207",
			25 => "208",
			26 => "209",
			27 => "217",
			28 => "218",
			29 => "220",
			30 => "225",
			31 => "228",
			32 => "231",
			33 => "237",
			34 => "272",
			35 => "273",
		),
		"PROPERTY_156" => array(
			0 => "164",
			1 => "165",
			2 => "167",
			3 => "171",
			4 => "173",
			5 => "176",
			6 => "178",
			7 => "179",
			8 => "181",
			9 => "183",
			10 => "186",
			11 => "187",
			12 => "188",
			13 => "189",
			14 => "190",
			15 => "191",
			16 => "192",
			17 => "194",
			18 => "195",
			19 => "196",
			20 => "197",
			21 => "198",
			22 => "199",
			23 => "200",
			24 => "204",
			25 => "206",
			26 => "207",
			27 => "208",
			28 => "217",
			29 => "218",
			30 => "220",
			31 => "225",
			32 => "228",
			33 => "231",
			34 => "237",
			35 => "272",
			36 => "273",
		),
		"PROPERTY_157" => array(
			0 => "164",
			1 => "165",
			2 => "167",
			3 => "171",
			4 => "176",
			5 => "178",
			6 => "179",
			7 => "181",
			8 => "183",
			9 => "186",
			10 => "187",
			11 => "188",
			12 => "189",
			13 => "191",
			14 => "192",
			15 => "194",
			16 => "195",
			17 => "196",
			18 => "197",
			19 => "198",
			20 => "199",
			21 => "200",
			22 => "204",
			23 => "206",
			24 => "207",
			25 => "208",
			26 => "209",
			27 => "217",
			28 => "218",
			29 => "220",
			30 => "225",
			31 => "228",
			32 => "231",
			33 => "237",
			34 => "272",
			35 => "273",
		),
		"PROPERTY_159" => array(
			0 => "164",
			1 => "165",
			2 => "166",
			3 => "167",
			4 => "171",
			5 => "173",
			6 => "174",
			7 => "178",
			8 => "179",
			9 => "181",
			10 => "183",
			11 => "186",
			12 => "187",
			13 => "195",
			14 => "197",
			15 => "198",
			16 => "199",
			17 => "200",
			18 => "204",
			19 => "206",
			20 => "207",
			21 => "209",
			22 => "213",
			23 => "217",
			24 => "218",
			25 => "219",
			26 => "225",
			27 => "226",
			28 => "228",
			29 => "231",
			30 => "272",
			31 => "273",
		),
		"PROPERTY_160" => array(
			0 => "164",
			1 => "165",
			2 => "167",
			3 => "171",
			4 => "176",
			5 => "178",
			6 => "179",
			7 => "181",
			8 => "183",
			9 => "186",
			10 => "187",
			11 => "188",
			12 => "189",
			13 => "191",
			14 => "192",
			15 => "194",
			16 => "195",
			17 => "196",
			18 => "197",
			19 => "198",
			20 => "199",
			21 => "200",
			22 => "204",
			23 => "206",
			24 => "207",
			25 => "208",
			26 => "209",
			27 => "217",
			28 => "218",
			29 => "220",
			30 => "225",
			31 => "228",
			32 => "231",
			33 => "237",
			34 => "272",
			35 => "273",
		),
		"PROPERTY_161" => array(
			0 => "164",
			1 => "165",
			2 => "167",
			3 => "171",
			4 => "176",
			5 => "178",
			6 => "179",
			7 => "181",
			8 => "183",
			9 => "186",
			10 => "187",
			11 => "188",
			12 => "189",
			13 => "191",
			14 => "192",
			15 => "194",
			16 => "195",
			17 => "196",
			18 => "197",
			19 => "198",
			20 => "199",
			21 => "200",
			22 => "204",
			23 => "206",
			24 => "207",
			25 => "208",
			26 => "209",
			27 => "217",
			28 => "218",
			29 => "220",
			30 => "225",
			31 => "228",
			32 => "231",
			33 => "237",
			34 => "272",
			35 => "273",
		),
		"PROPERTY_162" => array(
			0 => "164",
			1 => "165",
			2 => "167",
			3 => "171",
			4 => "176",
			5 => "178",
			6 => "179",
			7 => "181",
			8 => "183",
			9 => "186",
			10 => "187",
			11 => "188",
			12 => "189",
			13 => "191",
			14 => "192",
			15 => "194",
			16 => "195",
			17 => "196",
			18 => "197",
			19 => "198",
			20 => "199",
			21 => "200",
			22 => "204",
			23 => "206",
			24 => "207",
			25 => "208",
			26 => "209",
			27 => "217",
			28 => "218",
			29 => "220",
			30 => "225",
			31 => "228",
			32 => "231",
			33 => "237",
			34 => "272",
			35 => "273",
		),
		"PROPERTY_226" => array(
			0 => "164",
			1 => "165",
			2 => "167",
			3 => "168",
			4 => "171",
			5 => "172",
			6 => "175",
			7 => "176",
			8 => "177",
			9 => "178",
			10 => "179",
			11 => "180",
			12 => "181",
			13 => "182",
			14 => "183",
			15 => "184",
			16 => "186",
			17 => "187",
			18 => "188",
			19 => "189",
			20 => "191",
			21 => "192",
			22 => "193",
			23 => "194",
			24 => "195",
			25 => "196",
			26 => "197",
			27 => "198",
			28 => "199",
			29 => "200",
			30 => "201",
			31 => "202",
			32 => "203",
			33 => "204",
			34 => "205",
			35 => "206",
			36 => "207",
			37 => "208",
			38 => "209",
			39 => "210",
			40 => "211",
			41 => "212",
			42 => "215",
			43 => "216",
			44 => "217",
			45 => "218",
			46 => "220",
			47 => "221",
			48 => "222",
			49 => "225",
			50 => "228",
			51 => "231",
			52 => "272",
			53 => "273",
		),
		"GROUPS_ADMINS" => array(
			0 => "1",
			1 => "13",
		),
		"GROUP_DIRECTORS" => array(
			0 => "10",
			1 => "19",
		)
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>