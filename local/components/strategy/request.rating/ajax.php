<?php 
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("NEED_AUTH", true); 

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if( $USER->IsAuthorized() and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){

	if( $_REQUEST['request_id'] >0 and $_REQUEST['rate'] >0 ){

        global $DB, $USER;
        
        $authUserID = $USER->getID();
        $request_id = intval($_REQUEST['request_id']);

        $res = $DB->query("SELECT ID FROM ak_requset_rating WHERE `USER_ID`='".$authUserID."' and `REQUEST_ID`='".$request_id."' ", $err_mess.__LINE__);
        $rated = $res->SelectedRowsCount();
        
        if($rated < 1){

            $rate = intval($_REQUEST['rate']);
            
            $DB->PrepareFields("ak_requset_rating");
        
            $arFields = array(
                "REQUEST_ID"=>"'".$request_id."'",
                "USER_ID"=>"'".$authUserID."'",
                "RATE"=>"'".$rate."'"
            );

            $DB->StartTransaction();        
            $ID = $DB->insert("ak_requset_rating", $arFields);
        
            if(strlen($strError)<=0){
                $result['status'] = 'success';
                $result['message'] = 'Ваша оценка принята';
                $DB->Commit();

            }else{
                $result['status'] = 'error';
                $result['message'] = $strError;
            }

        }else{
            $result['status'] = 'rated';
            $result['message'] = "Вы уже оценили";
        }
       
    }else{
        $result['message'] = 'Error';
        $result['status'] = 'error'; 
    } 
    
    echo json_encode($result);
}