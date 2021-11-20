<?php 
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("NEED_AUTH", true); 

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if( strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){

	if($_REQUEST['realtyID']>0 and intval($_REQUEST['rate'])>0){

        global $DB, $USER;
        $userID = $USER->getID();
        $realtyID = intval($_REQUEST['realtyID']);

        $res = $DB->query("SELECT ID FROM st_realty_rating WHERE `USER_ID`='$userID' and `REALTY_ID`='$realtyID'");
        $rated = $res->SelectedRowsCount();        
        
        if($rated < 1){

            $rate = intval($_REQUEST['rate']);
            $DB->PrepareFields("st_realty_rating");
            $arFields = array(
                "USER_ID"=>"'".$userID."'",
                "REALTY_ID"=>"'".$realtyID."'",
                "RATE"=>"'".$rate."'"
            );		

            $DB->StartTransaction();        
            $ID = $DB->insert("st_realty_rating", $arFields, $err_mess.__LINE__);
            
            $ID = intval($ID);
            if(strlen($strError)<=0){
                $result['status'] = 'success'; 
                $result['message'] = 'Ваша оценка принята';
                $DB->Commit();       
            }else{
                $DB->Rollback();
                $result['status'] = 'error'; 
                $result['message'] = $err_mess;
            }

        }else{
            $result['status'] = 'rated';
            $result['message'] = "Вы уже оценили";
        }
       
    }else{
        $result['status'] = 'error'; 
    } 
    
    echo json_encode($result);
}