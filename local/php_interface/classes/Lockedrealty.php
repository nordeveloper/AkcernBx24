<?php


class Lockedrealty
{

    public static function getList($arFilter = array(), $byhour=false){
        global $DB;

        $where = 'WHERE 1';

        if( !empty($arFilter) ){
            foreach ($arFilter as $key=>$filter){
                $where.=" AND $key = '".$filter."'";
            }
        }

        $sql = "SELECT * FROM ak_realty_locked $where ORDER BY ID DESC";
        $dbRes = $DB->Query($sql);

        While( $row = $dbRes->fetch() ){
            $arUser = getUser($row['USER_ID']);
            $row['USER_NAME'] = '<a target="_blank" href="/company/personal/user/'.$arUser['ID'].'/">'.$arUser['FULL_NAME'].'</a>';
            if( strtotime($row['DATE_CREATED'])>0 ){
                $row['DATE_CREATED'] = date('d.m.Y H:i:s', strtotime($row['DATE_CREATED']) );
            }
            $arResult[] = $row;
        }

        return $arResult;
    }


    public static function getByID($id){
        if($id>0){
            global $DB;
            $id  = intval($id);

            $dbRes = $DB->query("SELECT * FROM ak_realty_locked WHERE REALTY_ID = '".$id."'");

            if($row = $dbRes->fetch()){
                return $row;
            }
        }
    }


    public static function getByRealtyID($id){
        if( intval($id) >0){
            global $DB;
            $dbRes = $DB->query("SELECT * FROM ak_realty_locked WHERE ACTIVE=1 AND REALTY_CODE = '".$id."'");
            if($row = $dbRes->fetch()){
                return $row;
            }
        }
    }


    public static function getByCode($code){
        if($code){
            global $DB;
            $code  = trim($code);
            $dbRes = $DB->query("SELECT * FROM ak_realty_locked WHERE ACTIVE=1 AND REALTY_CODE = '".$code."'");
            if($row = $dbRes->fetch()){
                return $row;
            }
        }
    }


    public static function Add($arData = array()){

        global $DB;
        $result = false;

        if( $arData['user_id']>0 and !empty($arData['realty_code']) ){

            $arFilter['IBLOCK_ID']= REALTY_IBLOCK_ID;
            $arFilter['NAME'] = trim($arData['realty_code']);
            $dbResH = \CIBlockElement::GetList(Array("ID"=>"ASC"), $arFilter, false, Array("nPageSize"=>1),
                ['IBLOCK_ID', 'ID', 'NAME']
            );

            if($arRealty = $dbResH->fetch()){

                $isLocked = self::getByCode($arRealty['NAME']);

                // если недвижимость не блокирован
                if( empty($isLocked) ){

                    $arFields = array(
                        "ACTIVE"=>"'1'",
                        "USER_ID"=>"'".$arData['user_id']."'",
                        "REALTY_ID"=>"'".$arRealty['ID']."'",
                        "REALTY_CODE"=>"'".$DB->ForSql($arRealty['NAME'])."'",
                        "DATE_CREATED"=>$DB->GetNowFunction(),
                    );

                    $DB->PrepareFields("ak_realty_locked");
                    $DB->StartTransaction();

                    $id = $DB->Insert("ak_realty_locked", $arFields, $err_mess.__LINE__);
                    if(strlen($strError)<=0 and $id>0){
                        $DB->Commit();
                        $result['id'] = $id;
                        $result['SUCCESS'] = 'Блокировка успешно добавлен';
                    }

                    if( !empty($result['SUCCESS']) ){
                        CIBlockElement::SetPropertyValuesEx($arRealty['ID'], REALTY_IBLOCK_ID, array('LOCKED' => 1));
                    }
                }else{
                    $result['ERROR'] = 'Неждвижимост кодом '. $arRealty['NAME']. ' уже блокирован';
                }
            }

        }else{
            $result['ERROR'] = 'Риелтор и код недвижимости обязательно';
        }

        return $result;
    }


    public static function Remove($id){
        if($id>0){
            global $DB;
            $DB->Query("DELETE FROM ak_realty_locked WHERE ID='$id'");
        }
    }


    public static function getLocked($user_id, $realty_code){
        global $DB;
        $result = false;

        $curdate = date('Y-m-d');
        $datefrom = date( "Y-m-d", strtotime( $curdate . "-2 day"));
        $dateto = date( "Y-m-d", strtotime( $curdate . "+1 day"));

        $sql = "SELECT * FROM ak_realty_locked WHERE REALTY_CODE='$realty_code' AND DATE_CREATED BETWEEN '$datefrom' AND '$dateto' and USER_ID='$user_id'";
        $dbRes = $DB->Query($sql);

        if(!empty($dbRes)){
            while( $row = $dbRes->fetch() ){
                $result['data'][] = $row;
                $result['count']++;
            }
            return $result;
        }
    }


    public static function getUserLockedCount($user_id, $realty_code){
        if($user_id>0){
            global $DB;

            $curdate = date('Y-m-d');
            $datefrom = date( "Y-m-d", strtotime( $curdate . "-1 day"));
            $dateto = date( "Y-m-d", strtotime( $curdate . "+1 day"));

            $sql = "SELECT COUNT(ID) as COUNT FROM ak_realty_locked WHERE ACTIVE=1 AND REALTY_CODE='$realty_code' AND DATE_CREATED BETWEEN '$datefrom' AND '$dateto' and USER_ID='$user_id'";

            $dbRes = $DB->Query($sql);
            if(!empty($dbRes)){
                $res = $dbRes->fetch();
                return $res;
            }
        }
    }


}