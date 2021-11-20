<?php
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if ( intval($_REQUEST['id']) ){

    $Queue = new RDQueue('st_realty_queue');
    $res = $Queue->Remove($_REQUEST['id']);
    echo $res;    
}
