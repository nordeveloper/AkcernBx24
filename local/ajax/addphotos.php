<?php 
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule("iblock"))
    return;

if( strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){

    if( intval($_POST['elID'])>0 and !empty($_FILES["images"]) ){

        $elID = intval($_POST['elID']);

        for($i = 0; $i < count($_FILES["images"]['name']); $i++){

            $path=$_SERVER['DOCUMENT_ROOT'].'/upload/tmp/';
            $watermarksrc = $_SERVER['DOCUMENT_ROOT'].'/upload/logo.png'; 

            // $num = substr( md5( mt_rand( 1,9999999999 ) ),0,9);    
            $imgPath = $path.$_FILES["images"]['name'][$i];            

            if( move_uploaded_file( $_FILES["images"]['tmp_name'][$i], $imgPath ) ){

                $filePath  = addWatermark($imgPath, $watermarksrc, 1000);

                $file = CFile::MakeFileArray($filePath);

                $arFiles[] = array('VALUE' => $file, 'DESCRIPTION' => $i);
            }
        }

        if(!empty($arFiles)){
            
            CIBlockElement::SetPropertyValues($elID, REALTY_IBLOCK_ID, $arFiles, 'MORE_PHOTO');

            CIBlockElement::SetPropertyValuesEx($elID, REALTY_IBLOCK_ID, array('HAVEIMAGE' => 232) );

            $el = new CIBlockElement;
            $arFields['MODIFIED_BY'] = $USER->getID();
            $el->Update($elID, $arFields);

            foreach($arFiles as $delFile){
               unlink($delFile['VALUE']['tmp_name']);
            }

            $resEl = CIBlockElement::GetList( array(), array('ID'=>$elID), false, false, array('ID', 'NAME', 'CREATED_BY', 'PROPERTY_REALTY_TYPE') );

            if($arElement = $resEl->fetch() ){

                $dep = CIntranetUtils::GetUserDepartments($arElement['CREATED_BY']);
                $director = CIntranetUtils::GetDepartmentManagerID($dep[0]);

                if($director>0){
                   sendMessageToChat($USER->getID(), $director, 'Добавилен фото к недвижимости <a href="/realty/realtyinfo/?type='.$arElement['PROPERTY_REALTY_TYPE_VALUE'].'&ID='.$arElement['ID'].'">'. $arElement['NAME'].'</a>');
                }

                $department = getDepartmentDispatcher($dep[0]);
                sendMessageToChat($USER->GetID(), $department['UF_DISPECHER'], 'Добавилен фото к недвижимости <a href="/realty/realtyinfo/?type='.$arElement['PROPERTY_REALTY_TYPE_VALUE'].'&ID='.$arElement['ID'].'">'. $arElement['NAME'].'</a>');
            }           

        }

        $_SESSION['notifymsg'] = 'Успешно выполнено';
        $result['status']='success';
        echo json_encode($result);
    }
        
}