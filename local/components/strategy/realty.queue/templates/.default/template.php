<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);

CJSCore::init("sidepanel");

if(!empty($_GET['m'])){
    $m = $_GET['m'];
}else{
    $m = date('m');
}

$days  = cal_days_in_month(CAL_GREGORIAN, $m, date('Y') );


if( !empty($_GET['month']) ){ $date = $_GET['month']; } else{ $date=date('Y-m'); }
?>
<div class="row">
    <div class="col-md-3 col-lg-2">
        <ul class="nav nav-tabs" id="leadsTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="newleads-tab" data-toggle="tab" href="#newleads" role="tab" aria-controls="newleads" aria-selected="true">Новые</a>
            </li>
        </ul>

        <div class="tab-content" id="leadsTabContent">
            <div class="tab-pane fade show active" id="newleads" role="tabpanel" aria-labelledby="newleads-tab">
            <button class="btn btn-info add-lead" type="button" name="addlead">Добавить лид</button>
                <p class="leads-title">Нове заявки</p>
                <? if(!empty($arResult['NEW_LEADS'])):?>
                    <form action="" method="post" id="form-leads">
                        <div class="lead-list">
                            <? foreach ($arResult['NEW_LEADS'] as $arLead):?>
                                <label class="lead-item">
                                    <div class="check-item">
                                        <input  type="radio" name="lead" value="<?=$arLead['ID']?>">
                                    </div>
                                    <div class="lead-wrapp">
                                        <a target="_blank" class="lead-link" href="/crm/lead/details/<?=$arLead['ID']?>/"><?=$arLead['TITLE']?></a>
                                        <p class="lead-date"><?=$arLead['DATE_CREATE']?></p>
                                    </div>
                                </label>
                            <? endforeach;?>
                        </div>
                        <input type="hidden" name="region" value="<?=intval($_GET['region'])?>">

                        <div class="form-group departments">
                            Выберите что бы распределить
                              <? foreach ($arResult['DEPARTMENTS'] as $department): ?>
                                <label><input type="radio" name="department" value="<?=$department['XML_ID']?>">  <?=$department['NAME']?></label>
                            <? endforeach; ?>
                        </div>

						<? if(!empty($arResult['isAdmins'])):?>                        
                        <input type="submit" class="btn btn-success" name="submit" value="Распределить">
						<? endif ?>
                    </form>                   

                <?else:?>
                    <div class="alert alert-info">
                        Нет заявок для распределения
                    </div>
                <?endif;?>

                <hr>
            </div>

        </div>

    </div>

    <div class="col-md-9 col-lg-10">

        <div class="row">
            <div class="col-md-6">
                <h5>Очередь недвижимости продажа <?if($_GET['region']==1):?>Ереван<?else:?>Регионы<?endif;?> на <? echo FormatDate('f Y г.', strtotime($date));?></h5>
            </div>
            <div class="col-md-2 prev-month">
                <a href="?month=<?=date('Y-m', strtotime($date.' -1 month'))?>&region=<?=$_GET['region']?>&m=<?=date('m', strtotime($date.' -1 month'))?>">< Прошлый месяц</a>
            </div>
            <div class="col-md-2 prev-month">
                <a href="?region=<?=$_GET['region']?>">Текущий месяц</a>
            </div>
            <div class="col-md-2 prev-month">
                <? if( strtotime($date) < strtotime(date('Y-m')) ):?>
                    <a href="?month=<?=date('Y-m', strtotime($date.' +1 month'))?>&region=<?=$_GET['region']?>">< Следующий месяц</a>
                <? endif ?>
            </div>
        </div>

        <table class="table-queue table table-bordered">

            <tr>
                <td class="coldays">Дни</td>
               <? foreach($arResult['DEPARTMENTS'] as $key=> $department): ?>
               <td class="colname" style="background:<?=$department['UF_COLOR']?>">
                   <div><?=$department['NAME']?></div>
               </td>
               <? endforeach;?>
            </tr>

            <? for($i=1; $i<=$days;$i++):?>
            <tr>
                <td><?echo $i?></td>
                <? foreach($arResult['DEPARTMENTS'] as $department): ?>
                    <td>&nbsp;
                        <? foreach ($arResult['QUEUE'][$i] as $dkey=> $arItem):?>
                        <? if($department['XML_ID']==$dkey):?>
                            <?  foreach ($arItem as $item):  ?>
                                <?$cancel = ''; if(!empty($item['CANCEL'])) $cancel = 'lead-cancel' ?>
                                    <div class="queue-item <?=$cancel?>" data-queue="realty" data-id="<?=$item['LEAD_ID']?>">
                                        <? if($item['LEAD_ID']<1): ?>
                                            <?=$item['LEAD_NAME']?>
                                        <? else:?>
                                           <p>
                                               <a target="_blank" class="lead-link" href="/crm/lead/details/<?=$item['LEAD_ID']?>/"><?=$item['LEAD_NAME']?></a>
                                           </p>
                                        <div class="leadinfo hide">
                                            <p class="info-date"><b>Дата распределения։</b> <span><?= date('d.m.Y', strtotime($item['DATE_CREATED']))?></span></p>
                                            <div class="lead-info"></div>
                                            
                                            <?if( $USER->getID()==QUEUE_ADMIN ):?>
                                            <button data-id="<?=$item['ID']?>" data-lead="<?=$item['LEAD_NAME']?>" data-queuetype="1" class="btn btn-danger btn-toJunk">Удалить очередь</button>
                                            <? endif?>

                                            <? if( $item['USER_ID']==$USER->getID() ): ?>
                                            <button data-id="<?=$item['ID']?>" data-lead="<?=$item['LEAD_NAME']?>" class="btn btn-warning btn-cancel">Отменить очередь</button>
                                            <? endif?>

                                        </div>
                                      <? endif?>
                                    </div>
                            <? endforeach;?>
                            <? endif?>
                        <? endforeach;?>
                    </td>
                <? endforeach;?>
            </tr>
            <? endfor?>

        </table>

