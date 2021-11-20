<?php

// agents property D unset by date
function DeactiveD(){

    if (!CModule::IncludeModule('iblock')) {
        return false;
    }

    $arFilter = array('IBLOCK_ID'=>REALTY_IBLOCK_ID, "ACTIVE"=>"Y", "PROPERTY_DEAL_TYPE"=>70, "!PROPERTY_D"=>false, "<PROPERTY_RENTDATETO"=>date('Y-m-d'));

    $dbres = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter, false, false,
        ['IBLOCK_ID', 'ID', 'NAME', 'PROPERTY_D', 'PROPERTY_RENTDATEFROM', 'PROPERTY_RENTDATETO']
    );

    while ($row = $dbres->fetch()){
        CIBlockElement::SetPropertyValuesEx($row['ID'], REALTY_IBLOCK_ID, array('D' => FALSE));
        CIBlockElement::SetPropertyValuesEx($row['ID'], REALTY_IBLOCK_ID, array('RENTDATEFROM' => FALSE));
        CIBlockElement::SetPropertyValuesEx($row['ID'], REALTY_IBLOCK_ID, array('RENTDATETO' => FALSE));
    }

    return "DeactiveD();";
}


function DeactiveLockedRealty(){

    if (!CModule::IncludeModule('iblock')) {
        return false;
    }
    global $DB;

    $dbRes = $DB->query("SELECT * FROM ak_realty_locked WHERE ACTIVE=1");
    while($row = $dbRes->fetch()){
        CIBlockElement::SetPropertyValuesEx($row['REALTY_ID'], REALTY_IBLOCK_ID, array('LOCKED' => 0));
    }

    $SQL = "UPDATE ak_realty_locked SET ACTIVE=0";
    $DB->Query($SQL);

    // file_put_contents(__DIR__.'/DeactiveLockedRealty.log', print_r($row,true), FILE_APPEND);
    return "DeactiveLockedRealty();";
}


function DeactiveLockedAddress(){
    global $DB;

    $SQL = "UPDATE ak_locked_address SET ACTIVE=0";
    $DB->Query($SQL);
    // file_put_contents(__DIR__.'/DeactiveLockedAddress.log', print_r($SQL,true), FILE_APPEND);
    return "DeactiveLockedAddress();";
}

function ExportRegions(){
    $arResult = Region::getList();
    $jsondata['data'] = json_encode($arResult, JSON_UNESCAPED_UNICODE);
    restCommand('https://akcern.am/en/import/region', $jsondata);
}


function ExportCities(){
    $arResult = City::getList();
    $jsondata['data'] = json_encode($arResult, JSON_UNESCAPED_UNICODE);

   
    $res  = restCommand('https://akcern.am/en/import/city', $jsondata);
    return "ExportCities();";
}


function ExportStreets(){
    $arResult['ITEMS'] = Streetforweb::getList();
    $jsondata['data'] = json_encode($arResult, JSON_UNESCAPED_UNICODE);
    $res  = restCommand('https://akcern.am/en/import/street', $jsondata);
    return "ExportStreets();";
}


function ExportDistricts()
{
    $arResult = District::getList();
    $jsondata['data'] = json_encode($arResult, JSON_UNESCAPED_UNICODE);
    $res  = restCommand('https://akcern.am/en/import/districts', $jsondata);
}

