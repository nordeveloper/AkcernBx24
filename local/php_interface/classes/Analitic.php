<?php

class Analitic
{

    public static function getRealtyAvG($arFilter = array()){

        global $DB;
        $where = '';
        $arData = false;

        $validFields = array('DEAL_TYPE', 'REALTY_TYPE', 'ZONE', 'ROOMS', 'FLOOR', 'TOTAL_AREA', 'BUILDING_TYPE', 'STATUS');

        if(!empty($arFilter)){
            foreach ($arFilter as $key=>$filter){

                if(!empty($filter) and in_array($key,$validFields) ){

                    if(is_array($filter)){
                        //dump($filter);
                        $where.=" and $key IN(".implode(',', $filter).")";
                    }else{
                        $where.=" and $key = '".$filter."'";
                    }

                }
            }

            if($arFilter['YEAR']>0){
                $year = intval($arFilter['YEAR']);
            }else{
                $year = date('Y');
            }
            $where.= " and YEAR(DATE_CREATED)='$year'";
        }

        if($arFilter['FilterType']=='count'){
          $sql = "SELECT MONTH(DATE_CREATED) AS MONTH, COUNT(ID) as AVG FROM realty_analitic WHERE 1 $where GROUP BY MONTH";
        }else{
          $sql = "SELECT MONTH(DATE_CREATED) AS MONTH, AVG(PRICE) as AVG FROM realty_analitic WHERE 1 $where GROUP BY MONTH";
        }

        $dbRes = $DB->Query($sql);

        while($row = $dbRes->fetch()){
            $arData['MONTH'][] = $row['MONTH'];
            $arData['AVG'][]  = round($row['AVG']);
        }
        return $arData;
    }


    public static function RealtyCount($arFilter){

        global $DB;
        $where = 'WHERE 1';
        $arData = false;

        $validFields = array('DEAL_TYPE', 'REALTY_TYPE', 'ZONE', 'ROOMS', 'FLOOR', 'TOTAL_AREA', 'BUILDING_TYPE', 'STATUS');

        if(!empty($arFilter)){
            foreach ($arFilter as $key=>$filter){
                if(!empty($filter) and in_array($key,$validFields) )
                    $where.=" and $key = '".$filter."'";
            }

            if($arFilter['YEAR']>0){
                $year = intval($arFilter['YEAR']);
            }else{
                $year = date('Y');
            }
            $where.= " and YEAR(DATE_CREATED)='$year'";
        }

        $sql = "SELECT COUNT(*) as COUNT FROM realty_analitic $where";

        $dbRes  = $DB->Query($sql);
        if($row = $dbRes->fetch()){
            return $row;
        }

    }


    public static function getRequestAvg($arFilter = array()){

        global $DB;
        $where = '';
        $arData = false;

        $validFields = array('DEAL_TYPE', 'REALTY_TYPE', 'ZONE', 'ROOMS', 'FLOOR');

        if(!empty($arFilter)){
            foreach ($arFilter as $key=>$filter){
                if(!empty($filter) and in_array($key,$validFields) )
                    $where.=" and $key = '".$filter."'";
            }

            if($arFilter['YEAR']>0){
                $year = intval($arFilter['YEAR']);
            }else{
                $year = date('Y');
            }
            $where.= " and YEAR(DATE_CREATED)='$year'";
        }

        if($arFilter['FilterType']=='count'){
            $sql = "SELECT MONTH(DATE_CREATED) AS MONTH, COUNT(ID) as AVG FROM requests_analitic WHERE 1 $where GROUP BY MONTH";
        }else{
            $sql = "SELECT MONTH(DATE_CREATED) AS MONTH, AVG(PRICE) as AVG FROM requests_analitic WHERE 1 $where GROUP BY MONTH";
        }

        $dbRes = $DB->Query($sql);

        while($row = $dbRes->fetch()){
            $arData['MONTH'][] = $row['MONTH'];
            $arData['AVG'][] = round($row['AVG']) ;
        }

        return $arData;
    }



