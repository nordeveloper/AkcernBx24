<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Архив недвижимости");
?>
<?$APPLICATION->IncludeComponent(
	"strategy:realty.archive", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_TYPE" => "CRM_PRODUCT_CATALOG",
		"IBLOCK_ID" => "26",
		"LINK_EDIT" => "/realty/realtyinfo/",
		"LIST_URL" => "/realty/archive/",
		"GROUPS_ADMINS" => array(
			0 => "1",
			1 => "13",
		),
		"GROUPS_DIRECTORS" => array(
			0 => "10",
		),
		"PROPERTY_72" => array(
			0 => "256",
			1 => "275",
			2 => "301",
		),
		"PROPERTY_73" => array(
			0 => "256",
			1 => "275",
			2 => "301",
		),
		"PROPERTY_74" => array(
			0 => "256",
			1 => "275",
			2 => "301",
		),
		"PROPERTY_75" => array(
			0 => "256",
			1 => "275",
			2 => "301",
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
			0 => "134",
			1 => "136",
			2 => "256",
			3 => "275",
			4 => "301",
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