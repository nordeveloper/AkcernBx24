<?php
AddEventHandler("crm", "OnAfterCrmLeadAdd", "OnAfterCrmLeadAddE");
function OnAfterCrmLeadAddE(&$arLead){

    if(!CModule::IncludeModule("crm")) return;
    $numeration = 'L-'.GenerateNumeration('LEAD');
    $crmlead = new CCrmLead();
    $fields['TITLE'] = $numeration;
    $crmlead->update($arLead['ID'], $fields);

    // sending notification to Dispachers
    
    $leadDb = CCrmLead::getList(Array('ID' => 'ASC'), array('ID'=>$arLead['ID']));

    if( $lead = $leadDb->fetch() ){

        if($lead['ASSIGNED_BY_ID']>0 and !empty($lead['SOURCE_ID']) ){

            $user = getUser($lead['ASSIGNED_BY_ID']);

            if( !empty($user['UF_DEPARTMENT'][0]) ){
                $dispatcher = getDepartmentDispatcher($user['UF_DEPARTMENT'][0]);
                // file_put_contents(__DIR__.'/../../logs/OnAfterCrmLeadAdd.log', "ASSIGNED_BY_ID ".$lead['ASSIGNED_BY_ID']." ".$lead['TITLE']." Department: ".$user['UF_DEPARTMENT'][0]."\n", FILE_APPEND);
                if( !empty($dispatcher['UF_DISPECHER']) ){                
                    sendMessageToChat($lead['ASSIGNED_BY_ID'], $dispatcher['UF_DISPECHER'], 'Ձեր տնօրենին նշանակվել է Lead: <a href="/crm/lead/details/'.$lead['ID'].'/">'.$lead['TITLE'].'</a>');        
                }
            }
        }
    }
}


AddEventHandler("crm", "OnAfterCrmLeadUpdate", "OnAfterCrmLeadUpdateE");
function OnAfterCrmLeadUpdateE(&$arLead){

    if(!CModule::IncludeModule("crm")) return;
    global $USER;

    if($arLead['STATUS_ID']=='JUNK'){

        $id = $arLead['ID'];
        global $DB;

        $sql1 = "SELECT * FROM st_realty_queue WHERE LEAD_ID='$id'";
        $res = $DB->Query($sql1);

        if($item = $res->fetch()){
            $Queue = new RDQueue('st_realty_queue');
            $res = $Queue->Remove($item['ID']);
            return true;
        }

        $sql2 = "SELECT * FROM st_requests_queue WHERE LEAD_ID='$id'";
        $res = $DB->Query($sql2);
        if($item = $res->fetch()){
            $Queue = new RDQueue('st_requests_queue');
            $res = $Queue->Remove($item['ID']);
            return true;
        }
    }

}




