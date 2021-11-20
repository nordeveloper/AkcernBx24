<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
$visit_count = 0;
if($arResult['CLIENT']['VISITS_COUNT']>0){
    $visit_count =  $arResult['CLIENT']['VISITS_COUNT'];
}
?>

<div class="row">
    <div class="aside col-sm-3">

        <div class="form-group">
            <p><b>Идентификатор</b> <br><?=$arResult['CLIENT']['IDENTIFICATOR']?></p>
        </div>

        <div class="form-group">
            <p><img src="<?= SITE_TEMPLATE_PATH ?>/images/user.png"></p>
            <p class="info-label"><b>Клиент</b></p>
            <p><?=$arResult['CLIENT']['FULL_NAME']?></p>
            <hr>
        </div>


        <div class="form-group">
            <p class="info-label"><b>Риелтор</b></p>
            <p><?=$arResult['CLIENT']['ASSIGNED_BY_NAME'].' '.$arResult['CLIENT']['ASSIGNED_BY_LAST_NAME']?></p>
            <hr>
        </div>

        <div class="form-group">
            <p class="info-label">Количество запросов</p>
            <p class="text-center"><span class="counter"><?=count($arResult['ITEMS'])?></span></p>
            <hr>
        </div>

        <div class="form-group">
            Оценка։ <?=$arResult['CLIENT']['RATING']?>
            <?$APPLICATION->IncludeComponent("strategy:client.rating", "", array("CONTACT_ID"=>$arResult['CLIENT']['ID']), false);?>
        </div>

       <div class="form-group">
            <p class="info-label">Количество посещений</p>
            <p class="text-center"><span class="counter"><?=$visit_count?></span></p>
            <hr>
        </div>
        <hr>
        
        <div class="form-group">
            <label>Дата посещения</label>
            <div class="input-date-box">
                <input type="text" data-contactid="<?=$arResult['CLIENT']['ID']?>" name="VISIT_DATE" value="<?=$arResult['CLIENT']['VISIT_DATE']?>" onclick="BX.calendar({node: this, field: this, bTime: false});">
            </div>
            <br>
            <button class="btn btn-custom">Сохранить дату посещения</button>
        </div>

    </div>

    <div class="right-content col-sm-9">
        <p class="h3 title text-center">Запросы клиента</p>

        <div class="row">
            <div class="col-12">
            <? if(!empty($arResult['ITEMS'])):?>

            <div class="table-responsive">

            <table class="client-requests data-table">
            <? foreach($arResult['ITEMS'] as $arItem): ?>
            <tr>
                <th colspan="2">
                    <p class="request-title">Запрос: <span><?=$arItem['NAME']?></span> <a href="/realty/client-requests/matches/?id=<? echo $arItem['ID']?>">Посмотреть совпадений</a></p>
                </th>
            </tr>

               <?foreach($arItem['PROPERTIES'] as $arProp):?>
                <? if(empty($arProp['VALUE'])) continue;?>
                <tr>
                    <td class="name"><? echo $arProp['NAME']?>:</td>
                    <td>
                        <span>
                            <?
                            if(is_array($arProp['VALUE'])){
                                echo implode(',', $arProp['VALUE']);
                            }else{
                                echo $arProp['VALUE'];
                            }
                            ?>
                        </span>
                    </td>
                </tr>
              <?endforeach ?>

           <? endforeach?>
            </table>
            </div>

            <? endif?>
            </div>
        </div>

    </div>

</div>


<script>
    $(document).ready(function () {
        $('.btn-custom').click(function () {
            let visit_input = $('.input-date-box input');
            $.ajax({
                'url':'/local/ajax/contact.php?',
                'data':{visit_date:visit_input.val(), 'id':visit_input.attr('data-contactid')},
                'method':'post',
                success:function (data) {
                    console.log(data);
                }
            })
        });
    });
</script>
