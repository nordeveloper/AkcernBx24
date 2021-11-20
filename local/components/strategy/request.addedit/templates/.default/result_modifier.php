<?php
if($arResult["ELEMENT"]['PROPERTIES']['CLIENTID']['VALUE']>0){
    $arContact =  CCrmContact::GetByID($arResult["ELEMENT"]['PROPERTIES']['CLIENTID']['VALUE'], true);

    $arResult['CLIENT'] = $arContact;

    if($arContact['ID']>0){

        $dbResMultiFields = CCrmFieldMulti::GetList( array(), array('ENTITY_ID' => 'CONTACT', 'ELEMENT_ID' => $arContact['ID']) );

        while($arMultiFields = $dbResMultiFields->Fetch())
        {
            $arContactFields[$arMultiFields['TYPE_ID']][] = $arMultiFields['VALUE'];
        }

        $arResult['CLIENT']['CONTACTS'] = $arContactFields;
    }
}


$arResult['ELEMENT']['VISITS_COUNT'] = 0;

if($arResult['ELEMENT']['ID']>0 and $arResult["ELEMENT"]['PROPERTIES']['CLIENTID']['VALUE']>0){
    $filter['ACTIVE']="Y";
    $filter['IBLOCK_ID'] = VISITS_HISTORY_IBLOCKID;
    $filter['PROPERTY_CLIENTID'] = $arResult["ELEMENT"]['PROPERTIES']['CLIENTID']['VALUE'];
    
    $resC = \CIBlockElement::GetList(array("NAME" => "ASC"), $filter, false, false,
        ['IBLOCK_ID', 'ID']
    );
    
    $arResult['ELEMENT']['VISITS_COUNT'] = $resC->selectedRowsCount();
}
