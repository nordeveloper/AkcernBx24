<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
    "NAME" => 'Сиписок недвижимости (Архивные)',
    "DESCRIPTION" => 'Сиписок недвижимости (Архивные)',
    "ICON" => "/images/news_list.gif",
    "SORT" => 10,
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
