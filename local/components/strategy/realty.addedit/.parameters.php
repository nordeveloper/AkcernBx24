<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

if($arCurrentValues["IBLOCK_ID"] > 0)
{
	$arIBlock = CIBlock::GetArrayByID($arCurrentValues["IBLOCK_ID"]);
}

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock=array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arProperty_LNSF = array();


//$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "id"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));

$resProps = Bitrix\Iblock\PropertyTable::getList(array(
	'filter' => array('IBLOCK_ID' => $arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y"), 'order'=>array('ID'=>'ASC', 'SORT'=>'ASC')
));

while ($arr=$resProps->Fetch())
{
	$arProperty[$arr["ID"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S", "F")))
	{
		$arProperty_LNSF[$arr["ID"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}


$arGroups = array();
$rsGroups = CGroup::GetList($by="c_sort", $order="asc", Array("ACTIVE" => "Y"));
while ($arGroup = $rsGroups->Fetch())
{
	$arGroups[$arGroup["ID"]] = $arGroup["NAME"];
}

$arComponentParameters = array(
	"GROUPS" => array(
		"PARAMS" => array(
			"NAME" => GetMessage("IBLOCK_PARAMS"),
			"SORT" => "200"
		),
		"ACCESS" => array(
			"NAME" => GetMessage("IBLOCK_ACCESS"),
			"SORT" => "400",
		)
	),

	"PARAMETERS" => array(

		"IBLOCK_TYPE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),

		"IBLOCK_ID" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),

		"GROUPS_ADMINS" => array(
			"PARENT" => "ACCESS",
			"NAME" => GetMessage("GROUPS_ADMIN"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arGroups,
		),
        "GROUPS_DIRECTORS" => array(
            "PARENT" => "ACCESS",
            "NAME" => getMessage("GROUPS_DIRECTORS"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arGroups,
        ),

		"LIST_URL" => array(
			"PARENT" => "PARAMS",
			"TYPE" => "TEXT",
			"NAME" => GetMessage("IBLOCK_ADD_LIST_URL"),
        ),
        
        "BLOCK_TOP" => array(
            "PARENT" => "",
            "NAME" => 'Верхный блок',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>10
        ),

        "BLOCKLEFT" => array(
            "PARENT" => "",
            "NAME" => 'Левый блок',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>10
        ),

        "PROPERTY_72" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Квартиры',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>10
        ),

        "PROPERTY_73" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Дома',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>10
        ),

        "PROPERTY_74" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Особняк',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>10
        ),

        "PROPERTY_75" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Дача',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>10
        ),

        "PROPERTY_76" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Офиса',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>10
        ),

        "PROPERTY_77" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Ресторан',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>10
        ),

        "PROPERTY_78" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Магазина',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>10
        ),

        "PROPERTY_80" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Земли',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>10
        ),

        "PROPERTY_81" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Автосервиса',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>10
        ),

        "PROPERTY_82" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Гостиницы',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>10
        ),

        "PROPERTY_83" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Парикмахерская',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>10
        ),

		"PROPERTY_225" => array(
			"PARENT" => "",
			"NAME" => 'Не полказать поля для Другое',
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_LNSF,
			"SIZE"=>10
		)
	),
);
?>
