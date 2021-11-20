
<?php 
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if( strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){

	if (!CModule::IncludeModule("iblock"))
		return;	

    if(intval($_POST['elID'])>0 and  intval($_POST['propvalID'])>0  ){

        $ELEMENT_ID = $_POST['elID'];
        $poropvalID = $_POST['propvalID'];
        $propid = $_POST['imgID'];

        $arFile["MODULE_ID"] = "iblock";
        $arFile["del"] = "Y";

        CIBlockElement::SetPropertyValueCode($ELEMENT_ID, "MORE_PHOTO", Array($poropvalID => Array("VALUE"=>$arFile) ) );
        CFile::Delete($propid);        

        $el = new CIBlockElement;
        $arFields['MODIFIED_BY'] = $USER->getID();
        $el->Update($elID, $arFields);     
        
        $_SESSION['notifymsg'] = 'Успешно выполнено';
        $result['STATUS'] = "OK";
        echo json_encode($result);
    }
    

    if( intval($_REQUEST['elID']) >0 and $_REQUEST['rmPhotos']=='Y'){

        $elementID = $_REQUEST['elID'];
        $res = CIBlockElement::GetProperty(REALTY_IBLOCK_ID, $elementID, "sort", "asc", Array('CODE'=>'MORE_PHOTO') );

        $arFile["MODULE_ID"] = "iblock";
        $arFile["del"] = "Y";

        while($row = $res->fetch()){

            $PROP['MORE_PHOTO'][$row['PROPERTY_VALUE_ID']] = $arFile;
            CIBlockElement::SetPropertyValuesEx($elementID, false, $PROP);
            CFile::Delete($row['VALUE']);
        }

        $el = new CIBlockElement;
        $arFields['MODIFIED_BY'] = $USER->getID();
        $el->Update($elID, $arFields);

        $_SESSION['notifymsg'] = 'Успешно выполнено';
        $result['STATUS'] = "OK";
        echo json_encode($result);
    }

}