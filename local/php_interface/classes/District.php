<?php


class District
{
    public static function getList($arFilter=array()){
        global $DB;

        $where = 'WHERE 1 ';

        if( intval($arFilter['id'])>1 ){
            $id = $arFilter['id'];
            $where.= " and id='$id'";
        }

        $sql = "SELECT * FROM st_zones_root ".$where." ORDER BY id asc";
        $dbres = $DB->query($sql);
        $arResult= array();

        while( $row = $dbres->fetch() ){
            $arResult[] = $row;
        }
        return $arResult;

    }


//    public static function getById($id){
//        global $DB;
//
//        if(intval($id)>0){
//            $sql = "SELECT * FROM st_zones_root WHERE id='$id' ORDER BY id asc";
//            $dbres = $DB->query($sql);
//
//            if( $row = $dbres->fetch() ){
//                return $row;
//            }
//        }
//
//    }
//
//
//    public static function Add($arData){
//        global $DB;
//
//        if( !empty($arData['name_ru']) and !empty($arData['name_am']) ){
//            $sort = intval($arData['sort']);
//            $name_ru= trim($arData['name_ru']);
//            $name_en = trim($arData['name_en']);
//            $name_am = trim($arData['name_am']);
//
//            $arFields = array(
//                "sort" => "'".$sort."'",
//                "name_ru"      => "'".$DB->ForSql(trim($name_ru))."'",
//                "name_en"   => "'".$DB->ForSql(trim($name_en))."'",
//                "name_am"   => "'".$DB->ForSql(trim($name_am))."'",
//            );
//
//            $DB->StartTransaction();
//            $ID = $DB->Insert('st_zones_root', $arFields, $err_mess.__LINE__);
//
//            $ID = intval($ID);
//            if (strlen($strError)<=0)
//            {
//                $DB->Commit();
//                $result['status']='success';
//                $result['message'] = 'Регин успешно добавлен';
//                $result['id'] = $ID;
//            }else{
//                $DB->Rollback();
//                $result['status']='error';
//                $result['message'] = 'Ошибка добавления города';
//            }
//        }else{
//            $result['status'] = 'error';
//            $result['message'] = 'Назание Армянском, Русском обязательно для заполнения';
//        }
//
//        return $result;
//    }
//
//    public static function Update($id, $arData){
//
//        global $DB;
//
//        if(intval($id)>0 and  !empty( trim($arData['name_am']) ) and  !empty(trim($arData['name_ru'])) ){
//
//            $sort = intval($arData['sort']);
//            $name_am = trim($arData['name_am']);
//            $name_ru= trim($arData['name_ru']);
//            $name_en = trim($arData['name_en']);
//
//            $arFields = array(
//                "sort" => "'".$sort."'",
//                "name_am"   => "'".$DB->ForSql(trim($name_am))."'",
//                "name_ru"      => "'".$DB->ForSql(trim($name_ru))."'",
//                "name_en"   => "'".$DB->ForSql(trim($name_en))."'"
//            );
//
//            $DB->StartTransaction();
//            $DB->Update('st_zones_root', $arFields, "WHERE id='".$id."'", $err_mess.__LINE__);
//
//            if (strlen($strError)<=0)
//            {
//                $DB->Commit();
//                $result['status']='success';
//                $result['message'] = 'Изменение успешно сохранены';
//            }else{
//                $DB->Rollback();
//                $result['status']='error';
//                $result['message'] = 'Ошибка добавления Региона';
//            }
//
//        }else{
//            $result['status'] = 'error';
//            $result['message'] = 'Назание Армянском, Русском обязательно для заполнения';
//        }
//
//        return $result;
//    }
//
//    public static function Remove($id){
//        global $DB;
//        if(intval($id)>0){
//            $sql = "DELETE FROM st_zones_root WHERE id='$id'";
//            $res  = $DB->Query($sql);
//            return $res;
//        }
//    }
//
//
//    public static function count(){
//        global $DB;
//        $sql = "SELECT COUNT(id) as COUNT FROM st_zones_root";
//        $res =  $DB->Query($sql);
//        $resC = $res->fetch();
//        return $resC['COUNT'];
//    }

}