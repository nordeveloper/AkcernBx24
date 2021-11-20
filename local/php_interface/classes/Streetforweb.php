<?php

class Streetforweb
{

    public static function getList(){

        global $DB;

        $sql = "SELECT * FROM street_forweb ORDER BY id asc";
        $dbres = $DB->query($sql);

        while( $row = $dbres->fetch() ){
            $arResult[] = $row;
        }

        return $arResult;
    }

    public static function getByID($id){

        if(intval($id)>0){
            global $DB;

            $sql = "SELECT * FROM street_forweb WHERE id='$id'";
            $dbres = $DB->query($sql);

            if( $row = $dbres->fetch() ){
                return $row;
            }
        }
        
    }

}