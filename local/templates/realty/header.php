<!DOCTYPE html>
<html lang="<?=LANGUAGE_ID?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">    
    <title><?$APPLICATION->ShowTitle()?></title>
    <?
    $APPLICATION->ShowHead();
    use \Bitrix\Main\Page\Asset;
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/css/bootstrap.min.css');

    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/js/toastr/toastr.min.css');

    //Asset::getInstance()->addExternalCss("/bitrix/css/main/font-awesome.css");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/jquery-3.3.1.min.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/jquery-ui.min.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/popper.min.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/bootstrap.min.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/toastr/toastr.min.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/main.js');
    
    $arGroups = $USER->GetUserGroupArray();
    ?>
</head>

<body>
<?$APPLICATION->ShowPanel();?>

<div class="wrapper">
    <header class="header">
            <nav class="menu-head navbar navbar-dark navbar-expand-lg">
                <a class="navbar-brand mb-0 h4" href="/realty/">AKCERN Holding</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menuhead" aria-controls="menuhead" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="menuhead">
                <ul class="navbar-nav">
                    <li class="nav-item aside-show"><button class="btn"><i class="glyphicon glyphicon-menu-right"></i></button></li>
                    <li class="nav-item"><a class="nav-link" href="<?=$APPLICATION->GetCurPageParam("lang=ru", array("lang") )?>">Ру</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?=$APPLICATION->GetCurPageParam("lang=en", array("lang"))?>">En</a></li>
                    <li class="nav-item"><a class="nav-link" href="/realty/analitics/"><i class="glyphicon glyphicon-stats"></i> Аналитика</a></li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#"  id="QueueDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="glyphicon glyphicon-tasks"></i> Очередь</a>
                        <div class="dropdown-menu" aria-labelledby="QueueDropdown">
                            <a class="dropdown-item" href="/realty/queue/realty/?region=1">Недвижимость Ереван</a>
                            <a class="dropdown-item" href="/realty/queue/realty/?region=2">Недвижимость Регионы</a>
                            <a class="dropdown-item" href="/realty/queue/requets/?type=1">Запросы продажа</a>
                            <a class="dropdown-item" href="/realty/queue/requets/?type=2">Запросы аренда</a>
                            <!-- <a class="dropdown-item" href="/realty/queue/bonuse/">Бонусы</a> -->
                        </div>
                    </li>

                    <?
                    if( !array_intersect(1,13, $arGroups) ){?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#"  id="DropdowSetting" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="glyphicon glyphicon-cog"></i> Настройки</a>
                        <div class="dropdown-menu" aria-labelledby="DropdowSetting">
                            <a class="dropdown-item" href="/realty/settings/regions/">Регионы</a>
                            <a class="dropdown-item" href="/realty/settings/cities/">Города</a>
                            <a class="dropdown-item" href="/realty/settings/streets/">Улицы</a>
                            <a class="dropdown-item" href="/realty/settings/zones/">Зоны</a>
                        </div>
                    </li>
                    <? } ?>

                    <li class="nav-item"><a  class="nav-link" href="/crm/" target="_blank"><i class="glyphicon glyphicon-share-alt"></i> CRM</a></li>
                    <li class="nav-item"><a  class="nav-link" href="/company/personal/user/<?=$USER->GetID()?>/"><i class="glyphicon glyphicon-user"></i> <?=$USER->GetFullName()?></a></li>
                    <li><a class="nav-link" href="?logout=yes"><i class="glyphicon glyphicon glyphicon-log-out"></i></a></li>
                </ul>
                </div>
            </nav>

    <?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"topmenu", 
	array(
		"ALLOW_MULTI_SELECT" => "N",
		"ROOT_MENU_TYPE" => "top_".LANGUAGE_ID,
		"DELAY" => "N",
		"MAX_LEVEL" => "2",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"USE_EXT" => "N",
		"COMPONENT_TEMPLATE" => "topmenu",
		"MENU_CACHE_GET_VARS" => array(
		),
		"CHILD_MENU_TYPE" => "left_".LANGUAGE_ID
	),
	false
    );?>
    </header>

    <div class="content">
    <div class="container-fluid">