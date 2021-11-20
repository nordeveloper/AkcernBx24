<?php
define("NO_KEEP_STATISTIC", true);
define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if( strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){
    if( intval($_REQUEST['id']) >0){

        if (!CModule::IncludeModule("crm"))
            return;

        $id= intval($_REQUEST['id']);
        $arResult = CCrmLead::GetByID($id, false);
    }  ?>

<p><b>Лид։ </b> <span><?= $arResult['TITLE'] ?></span></p>
<p><b>Дата создания։ </b><?=$arResult['DATE_CREATE']?></p>
    <?php if($_REQUEST['queue']!=='realty'): ?>
        <p><b>Имя։ </b><?=$arResult['NAME']?></p>
        <p><b>Цена։ </b> <?=$arResult['OPPORTUNITY']?></p>
    <?php endif ?>
<p><b>Комментарии։ </b> <span><?=strip_tags($arResult['COMMENTS'])?></span></p>
<p><b>Ответственный։</b> <span><?= $arResult['ASSIGNED_BY_NAME'].' '.$arResult['ASSIGNED_BY_LAST_NAME'] ?></span></p>
<?}?>