AddEventHandler("crm", "OnAfterCrmContactAdd", "OnAfterCrmContactAddE");
function OnAfterCrmContactAddE(&$arContact){

    if(!CModule::IncludeModule("crm")) return;
    global $USER;

    if($arContact['ASSIGNED_BY_ID']>0){
        $user_id = $arContact['ASSIGNED_BY_ID'];
    }else{
        if( method_exists($USER,'GetID') ){
            $user_id = $USER->GetID();
        }else{
            $user_id = $arContact['CREATED_BY_ID'];
        }
    }

    $User = getUser($user_id);


    if($arContact['LEAD_ID']>0){
        $lead =  CCrmLead::getByID($arContact['LEAD_ID']);
        $numeration = 'K'.$User['DEPARTMENT'].'+'.ltrim($lead['TITLE'], 'L-');
    }else{
        $numeration = 'K'.$User['DEPARTMENT'].'+'.GenerateNumeration('CONTACT');
    }

    // отправляем оповещение директору и диспечеру
    $director = CIntranetUtils::GetDepartmentManagerID($User['UF_DEPARTMENT'][0]);
    $dispatcher = getDepartmentDispatcher($User['UF_DEPARTMENT'][0]);

    if($director){
        sendMessageToChat($user_id, $director, 'Ստեղծվել է կոնտակտ ID:'.$arContact['ID'].' Կոդ։ <a href="/crm/contact/details/'.$arContact['ID'].'">'.$numeration.'</a>');
    }    

    if($dispatcher){
        sendMessageToChat($user_id, $dispatcher, 'Ստեղծվել է կոնտակտ ID:'.$arContact['ID'].' Կոդ։ <a href="/crm/contact/details/'.$arContact['ID'].'">'.$numeration.'</a>');
    }    


    //добавляем значение Идентификатор контакта UF_CRM_1593888387 и Подразделения UF_CRM_1599721372
    $crmContact = new CCrmContact();

    $fields['UF_CRM_1593888387'] = $numeration;
    $fields['UF_CRM_1599721372'] = $User['UF_DEPARTMENT'][0];
    $crmContact->update($arContact['ID'], $fields);

    
    // Проверка совпадении
    if(!empty($arContact['FM']['PHONE'])){
        foreach ($arContact['FM']['PHONE'] as $phone){

            $ducblicate = getPhoneDuplicate($phone['VALUE']);

            if(!empty($ducblicate)){
                $dublicateCont = current($ducblicate);
                if($arContact['ID']!=$dublicateCont['CONTACT_ID']){
                    $dublicateData= '<a target="_blank" href="/crm/contact/details/'.$arContact['ID'].'/">'.$arContact['NAME'].' '.$dublicateCont['PHONE'].'</a>';
                    $dublicateData.= ' c <a target="_blank" href="/crm/contact/details/'.$dublicateCont['CONTACT_ID'].'/">'.$dublicateCont['PHONE'].'  '.$dublicateCont['FULL_NAME'].'</a>';

                    $mess = 'Найден совпадения при добавление контакта '.$dublicateData ;
                    sendMessageToChat($user_id, INFO_MANAGER, $mess);
                    sendMessageToChat($user_id, INFO_MANAGER2, $mess);
                }
            }
        }
    }


    // если контак создался с Лида
    if($arContact['LEAD_ID']>0 and !empty($lead) ){

        $findByLeadId = $arContact['LEAD_ID'];
        $contactId = $arContact['ID'];

        $arSelect = Array("ID", "NAME", "PROPERTY_CONTACTID");
        $arFilter = Array("IBLOCK_ID"=>REALTY_IBLOCK_ID, "ACTIVE"=>"Y", "PROPERTY_CONTACTID"=>$findByLeadId);

        // file_put_contents(__DIR__.'/../../logs/EventContactAdd.Log', 'LeadId: '.$findByLeadId."\n", FILE_APPEND);

        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

        while($row = $res->fetch())
        {
            // file_put_contents(__DIR__.'/../../logs/EventContactAdd.log', $contactId."\n", FILE_APPEND);
            // file_put_contents(__DIR__.'/../../logs/EventContactAdd.log', 'RealtyId:'.$row['ID']."\n", FILE_APPEND);
            CIBlockElement::SetPropertyValuesEx($row['ID'], REALTY_IBLOCK_ID, array('OWNERID'=>$contactId));
            CIBlockElement::SetPropertyValuesEx($row['ID'], REALTY_IBLOCK_ID, array('CONTACTID'=>false));
            CIBlockElement::SetPropertyValuesEx($row['ID'], REALTY_IBLOCK_ID, array('NOTOWNER'=>false));            
        }
    }

}


/**** обработчик, добваления данные недвижимостьи в таблицу аналитики ****/

AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("AddToAnalitic", "OnAfterIBlockElementAddHandler"));
class AddToAnalitic
{
    function OnAfterIBlockElementAddHandler(&$arFields)
    {
        if($arFields["RESULT"]){

            $EL = $arFields;

            if($arFields['IBLOCK_ID']==26){

                $PROP = $arFields['PROPERTY_VALUES'];

                $data['USER_ID'] = $EL['CREATED_BY'];
                $data['REALTY_ID'] = $EL['ID'];
                $data['DATE_CREATED'] = $EL['DATE_CREATED'];
                $data['DEAL_TYPE'] = $PROP[92];
                $data['REALTY_TYPE'] = $PROP[92];
                $data['PRICE'] = $price = $PROP[90];
                $data['REGION'] = $PROP[96];
                $data['CITY'] = $PROP[284];
                $data['ZONE'] = $PROP[97];
                $data['ROOMS'] = $PROP[102];
                $data['FLOOR'] = $PROP[103];
                $data['TOTAL_AREA'] = $PROP[106];
                $data['BUILDING_TYPE'] =$PROP[95];
                $data['STATUS'] = $PROP[150];

                Analitic::AddRealty($data);
            }


            if($arFields['IBLOCK_ID']==28){

                $PROP = $arFields['PROPERTY_VALUES'];

                $dealtype = '';
                if($PROP[156]==149){
                    $dealtype = 70;
                }else if($PROP[156]==150){
                    $dealtype = 71;
                }

                $realtyType = array(151=>72, 152=>73, 153=>74, 154=>75, 155=>75, 156=>76, 157=>77, 159=>78, 160=>80, 161=>81, 162=>82);

                $data['USER_ID'] = $EL['CREATED_BY'];
                $data['REALTY_ID'] = $EL['ID'];
                $data['DATE_CREATED'] = $EL['DATE_CREATED'];
                $data['DEAL_TYPE'] = $dealtype;
                $data['REALTY_TYPE'] = $realtyType[$PROP[157]];
                $data['PRICE'] = $PROP[159];
                $data['REGION'] = $PROP[160];
                $data['CITY'] = $PROP[285];
                $data['ZONE'] = $PROP[161];
                $data['ROOMS'] = $PROP[165];
                $data['FLOOR'] = $PROP[166];
                $data['TOTAL_AREA'] = $PROP[170];
                $data['BUILDING_TYPE'] =$PROP[173];

                Analitic::AddRequest($data);
            }


            if($arFields['IBLOCK_ID']==35 and !empty($arFields['NAME']) ){

                if (!CModule::IncludeModule('iblock')) return;

                $findByListElId = $arFields['NAME'];
                $arSelect = Array("ID", "NAME", "PROPERTY_CONTACTID");
                $arFilter = Array("IBLOCK_ID"=>REALTY_IBLOCK_ID, "ACTIVE"=>"Y", "PROPERTY_CONTACTID"=>$findByListElId);

                $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

                $contactId = current($arFields['PROPERTY_VALUES']['KLIENT']);

                file_put_contents(__DIR__.'/../../logs/EventFindListElement.log', 'PROPERTY_CONTACTID: '.$findByListElId.' ContactID: '.$contactId."\n", FILE_APPEND);

                if($contactId>0){

                    while($row = $res->fetch())
                    {
                        file_put_contents(__DIR__.'/../../logs/EventUpdateElementContact.Log', 'ContactId: '.$contactId."\n", FILE_APPEND);
                        file_put_contents(__DIR__.'/../../logs/EventUpdateElementContact.Log','RealtyId:'.$row['ID']."\n", FILE_APPEND);

                        CIBlockElement::SetPropertyValuesEx($row['ID'], REALTY_IBLOCK_ID, array('OWNERID'=>$contactId));
                        CIBlockElement::SetPropertyValuesEx($row['ID'], REALTY_IBLOCK_ID, array('CONTACTID'=>false));
                        CIBlockElement::SetPropertyValuesEx($row['ID'], REALTY_IBLOCK_ID, array('NOTOWNER'=>false));
                    }
                }

            }
            
        }
    }
}