    public static function RequestsCount($arFilter=array()){

        global $DB;
        $where = 'WHERE 1';

        $validFields = array('DEAL_TYPE', 'REALTY_TYPE', 'ZONE', 'ROOMS');

        if(!empty($arFilter)){
            foreach ($arFilter as $key=>$filter){
                if(!empty($filter) and in_array($key,$validFields) )
                    $where.=" and $key = '".$filter."'";
            }

            if($arFilter['YEAR']>0){
                $year = intval($arFilter['YEAR']);
            }else{
                $year = date('Y');
            }
            $where.= " and YEAR(DATE_CREATED)='$year'";
        }

        $sql = "SELECT COUNT(*) as COUNT FROM requests_analitic $where";
        $dbRes  = $DB->Query($sql);

        if($row = $dbRes->fetch()){
            return $row;
        }
    }



    public static function AddRealty( $data=array() ){

        if($data['REALTY_ID']>0 and $data['DEAL_TYPE']>0 and $data['PRICE']>0){

            global $DB;

            if(strtotime($data['DATE_CREATED'])>0){
                $date = date('Y.m.d', strtotime($data['DATE_CREATED']));
            }else{
                $date = date('Y.m.d');
            }

            $arFields = array(
                "USER_ID"=>"'".intval($data['USER_ID'])."'",
                "REALTY_ID"=>"'".intval($data['REALTY_ID'])."'",
                "DATE_CREATED"=>"'".$date."'",
                "PRICE"=>"'".floatval($data['PRICE'])."'",
                "DEAL_TYPE"=>"'".intval($data['DEAL_TYPE'])."'",
                "REALTY_TYPE"=>"'".intval($data['REALTY_TYPE'])."'",
                "REGION"=>"'".intval($data['REGION'])."'",
                "CITY"=>"'".intval($data['CITY'])."'",
                "ZONE"=>"'".$DB->ForSql(trim($data['ZONE'])) ."'",
                "ROOMS"=>"'".intval($data['ROOMS'])."'",
                "FLOOR"=>"'".$DB->ForSql($data['FLOOR'])."'",
                "TOTAL_AREA"=>"'".intval($data['TOTAL_AREA'])."'",
                "BUILDING_TYPE"=>intval($data['BUILDING_TYPE']),
                "STATUS"=>"'".intval($data['STATUS'])."'",
            );


            $DB->PrepareFields("realty_analitic");
            $DB->StartTransaction();

            $id = $DB->Insert("realty_analitic", $arFields, $err_mess.__LINE__);
            if(strlen($strError)<=0 and $id>0){
                $DB->Commit();
                return $id;
            }
        }
    }


    function AddRequest($data){

        if($data['REQUEST_ID']>0 and $data['DEAL_TYPE']>0 and $data['PRICE']>0){

            global $DB;

            if(strtotime($data['DATE_CREATED'])>0){
                $date = date('Y.m.d', strtotime($data['DATE_CREATED']));
            }else{
                $date = date('Y.m.d');
            }

            $arFields = array(
                "REQUEST_ID"=>"'".intval($data['REQUEST_ID'])."'",
                "DATE_CREATED"=>"'".$date."'",
                "DEAL_TYPE"=>"'".intval($data['DEAL_TYPE'])."'",
                "REALTY_TYPE"=>"'".intval($data['REALTY_TYPE'])."'",
                "PRICE"=>"'".floatval($data['PRICE'])."'",
                "REGION"=>"'".intval($data['REGION'])."'",
                "CITY"=>"'".intval($data['CITY'])."'",
                "ZONE"=>"'".$DB->ForSql($data['ZONE'])."'",
                "ROOMS"=>"'".$data['ROOMS']."'",
                "FLOOR"=>"'".$DB->ForSql($data['FLOOR'])."'",
                "TOTAL_AREA"=>"'".intval($data['TOTAL_AREA'])."'",
                //"BUILDING_TYPE"=>"'".$DB->ForSql($data['BUILDING_TYPE'])."'",
            );

            $DB->PrepareFields("requests_analitic");
            $DB->StartTransaction();

            $id = $DB->Insert("requests_analitic", $arFields, $err_mess.__LINE__);
            if(strlen($strError)<=0 and $id>0){
                $DB->Commit();
                return $id;
            }
        }
    }

}