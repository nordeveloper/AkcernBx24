
<?php
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if( strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){

    if($_REQUEST['id']>0){

        $arFilter['IBLOCK_ID'] = REALTY_IBLOCK_ID;
        $arFilter['ID'] = intval($_REQUEST['id']);

        $res = \CIBlockElement::GetList(Array("ID"=>"ASC"), $arFilter, false, array("nPageSize"=>1),
            array('ID', 'IBLOCK_ID', 'DETAIL_PICTURE')
        );

        $arResult = array();

       if($row = $res->Fetch()){
           $arResult[]['src'] = CFile::GetPath($row['DETAIL_PICTURE']);
       }

        $db_props = CIBlockElement::GetProperty($arFilter['IBLOCK_ID'], $arFilter['ID'], array("sort" => "asc"), Array("CODE"=>"MORE_PHOTO"));
        while($ar_props = $db_props->Fetch()){
            $prop = $ar_props["VALUE"];
            $arResult[]['src'] = CFile::GetPath($prop);
        }

        echo json_encode($arResult, JSON_UNESCAPED_UNICODE);
    }

}