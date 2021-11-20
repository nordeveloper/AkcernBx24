<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
    "NAME" => 'Сиписок недвижимости',
    "DESCRIPTION" => 'Сиписок недвижимости',
    "ICON" => "/images/news_list.gif",
    "SORT" => 20,
    "CACHE_PATH" => "Y",
    "PATH" => array(
        "ID" => "Realty",
        "CHILD" => array(
            "ID" => "realtylist",
            "NAME" => 'Недвижимость',
            "SORT" => 10,
        ),
    ),
);
?>
