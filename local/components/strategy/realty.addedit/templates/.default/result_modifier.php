<?php
if( !empty($arResult['ELEMENT']) ){

    $arResult['OWNER']['CONTACTS'] = array();

    if($arResult['ELEMENT']['PROPERTIES']['OWNERID']['VALUE']>0){
       $arResult['OWNER']['CONTACTS'] = getContact($arResult['ELEMENT']['PROPERTIES']['OWNERID']['VALUE']);
    }

    $arResult['ELEMENT']['VISITS_COUNT'] = 0;
    $filter['ACTIVE']="Y";
    $filter['IBLOCK_ID'] = VISITS_HISTORY_IBLOCKID;
    $filter['PROPERTY_REALTYID'] = $arResult['ELEMENT']['ID'];

    $res = \CIBlockElement::GetList(array("NAME" => "ASC"), $filter, false, false,
        ['IBLOCK_ID', 'ID']
    );

    $arResult['ELEMENT']['VISITS_COUNT'] = $res->selectedRowsCount();
}


// Костыль для сортировки дополнительных изображений.

if( !empty($arResult['ELEMENT']['PROPERTIES']['MORE_PHOTO']['VALUE']) ){


    foreach($arResult['ELEMENT']['PROPERTIES']['MORE_PHOTO']['VALUE'] as $imgid)
    {
        $arPHOTO[]=CFile::GetFileArray($imgid);
    }

    // foreach($arPHOTO as $key => $value)
    // {
    //     $arPHOTO[$key] = $value;
    //     $arPHOTO[$key]['SORT'] =  intval( $value['DESCRIPTION']);
    //     $sorts[$key] =  $arPHOTO[$key]['SORT'];
    // }

    // array_multisort($sorts, SORT_ASC, $arPHOTO);
    $arResult['MORE_PHOTO'] = $arPHOTO;

    // dump($arResult['MORE_PHOTO']);

    // dump($arResult['ELEMENT']['PROPERTIES']['MORE_PHOTO']);
}