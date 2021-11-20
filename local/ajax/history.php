<?php
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule("iblock"))
    return;

CModule::IncludeModule('crm');

//if( strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){
//
//}

if(!empty($_REQUEST['add']) and intval($_REQUEST['REALTYID'])>0  and intval($_REQUEST['CLIENTID']) >0 ){

    $PROP['REALTYID'] = $_REQUEST['REALTYID'];
    $PROP['DEAL_TYPE'] = $_REQUEST['DEAL_TYPE'];
    $PROP['RATING'] = $_REQUEST['RATING'];
    $PROP['STATUS'] = $_REQUEST['STATUS'];

    $arContact = getContact($_REQUEST['CLIENTID']);
    $PROP['CLIENTID'] = $_REQUEST['CLIENTID'];
    $PROP['CLIENT_IDENT'] = $arContact['IDENTIFICATOR'];

    $res = CIBlockElement::GetProperty(REALTY_IBLOCK_ID, $_REQUEST['REALTYID'], array("sort" => "asc"), Array("CODE"=>"REALTY_TYPE"));
    if($obProp = $res->GetNext()) {
        $PROP['TYPE'] = $obProp['VALUE'];
    }

    $arFields = Array(
        "CREATED_BY"    => $USER->GetID(),
        "IBLOCK_ID"      => VISITS_HISTORY_IBLOCKID,
        "PROPERTY_VALUES"=> $PROP,
        "NAME"           => trim($_REQUEST['NAME']),
        "ACTIVE"         => "Y"
    );

    $el = new CIBlockElement;

    if($ID = $el->Add($arFields)){
        
        $result['ID'] = $ID;
        $arContact = getContact($_REQUEST['CLIENTID']);
        $oContact = new CCrmContact(false);
        $arCField['UF_CRM_1593888917'] = $arContact['VISITS_COUNT']+1;
        $oContact->Update($_REQUEST['CLIENTID'], $arCField);
    }
    else
        $result['status'] = 'error';
        $result['message'] = $el->LAST_ERROR;

    echo json_encode($result);
}else{
    $result['status'] = 'error';
    $result['message'] = 'Незаполнены обязательны поля';
    echo json_encode($result);
}


if(!empty($_REQUEST['update']) and intval($_REQUEST['REALTYID'])>0 and intval($_REQUEST['CLIENTID'])>0 ){

    $PROP = $_REQUEST;

    $arContact = getContact($_REQUEST['CLIENTID']);
    $PROP['CLIENT_IDENT'] = $arContact['IDENTIFICATOR'];

    $arFields = Array(
        "MODIFIED_BY"    => $USER->GetID(),
        "IBLOCK_ID"      => VISITS_HISTORY_IBLOCKID,
        "PROPERTY_VALUES"=> $PROP,
        "ACTIVE"         => "Y"
    );

    $el = new CIBlockElement;

    if($ID = $el->Update($_REQUEST['id'], $arFields))
        $result['ID'] = $ID;
    else
        $result['error'] = $el->LAST_ERROR;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}



if($_REQUEST['edit'] and intval($_REQUEST['id'])>0){

    $arFilter['IBLOCK_ID'] = VISITS_HISTORY_IBLOCKID;
    $arFilter['ID'] = $_REQUEST['id'];

    $res = \CIBlockElement::GetList(false, $arFilter, false, false,
        ['IBLOCK_ID', 'ID', 'NAME', 'PROPERTY_CLIENTID', 'PROPERTY_REALTYID', 'PROPERTY_RATING', 'PROPERTY_STATUS', 'PROPERTY_CLIENT_IDENT', 'PROPERTY_DEAL_TYPE']
    );

    if( $row = $res->fetch() ){
        $arContact = getContact($row['PROPERTY_CLIENTID_VALUE']);
    ?>

    <form id="form-history-edit"  style="max-width: 400px; margin: 0 auto">
        <div class="info-box"></div>
        <div class="form-group">
            <label>Клиент</label>
            <select name="CLIENTID" class="select2-contact form-control">
                <option value="<?=$row['PROPERTY_CLIENTID_VALUE']?>"><?=$arContact['FULL_NAME']?></option>
            </select>
        </div>
        <div class="form-group">
            <label>Код недвижимости</label>
            <select class="select2-realty form-control" name="REALTYID">
                <option value="<?=$row['PROPERTY_REALTYID_VALUE']?>"><?=$row['NAME']?></option>
            </select>
        </div>

        <div class="form-group">
            <label>Тип сделки</label>
            <select name="DEAL_TYPE" class="form-control">
                <option value=""></option>
                <option <? if($row['PROPERTY_DEAL_TYPE_ENUM_ID']==214):?>selected<?endif?> value="214">Аренда</option>
                <option <? if($row['PROPERTY_DEAL_TYPE_ENUM_ID']==215):?>selected<?endif?> value="215">Продажа</option>
            </select>
        </div>

        <div class="form-group">
            <label>Оценка</label>
            <select name="RATING" class="form-control">
                <option value=""></option>
                <option <? if($row['PROPERTY_RATING_ENUM_ID']==201):?>selected<?endif?>  value="201">Понравился</option>
                <option <? if($row['PROPERTY_RATING_ENUM_ID']==202):?>selected<?endif?> value="202">Не понравился</option>
            </select>
        </div>

        <div class="form-group">
            <label>Статус</label>
            <select name="STATUS" class="form-control">
                <option value=""></option>
                <option value="203" <? if($row['PROPERTY_STATUS_ENUM_ID']==203):?>selected<?endif?>>Посетил</option>
                <option value="204" <? if($row['PROPERTY_STATUS_ENUM_ID']==204):?>selected<?endif?>>Не посетил</option>
                <option value="205" <? if($row['PROPERTY_STATUS_ENUM_ID']==205):?>selected<?endif?>>Ожидает</option>
                <option value="206" <? if($row['PROPERTY_STATUS_ENUM_ID']==206):?>selected<?endif?>>В процессе</option>
            </select>
        </div>

        <div class="form-group" class="form-control">
            <input type="hidden" name="id" value="<?=$_REQUEST['id']?>">
            <input type="hidden" name="update" value="Y">
            <input type="button" name="submit" class=" btn btn-success" value="Сохранить">
        </div>
    </form>

    <?php } ?>

<? }?>