<?php
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

// $sql = "SELECT * FROM street_forweb WHERE name_ru like '%переулок%' order by id asc";
// $dbres = $DB->query($sql);

// $arResult= array();

// while( $row = $dbres->fetch() ){
//     dump($row);
//     $DB->query('')
// }

// $dbres = $DB->query("delete from street_forweb WHERE name_ru like '%проезд'");

// echo ExportStreets();

// if (!CModule::IncludeModule('iblock'))
// {
//     ShowError(GetMessage('CRM_MODULE_NOT_INSTALLED'));
//     return;
// }



// $arFilter = array(
//     'IBLOCK_ID'=>REALTY_IBLOCK_ID,
//     //'ACTIVE'=>'Y',
//     'PROPERTY_REGION'=>1//,
//     // 'PROPERTY_STREET'=>3177
// );


// $res = \CIBlockElement::GetList(array("ID"=>'ASC'), $arFilter, false, false, array('ID', 'NAME', 'ACTIVE', 'PROPERTY_STREET') );

// $i = 0;
// while($row = $res->fetch()){
    
//     // dump($row['NAME']);

//     // $street = Street::getById($row['PROPERTY_STREET_VALUE']);    

//     // $arStreet = explode(' ', $street['name_ru']);

    // if(in_array('проезд', $arStreet)){
    //     dump($row['NAME']);
    //     //dump($arStreet);
    //     //dump(array_pop($arStreet));
    //     //dump($arStreet);

    //     // $streetsweb = getStreetWeb($arStreet[0]);
    //     // CIBlockElement::SetPropertyValuesEx($row['ID'], 26, array('STREETWEB' =>$streetsweb['id'] ));
    //     // dump($streetsweb);
    //     $i++;
    // }
// }

echo '<br><br>updated:'.$i;



// echo $i;

// $filter['!EXTERNAL_AUTH_ID']='imconnector';
// $rsUsers = CUser::GetList(($by="ID"), ($order="asc"), $filter, array("SELECT"=>array("UF_*")) );

// while ($row = $rsUsers->fetch()){

//     dump($row['NAME'].':'.$row['UF_INFILTER']);

//     // $user = new CUser;
//     // $fields = Array( 
//     //      "UF_INFILTER" =>1, 
//     // ); 
//     // $user->Update($row['ID'], $fields);
// }