<br><br><br><br>
    </div>
</div>

<script>

    $(document).ready(function () {

        $('.queue-item').hover(function() {
                let lead = $(this);
                let leadID = lead.data('id');
                let infobox = $(lead).find('.leadinfo');
                infobox.removeClass('hide');
                        $.ajax({
                            url:'/local/ajax/getlead.php',
                            dataType:'html',
                            method:'get',
                            data:'id='+leadID+'&queue='+lead.data('queue'),
                            success:function (data) {
                                infobox.find('.lead-info').html(data);
                            }
                        });
            },
            function() {
                $(this).find('.leadinfo').addClass('hide');
            }
        );


        <?if( $USER->getID()==QUEUE_ADMIN):?>

        $('.leadinfo .btn-toJunk').click(function () {
            
            let btn = $(this);
            let conf = confirm('Вы уверены что хотите удалить очеред '+btn.attr('data-lead'));            
            
            if(conf==true){                
                $.ajax({
                    url:'/local/ajax/realty_queue_remove.php',
                    // dataType:'JSON',
                    data:{id:btn.attr('data-id')}
                }).done(function (data) {
                    if(data){
                        location.reload();
                    }
                });

            }
        });        
        <?endif?>        

        $('.leadinfo .btn-cancel').click(function () {
            let btn = $(this);
            let conf = confirm('Вы уверены что хотите отменить очеред '+btn.attr('data-lead') );
            $.ajax({
                url:'/local/ajax/realty_queue_cancel.php',
                // dataType:'JSON',
                data:{id:btn.attr('data-id')}
            }).done(function (data) {
                if(data){
                    location.reload();
                }
            });
        });


        $('.lead-link').click(function () {
            let leadlink = $(this).attr('href');
            BX.SidePanel.Instance.open(leadlink, {});
            return false;
        });


        $('.add-lead').click(function(){
            BX.SidePanel.Instance.open('/crm/lead/details/0/', {});
            return false;          
        });

});
</script>