function ExportRealty()
{

    if (!CModule::IncludeModule('iblock'))
    {
        ShowError('MODULE IBLOCK NOT INSTALLED');
        return;
    }

    echo 'Cron Sarted: '.date('d.m.Y H:i:s');
    file_put_contents(__DIR__.'/../logs/ExportRealty.log', 'Cron Begin '.date('d.m.Y H:i:s')."\n");

    //$ntop = Array ("nTopCount" => 1);
    $ntop = false;

    $arFilter = array(
        'IBLOCK_ID'=>REALTY_IBLOCK_ID,
        'ACTIVE'=>'Y', 
        'PROPERTY_X'=>false,
        'PROPERTY_VERIFAED'=>false,
        'PROPERTY_MATCHED'=>false,
        '>TIMESTAMP_X'=>date('Y-m-d', strtotime( date('Y-m-d').' -10 days' ) )
    );    

    $res = \CIBlockElement::GetList(array("ID"=>'ASC'),
        $arFilter,
        false, $ntop,
        false
    );

    $skipFields = array(
        'OWNERID', 'CLIENTID', 'HOME', 'APARTMENT', 'X', 'LINE', 'MARK', 'RENTDATEFROM', 'RENTDATETO', 'RATING', 'STATUS', 'OTHER_PARAMETERS', 
        'PHONE', 'DEPARTMENT', 'VERIFAED', 'FACEBOOK_DATE','FACEBOOK_LINK', 'LOCKED', 'CRON', 'OWNER_CODE', 'CERTNUM', 'CERTDOC', 'CONTRACT_TYPE', 'VALUATION', 'MATCHED'
    );

    $i = 0;    

    while( $ob = $res->GetNextElement() ) {

        $arItem = $ob->GetFields();

        $arElement = $arItem;

        unset($arElement['IBLOCK_ID']);
        unset($arElement['~NAME']);
        unset($arElement['~IBLOCK_ID']);
        unset($arElement['~ID']);
        unset($arElement['~DATE_CREATE']);
        unset($arElement['~TIMESTAMP_X']);
        unset($arElement['~DETAIL_PICTURE']);

        if(strtotime($arItem['DATE_ACTIVE_TO'])-strtotime(date('d.m.Y')) < 0  ){
            $arElement['IS_ARCHIVE'] = 'Y';
        }else{
            $arElement['IS_ARCHIVE'] = 'N';
        }

        $arProps = $ob->GetProperties();

        foreach ($arProps as $key => $PROPERTY) {

            unset($PROPERTY['TIMESTAMP_X']);
            unset($PROPERTY['IBLOCK_ID']);
            unset($PROPERTY['ACTIVE']);
            unset($PROPERTY['DEFAULT_VALUE']);
            unset($PROPERTY['~DEFAULT_VALUE']);
            unset($PROPERTY['FILE_TYPE']);
            unset($PROPERTY['LIST_TYPE']);
            unset($PROPERTY['TMP_ID']);
            unset($PROPERTY['FILTRABLE']);
            unset($PROPERTY['PROPERTY_VALUE_ID']);
            unset($PROPERTY['USER_TYPE']);
            unset($PROPERTY['USER_TYPE_SETTINGS']);
            unset($PROPERTY['VALUE_SORT']);
            unset($PROPERTY['XML_ID']);
            unset($PROPERTY['MULTIPLE_CNT']);
            unset($PROPERTY['ROW_COUNT']);
            unset($PROPERTY['COL_COUNT']);
            unset($PROPERTY['LINK_IBLOCK_ID']);
            unset($PROPERTY['WITH_DESCRIPTION']);
            unset($PROPERTY['SEARCHABLE']);
            unset($PROPERTY['SORT']);
            unset($PROPERTY['IS_REQUIRED']);
            unset($PROPERTY['VERSION']);
            unset($PROPERTY['DESCRIPTION']);
            unset($PROPERTY['~DESCRIPTION']);
            unset($PROPERTY['~NAME']);
            unset($PROPERTY['~VALUE']);

            if(in_array($key, $skipFields)) continue;
         

            if($key=='MORE_PHOTO' and !empty($PROPERTY['VALUE']) and empty($arProps['NOTIMAGE']['VALUE']) ){

                $images = array();

                if( $arItem['DETAIL_PICTURE'] >0 ){           
                    $images[] = CFile::GetPath($arItem['DETAIL_PICTURE']);
                }

                foreach($PROPERTY['VALUE'] as $imgID){
                  
                    if(getImageStatus($imgID)) continue;
                                            
                    $images[] = CFile::GetPath($imgID);
                }
                
                $arElement['PROPERTIES']['IMAGES']['VALUE'] = $images;                
            }            

            unset($arElement['PROPERTIES']['MORE_PHOTO']);

            $arElement['PROPERTIES'][$PROPERTY['CODE']]= $PROPERTY;

            $arElement['PROPERTIES']['DISTRICT']['VALUE'] = getDisctictByZone($arElement['PROPERTIES']['ZONE']['VALUE']);
        }
        
        $jsondata['data'] = json_encode($arElement , JSON_UNESCAPED_UNICODE);
        $response = restCommand('https://akcern.am/en/import', $jsondata);
       

        if( $response['info']['http_code']==200 ){

            $arResp = json_decode($response['result'],true);

            if($arResp['status']=='Success'){
               $i++;
                
               CIBlockElement::SetPropertyValuesEx( $arItem['ID'], $arItem["IBLOCK_ID"], array('CRON'=>false) );

               file_put_contents(__DIR__.'/../logs/RealtyExportSuccess.log', 'ID:'.$arItem['ID']."\n", FILE_APPEND);

            }else{
               file_put_contents(__DIR__.'/../logs/RealtyExportError.log', 'ID:'.$arItem['ID']." Error no success\n", FILE_APPEND);
            }

        }else{

            file_put_contents(__DIR__.'/../logs/RealtyExportError.log', print_r($response,1), FILE_APPEND);
        }              
    }

    $log = 'Cron end '.date('d.m.Y H:i:s').' Total Count:'.$i."\n";
    file_put_contents(__DIR__.'/../logs/RealtyExportSuccess.log', $log, FILE_APPEND);
}

