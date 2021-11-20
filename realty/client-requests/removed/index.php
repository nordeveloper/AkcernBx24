<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Уделленые запросы клиентов");
?>
<?$APPLICATION->IncludeComponent(
	"strategy:requests.removed", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_TYPE" => "CRM_PRODUCT_CATALOG",
		"IBLOCK_ID" => "28",
		"LINK_LIST" => "/realty/client-requests/",
		"LIST_URL" => "/realty/client-requests/",
		"GROUPS_ADMINS" => array(
			0 => "1",
			1 => "13",
		),
		"PROPERTY_151" => array(
		),
		"PROPERTY_152" => array(
		),
		"PROPERTY_153" => array(
		),
		"PROPERTY_154" => array(
		),
		"PROPERTY_155" => array(
		),
		"PROPERTY_156" => array(
		),
		"PROPERTY_157" => array(
		),
		"PROPERTY_159" => array(
		),
		"PROPERTY_160" => array(
		),
		"PROPERTY_161" => array(
		),
		"PROPERTY_162" => array(
		),
		"PROPERTY_158" => array(
		),
		"GROUPS_DIRECTORS" => array(
			0 => "10",
			1 => "19",
		)
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>