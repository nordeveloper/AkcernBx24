<?php
class RDQueue
{
    function __construct($tablename)
    {
        $this->tablename = $tablename;

        if($this->tablename =='st_realty_queue'){
            $this->quuetype = 'realty';
        }

        if($this->tablename =='st_requests_queue'){
            $this->quuetype = 'request';
        }
    }


    public function getList($arFilter = array(), $region=false, $type=false){
        global $DB;
        $andwhere = '';

        if($region>0){
            $region = intval($region);
            $andwhere.= " AND REGION='$region' ";
        }

        if(intval($type)>0){
            $andwhere.= " AND TYPE='$type' ";
        }

        if(!empty($arFilter)){
            $curmonth = explode('-', $arFilter);
            $andwhere.= " and MONTH(DATE_CREATED) = $curmonth[1] and YEAR(DATE_CREATED) = $curmonth[0]";
        }else{
            //$andwhere = " and MONTH(DATE_CREATED) = '".date('m')."' and YEAR(DATE_CREATED) = '".date('Y')."'";
        }

        $sql = "SELECT *, DAY(DATE_CREATED) AS DAY FROM $this->tablename WHERE 1 $andwhere";
        $dbRes = $DB->Query($sql, false, $err_mess.__LINE__);

        $arItems = array();
        while( $row = $dbRes->fetch() ){
            $arItems[$row['DAY']][$row['DEPARTMENT']][] = $row;
        }

        return $arItems;
    }


    public function Add( $arData = array() ){

        global $DB;

        $DB->PrepareFields($this->tablename);
        $arFields = array(
            "DATE_CREATED"=>$DB->GetNowFunction(),
            "DEPARTMENT"=>"'".intval($arData['DEPARTMENT'])."'",
            "DEP_XMLID"=>"'".intval($arData['DEP_XMLID'])."'",
            "LEAD_ID"=>"'".intval($arData['LEAD_ID'])."'",
            "LEAD_NAME"=>"'".$DB->ForSql($arData['LEAD_NAME'])."'",
            "USER_ID"=>"'".intval($arData['USER_ID'])."'",
            "QUEUE"=>"'".intval($arData['QUEUE'])."'",
            "CYCLE"=>"'".intval($arData['CYCLE'])."'",
            "REGION"=>"'".intval($arData['REGION'])."'",
            "TYPE"=>"'".intval($arData['TYPE'])."'",
            "NAME"=>"'".$DB->ForSql($arData['NAME'])."'",
            "COMMENT"=>"'".$DB->ForSql($arData['COMMENTS'])."'",
            "PRICE"=>"'".intval($arData['OPPORTUNITY'])."'",
            "LEAD_DATE"=>"'".date("Y-m-d H:i:s", strtotime($arData['DATE_CREATE']))."'",
            "URL"=>"'".$DB->ForSql($arData['URL'])."'",
            "ASSIGNED_FULLNAME"=>"'".$DB->ForSql($arData['ASSIGNED_FULLNAME'])."'",
        );

        $DB->StartTransaction();
        $result = $DB->Insert($this->tablename, $arFields, $err_mess.__LINE__);

        if (strlen($strError)<=0) {
            $DB->Commit();
            return $result;
        }else $DB->Rollback();
    }



    public function QueueCycleCount($data){
        global $DB;
        $andwhere = 'WHERE 1';

        if(intval($data['REGION'])>0){
            $region = $data['REGION'];
            $andwhere.= " AND REGION='$region'";
        }

        if(intval($data['TYPE'])>0){
            $type = $data['TYPE'];
            $andwhere.= " AND TYPE='$type'";
        }

        //$table = $this->tablename.'cycle';
        $table  = $this->tablename;
        $sql = "SELECT COUNT(ID) as cyclecount FROM $table $andwhere ORDER BY ID DESC";
        $res = $DB->Query($sql, false, $err_mess.__LINE__);

        if( $row = $res->fetch() ){
            return floor($row['cyclecount']/6);
        }
    }


    public function getDepCycle($data=array()){

        if( !empty($data) and is_array($data) ){
            global $DB;

            $andwhere = 'WHERE 1';

            if(intval($data['REGION'])>0){
                $region = $data['REGION'];
                $andwhere.=  " AND REGION='$region'";
            }

            if($data['TYPE']>0){
                $type = $data['TYPE'];
                $andwhere.=  " AND TYPE='$type'";
            }

           $sql = "SELECT COUNT(ID) as COUNT FROM $this->tablename $andwhere";

            $res = $DB->Query($sql);
            if($res){
                $row = $res->fetch();
                return $row['COUNT'];
            }
        }    
    }


