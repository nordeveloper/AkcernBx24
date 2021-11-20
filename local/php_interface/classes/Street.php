<?php

class Street
{

    public static function getList($arFilter=array(), $orderby = false, $limitOffset=false){
        global $DB;
        $where = 'WHERE active=1 ';
        $limit = '';

        if(empty($orderby)){
            $orderby = 'name_ru asc';
        }

        if(!empty($limitOffset)){
            $limit = 'limit '.$limitOffset;
        }

        if( intval($arFilter['id'])>1 ){
            $id = $arFilter['id'];
            $where.= " AND id='$id'";
        }

        if( intval($arFilter['region_id'])>0 ){
            $region_id = $arFilter['region_id'];
            $where.= " AND region_id='$region_id'";
        }

        if( !empty($arFilter['zone']) ){

            $zones = trim($arFilter['zone']);
            
            $arZones = explode(',',$zones);

            if(is_array($arZones) and count($arZones)>1){
                $where.= " AND zone_code in ($zones) ";
                $where.= " OR zone_code like '%$zones%' ";
            }else{
                $where.= " AND zone_code like '%$zones%' ";
            }
        }

        if( !empty($arFilter['name_ru']) ){
            $name = $DB->forSQL(trim($arFilter['name_ru']));
            $where.= " AND `name_ru` like '%".$name."%'";
        }

        if( !empty($arFilter['name_am']) ){
            $name = $DB->forSQL(trim($arFilter['name_am']));
            $where.= " AND `name_am` like '%".$name."%'";
        }


        $sql = "SELECT * FROM s1_street ".$where." ORDER BY $orderby $limit";
        $dbres = $DB->query($sql);

        $arResult= array();

        while( $row = $dbres->fetch() ){
            $arResult['ITEMS'][] = $row;
        }

        $sqlC = "SELECT COUNT(id) as COUNT FROM s1_street ".$where." ";
        $resC =  $DB->Query($sqlC);
        $rowC = $resC->fetch();

        $arResult['COUNT'] = $rowC['COUNT'];
        return $arResult;
    }


    public static function count(){
        global $DB;
        $sql = "SELECT COUNT(id) as COUNT FROM s1_street";
        $res =  $DB->Query($sql);
        $resC = $res->fetch();
        return $resC['COUNT'];
    }


    public static function getByID($id){
        if(intval($id)>0){
            global $DB;

            $sql = "SELECT * FROM s1_street WHERE id='$id'";
            $dbres = $DB->query($sql);

            if( $row = $dbres->fetch() ){
                return $row;
            }
        }
    }


    public static function Add($arData){

        global $DB;

        if( intval($arData['region_id'])>0 and !empty($arData['zone_code']) and !empty($arData['name_am']) and !empty($arData['name_ru']) and !empty($arData['name_en'])) {

            if(empty($arData['sort'])){
                $sort = 100;
            }else{
                $sort = $arData['sort'];
            }

            $zone = implode(',',$arData['zone_code']);

            $arFields = array(
                "active"    => "'".$arData['active']."'",
                "region_id"    => "'".$arData['region_id']."'",
                "sort" => "'".$sort."'",

                "name_am"   => "'".$DB->ForSql( trim($arData['name_am']) )."'",
                "name_ru"      => "'".$DB->ForSql( trim($arData['name_ru']) )."'",
                "name_en"   => "'".$DB->ForSql( trim($arData['name_en']) )."'",
                
                // "shortname_am"   => "'".$DB->ForSql( trim($arData['shortname_am']) )."'",
                // "shortname_ru"      => "'".$DB->ForSql( trim($arData['shortname_ru']) )."'",
                // "shortname_en"   => "'".$DB->ForSql( trim($arData['shortname_en']) )."'",

                "zone_code" => "'".trim($zone)."'",
            );

            if(intval($arData['city_id'])>0){
                $arFields["city_id"] = "'".intval($arData['city_id'])."'";
            }

            $DB->StartTransaction();
            $ID = $DB->Insert('s1_street', $arFields, $err_mess.__LINE__);

            $ID = intval($ID);
            if (strlen($strError)<=0)
            {
                $DB->Commit();
                $result['id'] = $ID;
                $result['status'] = 'success';
                $result['message'] = 'Улица успешно добавлен';

            }else{
                $DB->Rollback();
                $result['status']='error';
                $result['message'] = "Ошибка добавления улицы $strError";
            }

        }else{
            $result['status']='error';
            $result['message'] = 'Отмечаные поля обязательны для заполнения';
        }

        return $result;
    }


    public static function Update($id, $arData){
        global $DB;

        if(intval($id)>0 and !empty($arData['zone_code']) and !empty($arData['region_id']) ){

            $zone = implode(',',$arData['zone_code']);

            $arFields = array(
                "active"    => "'".$arData['active']."'",
                "region_id" => "'".intval($arData['region_id'])."'",
                "city_id"   => "'".intval($arData['city_id'])."'",
                "name_ru"   => "'".$DB->ForSql(trim($arData['name_ru']))."'",
                "name_en"   => "'".$DB->ForSql(trim($arData['name_en']))."'",
                "name_am"   => "'".$DB->ForSql(trim($arData['name_am']))."'",
                // "shortname_am"   => "'".$DB->ForSql( trim($arData['shortname_am']) )."'",
                // "shortname_ru"      => "'".$DB->ForSql( trim($arData['shortname_ru']) )."'",
                // "shortname_en"   => "'".$DB->ForSql( trim($arData['shortname_en']) )."'",
                "zone_code" => "'".$DB->ForSql($zone)."'",
            );

  
            if(empty($arData['sort'])){
                $arFields['sort'] = $arData['sort'];
            }

            $DB->StartTransaction();
            $DB->Update('s1_street', $arFields, "WHERE id='".$id."'", $err_mess.__LINE__);

            if (strlen($strError)<=0)
            {
                $DB->Commit();
                $result['status']='success';
                $result['message'] = 'Изменение успешно сохранены';
            }else{
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


    public static function Remove($id){
        global $DB;
        if(intval($id)>0){
            $sql = "DELETE FROM s1_street WHERE id='$id'";
            $res  = $DB->Query($sql);
            return $res;
        }
    }

}