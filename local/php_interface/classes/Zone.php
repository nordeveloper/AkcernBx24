<?php

class Zone
{
    public static function getList($arfilter=array(), $order=false, $limitOffset=false){
        global $DB;
        $where = 'WHERE 1';
        $orderby = 'sort asc';
        $limit = '';

        if($arfilter['id']>0){
            $id = trim($arfilter['id']);
            $where.= " AND id='$id'";
        }

        if( !empty($arfilter['zone']) ){
            $zone = trim($arfilter['zone']);
            $where.= " AND code like '$zone%'";
        }

        if($arfilter['region_id']>0){
            $rid = trim($arfilter['region_id']);
            $where.= " AND region_id='$rid'";
        }

        if(!empty($order)){
            $orderby = $order;
        }

        if( !empty($limitOffset) ){
            $limit = 'limit '. $limitOffset;
        }

        $sql = "SELECT * FROM s1_zone $where ORDER BY $orderby $limit";
        $dbres = $DB->query($sql,false, $err_mess.__LINE__);

        $arResult = array();
        while( $row = $dbres->fetch() ){
            $arResult[] = $row;
        }
        return $arResult;
    }



    public static function getByID($id){
        global $DB;
        if(intval($id)>0){
            $sql = "SELECT * FROM s1_zone WHERE id ='$id'  ORDER BY id asc";
            $dbres = $DB->query($sql);
            if($row = $dbres->fetch()){
                return $row;
            }
        }
    }


    public static function Add($arData){

        global $DB;

        if( !empty($arData['code']) ){

            if(empty($arData['sort'])){
                $sort = 100;
            }else{
                $sort = intval($arData['sort']);
            }

            $active = intval($arData['active']);
            $code = trim($arData['code']);
            $region_id = intval($arData['region_id']);
            $name_am = trim($arData['name_am']);
            $name_ru = trim($arData['name_ru']);
            $name_en = trim($arData['name_en']);

            $arFields = array(
                "active" => "'".$active."'",
                "sort" => "'".$sort."'",
                "code"   => "'".$DB->ForSql($code)."'",
                "region_id" => "'".$region_id."'",
                "name_am"   => "'".$DB->ForSql($name_am)."'",
                "name_ru"   => "'".$DB->ForSql($name_ru)."'",
                "name_en"   => "'".$DB->ForSql($name_en)."'",
            );

            $DB->StartTransaction();
            $ID = $DB->Insert('s1_zone', $arFields, $err_mess.__LINE__);

            if (strlen($strError)<=0)
            {
                $DB->Commit();
                $result['status']='success';
                $result['message'] = 'Зона успешно добавлен';
                $result['id'] = $ID;

            }
            else {
                $DB->Rollback();
                $result['status']='error';
                $result['message'] = $strError;
            }

        }else{
            $result['status']='error';
            $result['message'] = 'Отмечаные поля обязательны для заполнения';
        }

        return $result;
    }

    public static function Update($id, $arData){

        global $DB;

        $DB->StartTransaction();

        if(intval($id)>0 and !empty($arData['code']) ){

            if(empty($arData['sort'])){
                $sort = 100;
            }else{
                $sort = intval($arData['sort']);
            }

            $active = intval($arData['active']);
            $code = trim($arData['code']);
            $region_id = intval($arData['region_id']);
            $name_am = $arData['name_am'];
            $name_ru = $arData['name_ru'];
            $name_en = $arData['name_en'];

            $arFields = array(
                "active" => "'".$active."'",
                "sort" => "'".$sort."'",
                "code"   => "'".$DB->ForSql($code)."'",
                "region_id" => "'".$region_id."'",
                "name_am" => "'".$DB->ForSql($name_am)."'",
                "name_ru" => "'".$DB->ForSql($name_ru)."'",
                "name_en" => "'".$DB->ForSql($name_en)."'",
            );

            $DB->Update(TABLE_ZONE, $arFields, "WHERE id='".$id."'", $err_mess.__LINE__);

            if (strlen($strError)<=0)
            {
                $DB->Commit();
                $result['status']='success';
                $result['message'] = 'Изменение успешно сохранены';
                $result['id'] = $id;
                return $result;
            }
            else $DB->Rollback();

        }else{
            $result['status']='success';
            $result['message'] = 'Отмечаные поля обязательны для заполнения';
        }

        return $result;
    }


    public static function Remove($id){
        global $DB;
        if(intval($id)>0){
            $sql = "DELETE FROM s1_zone WHERE id='$id'";
            $res  = $DB->Query($sql);
            return $res;
        }
    }

    public static function Count(){
        global $DB;
        $sql = "SELECT COUNT(id) as COUNT FROM s1_zone";
        $res =  $DB->Query($sql);
        $resC = $res->fetch();
        return $resC['COUNT'];
    }

}