    public function AddQueueCycle($data){

        global $DB;
        $arFields = array(
            "DATE"=>$DB->GetNowFunction(),
            "CYCLE"=>"'".intval($data['CYCLE'])."'",
        );

        if(intval($data['REGION'])>0){
            $arFields["REGION"]="'".$data['REGION']."'";
        }

        if(intval($data['TYPE'])>0){
            $arFields["TYPE"] = "'".$data['TYPE']."'";
        }

        $table = $this->tablename.'cycle';
        $res = $DB->Insert($table, $arFields, $err_mess.__LINE__);
        return $res;
    }


    public function Cancel($id){

        if(intval($id)>0){
            global $DB;
            $arQueue = $this->getQueuByID($id);

            if( !empty($arQueue) ){
    
                $sql = "UPDATE $this->tablename set CANCEL='#ff0000' WHERE ID='$id'";
                $res = $DB->Query($sql);
                  
                sendMessageToChat($arQueue['LEAD_ID'], QUEUE_ADMIN, 'Խնդրում եմ այս լիդը <a href="'.$arQueue['URL'].'">'.$arQueue['LEAD_NAME'].'</a> հանել հերթից');
                return 'SUCCESS';
            }
        }
    }


    public function Remove($id){

        if( intval($id)>0 ){

            global $DB;

            $arQueue = $this->getQueuByID($id);

            if( !empty($arQueue) ){

                // $arReDep = getDepartamentList( array("ID"=>$arQueue['DEP_XMLID']) );

                // if( $this->quuetype == 'realty' ){
                //     $regionid = $arQueue['REGION'];
                //     $arDep = current($arReDep);
                //     $queueNum =  intval($arDep['UF_REALTYCYCLE_'.$regionid]) - 1;
                //     $arDFields = Array("UF_REALTYCYCLE_".$regionid=>$queueNum);                   
                // }

                // if($this->quuetype == 'request' ){
                //     $type = $arQueue['TYPE'];
                //     $arDep = current($arReDep);
                //     $queueNum = intval($arDep['UF_REQUESTSCYCLE_'.$type]) - 1;
                //     $arDFields = Array("UF_REQUESTSCYCLE_".$type=>$queueNum);
                // }

                file_put_contents($_SERVER['DOCUMENT_ROOT']. '/local/logs/QueueRemove.log', print_r($arQueue,true), FILE_APPEND);

                $resDel = $DB->Query("DELETE FROM $this->tablename WHERE ID='$id'"); 

                // if( intval( $resDel->AffectedRowsCount() ) >0 ){
                //     $obDep = new CIBlockSection;
                //     $resUp = $obDep->Update($arQueue['DEP_XMLID'], $arDFields);
                //     file_put_contents($_SERVER['DOCUMENT_ROOT'].'/local/logs/QueueRemove.log', print_r($arDFields,true), FILE_APPEND);
                // }

                if( intval( $resDel->AffectedRowsCount() ) >0 ){
                    $crmlead = new CCrmLead();
                    $arLFields['ASSIGNED_BY_ID'] = QUEUE_ADMIN;
                    $arLFields['STATUS_ID'] = 'JUNK';
                    $crmlead->update($arQueue['LEAD_ID'], $arLFields);
                    
                    sendMessageToChat($arQueue['USER_ID'], QUEUE_ADMIN, 'Лид <a href="'.$arQueue['URL'].'">'.$arQueue['LEAD_NAME'].'</a> был удален с очереди');

                    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/local/logs/QueueRemove.log', print_r($arLFields,true), FILE_APPEND);
                    return 'SUCCESS';
                }
            }
        }
    }


    public function getQueuByID($id){

        if(intval($id)>0){

            global $DB;
               
            $SQL = "SELECT * FROM $this->tablename WHERE ID='$id'";
            $res = $DB->Query($SQL);
            if($row = $res->fetch()){
                return $row;
            }
        }
    }


    public static function LeadCancel($lead_id, $type=false){

        if(intval($lead_id)>0){
            global $DB;

            if( !empty($type) ){
                $tablename = 'st_realty_queue';
            }else{
                $tablename = 'st_requests_queue';
            }

            $SQL = "SELECT * FROM $tablename WHERE LEAD_ID='$lead_id'";
            $resQ = $DB->Query($SQL);

            if($arQueue = $resQ->fetch()){

                $sql = "UPDATE $tablename set CANCEL='#ff0000' WHERE LEAD_ID='$lead_id'";
                $resUp = $DB->Query($sql);
    
                if($resUp){
                    sendMessageToChat($arQueue['USER_ID'], QUEUE_ADMIN, 'Խնդրում եմ այս լիդը <a href="'.$arQueue['URL'].'">'.$arQueue['LEAD_NAME'].'</a> հանել հերթից');
                    return 'SUCCESS';
                }
            }
        }
    }

}