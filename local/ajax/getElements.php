<?php
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule("iblock"))
    return;

use \Bitrix\Main\Page\Asset;
Asset::getInstance()->addCss('/bitrix/components/bitrix/main.ui.grid/templates/.default/style.min.css');

$arFilter['IBLOCK_ID'] = REALTY_IBLOCK_ID;
$arFilter['ACTIVE'] = "Y";

//if(!empty($_REQUEST['name'])){
//    $arFilter['%NAME'] = trim($_REQUEST['name']);
//}

if(!empty( $_REQUEST['PARAMS']['CONTACT_ID'])){
    $arFilter['PROPERTY_OWNERID'] = $_REQUEST['PARAMS']['CONTACT_ID'];
    $arSelect = array("*", "PROPERTY_*");

    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

    $str = '<table style="width: 100%; border-bottom: solid 1px #ccc">';
    $str.='<tr style="border-bottom: solid 1px #ccc">
            <td style="border-bottom: solid 1px #ccc; padding:5px; background: #fff"><b>Код</b></td>
            <td style="border-bottom: solid 1px #ccc; padding:5px; background: #fff"><b>Тип сделки</b></td>
            <td style="border-bottom: solid 1px #ccc; padding:5px; background: #fff"><b>Тип недвижимости</b></td>
            <td style="border-bottom: solid 1px #ccc; padding:5px; background: #fff"><b>Цена</b></td>
            <td style="border-bottom: solid 1px #ccc; padding:5px; background: #fff"><b>Зона</b></td>
        </tr>';
    while($row = $res->GetNextElement())
    {
        $el = $row->getFields();
        $arProps = $row->getProperties();

        $str.='<tr style="border-bottom: solid 1px #ccc">
<td style="border-bottom: solid 1px #ccc; padding:5px; background: #fff"><a target="_blank" href="/realty/realtyinfo/?ID='.$el['ID'].'&type='.$arProps['REALTY_TYPE']['VALUE_ENUM_ID'].'">'.$el['NAME'].'</a>
</td>
<td style="border-bottom: solid 1px #ccc;padding:5px; background: #fff">'.$arProps['DEAL_TYPE']['VALUE'].'</td>
<td style="border-bottom: solid 1px #ccc;padding:5px; background: #fff">'.$arProps['REALTY_TYPE']['VALUE'].'</td>
<td style="border-bottom: solid 1px #ccc;padding:5px; background: #fff">'.$arProps['PRICE']['VALUE'].'</td>
<td style="border-bottom: solid 1px #ccc;padding:5px; background: #fff">'.$arProps['ZONE']['VALUE'].'</td>
';

//        foreach ($arProps as $arProp){
//            if(!empty($arProp['VALUE'])){
//                $str.='<td class="main-grid-cell">'.$arProp['NAME'].'</td><td class="main-grid-cell">'.$arProp['VALUE'].'</td>';
//            }
//        }

        $str.='</tr>';
    }
    $str.='<table>';

    echo $str;
}