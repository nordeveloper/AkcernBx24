<?php

class Lockedaddress
{

    public static function getList($arFilter = array()){

        global $DB;
        $where = 'WHERE 1';

        if( !empty($arFilter) ){
            foreach ($arFilter as $key=>$filter){
                $where.=" AND ".strtoupper($key)." = '".$filter."'";
            }
        }

        $sql = "SELECT * FROM ak_locked_address $where ORDER by ID DESC";
        $dbRes = $DB->Query($sql);

        $arResult = false;

        While( $row = $dbRes->fetch() ){
            $arUser = getUser($row['USER_ID']);
            $row['USER_NAME'] = '<a target="_blank" href="/company/personal/user/'.$arUser['ID'].'/">'.$arUser['FULL_NAME'].'</a>';
            if( strtotime($row['DATE'])>0 ){
                $row['DATE'] = date('d.m.Y H:i:s', strtotime($row['DATE']) );
            }
            $arResult[] = $row;
        }

        return $arResult;
    }


    public static function getByID($id){
        if($id>0){
            global $DB;
            $id  = intval($id);

            $dbRes = $DB->query("SELECT * FROM ak_locked_address WHERE REALTY_ID = '".$id."'");

            if($row = $dbRes->fetch()){
                return $row;
            }
        }
    }


    public static function Add( $arData = array() ){

        global $DB;

        if(intval($arData['user_id'])<1){
            $result['ERROR'] = 'Поле Риелтор обязательно для заполнения';
        }

        if( $arData['deal_type']<1){
            $result['ERROR'] = 'Тип сделки обязательно для заполнения';
        }

        if( empty($arData['zone'])){
            $result['ERROR'] = 'Поле зона обязательно для заполнения';
        }

        if(intval($arData['street'])<1){
            $result['ERROR'] = 'Поле улица обязательно для заполнения';
        }

        if(empty($arData['home'])){
            $result['ERROR'] = 'Дом обязательно для заполнения';
        }


        $result = false;

        if( empty($result['ERROR']) ){

            $arFields = array(
                "ACTIVE"=>"'1'",
                "USER_ID"=>"'".$arData['user_id']."'",
                "DEAL_TYPE"=>"'".$arData['deal_type']."'",
                "DATE"=>$DB->GetNowFunction(),
                "REGION"=>"'".$arData['region']."'",
                "CITY"=>"'".$arData['city']."'",
                "ZONE"=>"'".$DB->ForSql($arData['zone'])."'",
                "STREET"=>"'".$arData['street']."'",
                "HOME"=>"'".$DB->ForSql($arData['home'])."'",
                "APARTMENT"=>"'".$DB->ForSql($arData['apartment'])."'"
            );

            $DB->PrepareFields("ak_locked_address");
            $DB->StartTransaction();

            $id = $DB->Insert("ak_locked_address", $arFields, $err_mess.__LINE__);
            if(strlen($strError)<=0 and $id>0){
                $DB->Commit();
                $result['id'] = $id;
                $result['SUCCESS'] = 'Блокировка успешно добавлен';
            }
        }

        return $result;
    }


    public static function RemoveByID($id){
        if($id>0){
            global $DB;
            $DB->Query("DELETE FROM ak_locked_address WHERE ID='$id'");
        }
    }

    public static function getCount(){
        global $DB;
        $sql = "SELECT COUNT(ID) as COUNT FROM ak_locked_address";
        $dbRes = $DB->Query($sql);
        if(!empty($dbRes)){
            $res = $dbRes->fetch();
            return $res['COUNT'];
        }
    }


    public static function getLocked($arFilter=array()){

        global $DB;
        $result = false;

        $curdate = date('Y-m-d');
        $datefrom = date( "Y-m-d", strtotime( $curdate . "-2 day"));
        $dateto = date( "Y-m-d", strtotime( $curdate . "+1 day"));

        $where = 'WHERE ACTIVE=1 ';

        if( !empty($arFilter) ){
            unset($arFilter['user_id']);
            foreach ($arFilter as $key=>$filter){
                $where.=" AND ".strtoupper($key)." = '".$filter."'";
            }
        }


        $sql = "SELECT * FROM ak_locked_address $where AND DATE BETWEEN '$datefrom' AND '$dateto'";
        $dbRes = $DB->Query($sql);

        if(!empty($dbRes)){
            while( $row = $dbRes->fetch() ){
                $result['data'][] = $row;
                $result['count']++;
            }
            return $result;
        }

    }


    public static function getUserLockedCount($arFilter){

        if(intval($arFilter['user_id'])>0){
            global $DB;

            $curdate = date('Y-m-d');
            $datefrom = date( "Y-m-d", strtotime( $curdate . "-2 day"));
            $dateto = date( "Y-m-d", strtotime( $curdate ));

            $where = 'WHERE 1';

            if( !empty($arFilter) ){
                foreach ($arFilter as $key=>$filter){
                    $where.=" AND ".strtoupper($key)." = '".$filter."'";
                }
            }

            $sql = "SELECT COUNT(ID) as COUNT FROM ak_locked_address $where AND DATE BETWEEN '$datefrom' AND '$dateto'";

            $dbRes = $DB->Query($sql);
            if(!empty($dbRes)){
                $res = $dbRes->fetch();
                return $res;
            }
        }

    }

}