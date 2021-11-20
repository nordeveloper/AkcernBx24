<?php
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if ( intval($_REQUEST['lead_id'])>0 ){
    
    $Queue = new RDQueue('st_realty_queue');
    $res = $Queue->Cancel($_REQUEST['id']);    
    echo $res;
}

