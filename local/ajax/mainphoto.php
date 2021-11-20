
<?php 
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if( strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){
        
	if (!CModule::IncludeModule("iblock"))
		return;

	// add main photo
    if( intval($_POST['elID'])>0 and $_FILES ){

        $el = new CIBlockElement;

        $elID = intval($_POST['elID']);

        $path=$_SERVER['DOCUMENT_ROOT'].'/upload/tmp/';
        $watermarksrc = $_SERVER['DOCUMENT_ROOT'].'/upload/logo.png'; 

        $imgPath = $path.$_FILES["image"]['name'];     

        if( move_uploaded_file( $_FILES["image"]['tmp_name'], $imgPath ) ){

            $fname = addWatermark($imgPath, $watermarksrc, 1000);

            $arElement = Array(
                "DETAIL_PICTURE" => CFile::MakeFileArray($fname)
            );

            if($res = $el->UPDATE($elID, $arElement)){
                $result['status'] = 'success';
                $result['file'] = $_FILES['image'];
            }else{
                $result['status']='error';
                $result['message'] = 'Ошибка при загрузки файла';
            }
            echo json_encode($result);
        }

    }


    //remove main photo
    if( intval($_REQUEST['elID'])>0 and intval($_REQUEST['imgID'])>0 ){

        $elID = $_REQUEST['elID'];

        $el = new CIBlockElement;

        $arElement = Array(
            "DETAIL_PICTURE" => array('del' => 'Y'),
        );

        if($res = $el->UPDATE($elID, $arElement)){

            $result['status'] = 'success';

            CIBlockElement::SetPropertyValuesEx($elID, REALTY_IBLOCK_ID, array('HAVEIMAGE' => 232) );

        }else{
            $result['status']='error';
            $result['message'] = 'Ошибка удаление файла';
        }

        echo json_encode($result);
    }

}