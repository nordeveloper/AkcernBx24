<?php 
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("NEED_AUTH", true); 

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if( $USER->IsAuthorized() and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){

	if( $_REQUEST['contact_id'] >0 and $_REQUEST['rate'] >0 ){

//        if (!CModule::IncludeModule('crm'))
//        {
//            ShowError(GetMessage('CRM_MODULE_NOT_INSTALLED'));
//            return;
//        }

        global $DB, $USER;
        
        $authUserID = $USER->getID();
        $contact_id = intval($_REQUEST['contact_id']);

        $res = $DB->query("SELECT ID FROM ak_rating_users WHERE `USER_ID`='".$authUserID."' and `CONTACT_ID`='".$contact_id."' ", $err_mess.__LINE__);
        $rated = $res->SelectedRowsCount();
        
        if($rated < 1){

            $rate = intval($_REQUEST['rate']);
            
            $DB->PrepareFields("ak_rating_users");
        
            $arFields = array(
                "CONTACT_ID"=>"'".$contact_id."'",
                "USER_ID"=>"'".$authUserID."'",
                "RATE"=>"'".$rate."'"
            );

            $DB->StartTransaction();        
            $ID = $DB->insert("ak_rating_users", $arFields);
        
            if(strlen($strError)<=0){
                $result['status'] = 'success';
                $result['message'] = 'Ваша оценка принята';
                $DB->Commit();

//                $oCont = new CCrmContact();
//                $oCont->update($contact_id, array('UF_CRM_1599808888'=>$rate));

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