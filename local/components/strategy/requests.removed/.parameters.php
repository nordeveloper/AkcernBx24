<?php
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


$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "id"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>REALTY_IBLOCK_ID));
while ($arr=$rsProp->Fetch())
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

    "GROUPS_ADMINS" => array(
        "PARAMS" => array(
            "NAME" => 'Параметры компонента',
            "SORT" => "200"
        ),
        "ACCESS" => array(
            "NAME" => 'Группы пользователей, имеющие право на добавление/редактирование',
            "SORT" => "400",
        )
    ),

    "PARAMETERS" => array(

        "IBLOCK_TYPE" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => 'Тип инфоблока',
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlockType,
            "REFRESH" => "Y",
        ),

        "IBLOCK_ID" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => 'Инфоблок',
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
        ),

        "GROUPS_ADMINS" => array(
            "PARENT" => "ACCESS",
            "NAME" => 'Группы',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arGroups,
            "SIZE"=>5
        ),
        "GROUPS_DIRECTORS" => array(
            "PARENT" => "ACCESS",
            "NAME" => 'Директоры',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arGroups,
            "SIZE"=>5
        ),

        "LIST_URL" =>array(
            "PARENT"=>"",
            "NAME"=>"Урл списка",
            "DEFAULT"=>'/realty/removed/'
        ),

        "PROPERTY_151" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Квартиры',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>5
        ),

        "PROPERTY_152" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Дома',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>5
        ),

        "PROPERTY_153" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Осабняк',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>5
        ),  
        
        "PROPERTY_154" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Дача',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>5
        ),         

        "PROPERTY_155" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Офиса',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>5
        ),

        "PROPERTY_156" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Ресторан',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>5
        ),

        "PROPERTY_157" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Магазина',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>5
        ),

        "PROPERTY_159" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Земли',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>5
        ),

        "PROPERTY_160" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Автосервиса',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>5
        ),

        "PROPERTY_161" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Гостиница',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>5
        ),

        "PROPERTY_162" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Парикмахерская',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>5
        ),

        "PROPERTY_158" => array(
            "PARENT" => "",
            "NAME" => 'Не полказать поля для Другое',
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNSF,
            "SIZE"=>5
        )
    ),
);