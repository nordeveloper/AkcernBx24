<?php


class City
{
    public static function getList($arFilter=array(), $orderby = false, $limitOffset=false){
        global $DB;

        $where = 'WHERE 1 ';
        $limit = '';

        if(empty($orderby)){
            $orderby = 'id asc';
        }

        if(!empty($limitOffset)){
            $limit = 'limit '.$limitOffset;
        }

        if( intval($arFilter['id'])>1 ){
            $id = $arFilter['id'];
            $where.= " and id='$id'";
        }


        if( intval($arFilter['region_id'])>0 ){
            $region_id = $arFilter['region_id'];
            $where.= " and region_id='$region_id'";
        }

        if( !empty($arFilter['name_am']) ){
            $q = trim($arFilter['name_am']);
            $where.= " AND name_am like '%".$q."%'";
        }

        if( !empty($arFilter['name_ru']) ){
            $q = trim($arFilter['name_ru']);
            $where.= " AND name_ru like '%".$q."%'";
        }

        if( !empty($arFilter['name_en']) ){
            $q = trim($arFilter['name_en']);
            $where.= " AND name_en like '%".$q."%'";
        }


        $sql = "SELECT * FROM ak_cities ".$where." ORDER BY $orderby $limit";
        $dbres = $DB->query($sql);

        while( $row = $dbres->fetch() ){
            $arResult['ITEMS'][] = $row;
        }

        $sqlC = "SELECT COUNT(id) as COUNT FROM ak_cities ".$where." ";
        $resC =  $DB->Query($sqlC);
        $rowC = $resC->fetch();

        $arResult['COUNT'] = $rowC['COUNT'];

        return $arResult;
    }


    public static function getById($id){
        global $DB;

        if(intval($id)>0){
            $sql = "SELECT * FROM ak_cities WHERE id='$id' ORDER BY id asc";
            $dbres = $DB->query($sql);

            if( $row = $dbres->fetch() ){
                return $row;
            }
        }

    }


    public static function Add($arData){
        global $DB;

        if( intval($arData['region_id'])>0 and !empty($arData['name_ru']) and !empty($arData['name_am']) and !empty($arData['name_en']) ){

            $sort = intval($arData['sort']);
            $name_ru= trim($arData['name_ru']);
            $name_en = trim($arData['name_en']);
            $name_am = trim($arData['name_am']);

            $arFields = array(
                "region_id"=>"'".$arData['region_id']."'",
                "sort" => "'".$sort."'",
                "name_ru"      => "'".$DB->ForSql(trim($name_ru))."'",
                "name_en"   => "'".$DB->ForSql(trim($name_en))."'",
                "name_am"   => "'".$DB->ForSql(trim($name_am))."'",
            );

            $DB->StartTransaction();
            $ID = $DB->Insert('ak_cities', $arFields, $err_mess.__LINE__);

            $ID = intval($ID);
            if (strlen($strError)<=0)
            {
                $DB->Commit();
                $result['status']='success';
                $result['message'] = 'Регин успешно добавлен';
                $result['id'] = $ID;
            }else{
                $DB->Rollback();
                $result['status']='error';
                $result['message'] = 'Ошибка добавления города';
            }
        }else{
            $result['status'] = 'error';
            $result['message'] = 'Назание Армянском, Русском обязательно для заполнения';
        }

        return $result;
    }

    public static function Update($id, $arData){

        global $DB;

        if(intval($id)>0 and  !empty( trim($arData['name_am']) ) and  !empty(trim($arData['name_ru'])) and !empty( trim($arData['name_en']) )){

            $sort = intval($arData['sort']);
            $name_am = trim($arData['name_am']);
            $name_ru= trim($arData['name_ru']);
            $name_en = trim($arData['name_en']); 

            $arFields = array(
                "sort" => "'".$sort."'",
                "name_am"   => "'".$DB->ForSql(trim($name_am))."'",
                "name_ru"      => "'".$DB->ForSql(trim($name_ru))."'",
                "name_en"   => "'".$DB->ForSql(trim($name_en))."'",
                "region_id"   => "'".$region_id."'",
            );

            if(intval($arData['region_id'])>0){
                $arFields['region_id'] = "'".$arData['region_id']."'";
            }


            $DB->StartTransaction();
            $DB->Update('ak_cities', $arFields, "WHERE id='".$id."'", $err_mess.__LINE__);

            if (strlen($strError)<=0)
            {
                $DB->Commit();
                $result['status']='success';
                $result['message'] = 'Изменение успешно сохранены';
            }else{
                $DB->Rollback();
                $result['status']='error';
                $result['message'] = 'Ошибка добавления Региона';
            }

        }else{
            $result['status'] = 'error';
            $result['message'] = 'Все поля побязательны для заполнения';
        }

        return $result;
    }

    public static function Remove($id){
        global $DB;
        if(intval($id)>0){
            $sql = "DELETE FROM ak_cities WHERE id='$id'";
            $res  = $DB->Query($sql);
            return $res;
        }
    }


    public static function count(){
        global $DB;
        $sql = "SELECT COUNT(id) as COUNT FROM ak_cities";
        $res =  $DB->Query($sql);
        $resC = $res->fetch();
        return $resC['COUNT'];
    }

}