function ExportRemoved(){
    $arFilter = array(
        'IBLOCK_ID'=>REALTY_IBLOCK_ID,
        'ACTIVE'=>'N',
        '>TIMESTAMP_X'=>date('Y-m-d', strtotime( date('Y-m-d').' -10 days' ) )
    );

    //$ntop = Array ("nTopCount" => 1000);
    $ntop = false;

    $res = \CIBlockElement::GetList(array("ID"=>'DESC'),
        $arFilter,
        false, $ntop,
        ['IBLOCK_ID', 'NAME', 'ID', 'TIMESTAMP_X']
    );

    while( $row = $res->fetch() ) {
        
        // dump($row);
        $jsondata['data'] = json_encode($row); 

        $response = restCommand('https://akcern.am/en/removed', $jsondata);

        if( $response['info']['http_code']==200 ){
           $arResp = json_decode($response['result'],true);
           if($arResp['status']=='Success'){
            file_put_contents(__DIR__.'/../logs/cronRelatyRemoved.log', $arResp['status'].' CRM Realty ID:'.$row['ID'].' '.date('d.m.Y H:i:s') );  
           }
        }
    }    
}


function ExportBlog(){
    if (!CModule::IncludeModule('iblock'))
    {
        ShowError('MODULE IBLOCK NOT INSTALLED');
        return;
    }

    //$ntop = Array ("nTopCount" => 1);
    $ntop = false;

    $res = \CIBlockElement::GetList(array("ID"=>'ASC'),
        array(
            'IBLOCK_ID'=>BLOG_IBLOCK_ID, 'ACTIVE'=>'Y', 'SHOW_NEW'=>'Y',
            'PROPERTY_CRON'=>false
        ),
        false, false,
        ['IBLOCK_ID', 'NAME', 'ID', 'CODE', 'DATE_CREATE', 'TIMESTAMP_X', 'DETAIL_PICTURE', 'PREVIEW_PICTURE', 'DETAIL_TEXT',
            'PROPERTY_NAME_RU',
            'PROPERTY_NAME_EN',
            'PROPERTY_TEXT_RU',
            'PROPERTY_TEXT_EN',
        ]
    );

    while( $row = $res->fetch() ) {

       $arItem['id']  = $row['ID'];
       $arItem['date_create'] = $row['DATE_CREATE'];
       $arItem['slug'] = $row['CODE'];
       $arItem['name'] = $row['NAME'];
       $arItem['name_ru'] = $row['PROPERTY_NAME_RU_VALUE'];
       $arItem['name_en'] = $row['PROPERTY_NAME_EN_VALUE'];

       $arItem['description'] = $row['DETAIL_TEXT'];
       $arItem['description_ru'] = $row['PROPERTY_TEXT_RU_VALUE']['TEXT'];
       $arItem['description_en'] = $row['PROPERTY_TEXT_EN_VALUE']['TEXT'];
       $arItem['image'] = CFile::GetPath($row['DETAIL_PICTURE']);

//      $jsondata['data'] = json_encode($arResult, JSON_UNESCAPED_UNICODE);
        $response  = restCommand('https://akcern.am/blog/import', $arItem);
        

        if( $response['info']['http_code']==200 ){
            
            $arResponse = json_decode($response,true);

            file_put_contents(__DIR__.'/ExportBlogResp.txt', $arItem['ID']."\n", FILE_APPEND);
            if($arResponse['status']=='Success'){
                CIBlockElement::SetPropertyValuesEx($arItem['ID'], $arItem["IBLOCK_ID"], array('CRON'=>1));
            }
        }else{
            echo 'No Resposne';
        }

    }

    return "ExportBlog();";
}

// end agents



