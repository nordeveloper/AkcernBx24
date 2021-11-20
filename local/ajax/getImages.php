
<?php 
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if( intval($_REQUEST['elID'])>0 ){
    
    $elementID = $_REQUEST['elID'];
    $res = CIBlockElement::GetProperty(REALTY_IBLOCK_ID, $elementID, "sort", "asc", Array('CODE'=>'MORE_PHOTO') );

    $id = $_REQUEST['elID'];

    $zipFile = '/upload/tmpzip/images_'.$id.'.zip';

    $zip = new ZipArchive;

    if(true === ($zip->open($_SERVER['DOCUMENT_ROOT'].$zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE))){

        while($row=$res->fetch()){

            $img = CFile::getPath($row['VALUE']);

            $zip->addFile($_SERVER['DOCUMENT_ROOT'].$img, basename($img));
        }

        $zip->close();
    }

    if(file_exists($_SERVER['DOCUMENT_ROOT'].$zipFile)){
        localRedirect($zipFile);
    }else{
        echo '<p style="color:red">Ошибка при скачивание архива картинок</p>';
    }    
}
