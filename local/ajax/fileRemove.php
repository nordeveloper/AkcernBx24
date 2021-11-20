
<?php
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if( strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){

    if (!CModule::IncludeModule("iblock"))
        return;

    if($_POST['elID']>0 and $_POST['fileID']>0){

        $ELEMENT_ID = intval($_POST['elID']);
        $poropvalID = intval($_POST['propvalID']);
        $propid = intval($_POST['fileID']);

        $arFile["MODULE_ID"] = "iblock";
        $arFile["del"] = "Y";

        CIBlockElement::SetPropertyValueCode($ELEMENT_ID, "CERTDOC", Array($poropvalID => Array("VALUE"=>$arFile) ) );
        CFile::Delete($propid);
        $result['status'] = "OK";

    }else{
        $result['status'] = "Error element ID or Image ID";
    }

    echo json_encode($result);
}