// Регистрируем обработчик события
\Bitrix\Main\EventManager::getInstance()->addEventHandler('main', 'onProlog', function () {
    // Проверим является ли страница детальной карточкой CRM через функционал роутинга компонентов
    $engine = new \CComponentEngine();

    $page = $engine->guessComponentPath(
        '/crm/',
        ['detail' => '#entity_type#/details/#entity_id#/'],
        $variables
    );

    // Если страница не является детальной карточкой CRM прервем выполенение
    if ($page !== 'detail') {
        return;
    }

    // Проверим валидность типа сущности
    $allowTypes = ['contact'];
    $variables['entity_type'] = strtolower($variables['entity_type']);
    if (!in_array($variables['entity_type'], $allowTypes, true)) {
        return;
    }

    // Проверим валидность идентификатора сущности
    $variables['entity_id'] = (int) $variables['entity_id'];
    if (0 >= $variables['entity_id']) {
        return;
    }

    $assetManager = \Bitrix\Main\Page\Asset::getInstance();

    // Подключаем js файл
    $assetManager->addJs('/local/templates/realty/js/crm.js');

    // Подготовим параметры функции
    $jsParams = \Bitrix\Main\Web\Json::encode(
        $variables,
        JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE
    );

    // Инициализируем добавление таба
    $assetManager->addString('
        <script>
        BX.ready(function () {
            if (typeof initialize_foo_crm_detail_tab === "function") {
                initialize_foo_crm_detail_tab('.$jsParams.');
            }
        });
        </script>
    ');
});








// AddEventHandler("iblock", "OnAfterIBlockElementUpdate", Array("EditListContact", "OnAfterIBlockElementUpdateHandler"));
// class EditListContact{

//     function OnAfterIBlockElementUpdateHandler(&$arFields){

//         if( !empty($arFields) and $arFields['IBLOCK_ID']==CONTACT_IBLOCK_ID ){

//             if( !empty($arFields['PROPERTY_VALUES'][307]) ){

//                 $findByListElId = $arFields['ID'];
//                 $arSelect = Array("ID", "NAME", "PROPERTY_CONTACTID");
//                 $arFilter = Array("IBLOCK_ID"=>REALTY_IBLOCK_ID, "ACTIVE"=>"Y", "PROPERTY_CONTACTID"=>$findByListElId);

//                 file_put_contents(__DIR__.'/filndFilter.Log',print_r($arFilter,1), FILE_APPEND);

//                 $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

//                 $contactId = current($arFields['PROPERTY_VALUES'][307]);
//                 while($row = $res->fetch())
//                 {
//                     file_put_contents(__DIR__.'/filndedEl.Log', $contactId."\n", FILE_APPEND);
//                     file_put_contents(__DIR__.'/eleUpd.Log',print_r($row), FILE_APPEND);

//                     CIBlockElement::SetPropertyValuesEx($row['ID'], REALTY_IBLOCK_ID, array('OWNERID'=>$contactId));
//                     CIBlockElement::SetPropertyValuesEx($row['ID'], REALTY_IBLOCK_ID, array('CONTACTID'=>false));
//                     CIBlockElement::SetPropertyValuesEx($row['ID'], REALTY_IBLOCK_ID, array('NOTOWNER'=>false));
//                 }

//             }
//         }

//     }
// }