<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
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

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/js/chosen/chosen.css');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/chosen/chosen.jquery.min.js');

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/bootstrap-select.min.css');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/bootstrap-select.min.js');

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/select2.min.css');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/select2.min.js');
\CJSCore::init("sidepanel");

$form = new Forms();
//CJSCore::Init(['ui']);
?>
<form class="add-edit-box row" name="realty_form" method="POST" action="">
    <? if (!empty($arResult["ERRORS"])):?>
        <div class="info-box">
            <div class=" alert alert-danger">
                <button type="button" class="close">&times;</button>
                <span><? echo $arResult["ERRORS"] ?></span>
            </div>
        </div>
    <?endif;?>
    <? if (strlen($arResult["MESSAGE"]) > 0):?>
        <div class="info-box success-box alert-success">
            <button type="button" class="close">&times;</button>
            <? echo $arResult["MESSAGE"]?>
        </div>
    <?endif?>

    <?=bitrix_sessid_post()?>
    <? if($arResult['ELEMENT']):?>
        <input type="hidden" name="ID" value="<?=$arResult['ELEMENT']['ID']?>">
    <? endif ?>    
    <div class="aside col-lg-2 col-md-3 col-sm-12">

        <p class="h4"><?=getMessage('COMPONENT_TITLE')?></p>
        <hr>

        <? if($arResult['ELEMENT']['ID']){?>
            <div class="form-group identificator">    
            <p><?=getMessage('IDENTIFICATOR')?> <br><? echo $arResult['ELEMENT']['NAME'];?></p>
            <hr>
            </div>
        <? }?>

        <div class="form-group text-center">
            <p>
                <?=getMessage('AGENCY')?><br><?echo $arResult['USER']['DEPARTMENT_NAME'] ?>
            </p>
            <p><img src="<?= SITE_TEMPLATE_PATH ?>/images/user.png"></p>
            <p><?=getMessage('REALTOR')?></p>
            <? if( $arResult['ACCESS']=='GRANTED' ):?>
                <?$APPLICATION->IncludeComponent("bitrix:main.user.selector", "",
                array("INPUT_NAME"=>'CREATED_BY',
                    "LIST"=>array($arResult['ELEMENT']['CREATED_BY']),
                    "BUTTON_SELECT_CAPTION"=>getMessage('CHANGE'),
                    "SELECTOR_OPTIONS" => array('disableLast' => 'Y'),
                ),  false);?>
            <? else: ?>
                <p><?= $arResult['USER']['FULL_NAME'] ?></p>
            <? endif?>
            <hr>
        </div>

        <div class="form-group">
            <p><img src="<?= SITE_TEMPLATE_PATH ?>/images/user.png"></p>

            <?if( empty($arResult['HIDE']) ):?>
                <label>Клиент <span class="required">*</span></label>
                <p><a target="_blank" class="contact-link" href="/crm/contact/details/<?=$arResult["ELEMENT"]["PROPERTIES"]['CLIENTID']['VALUE']?>/"><?=$arResult["ELEMENT"]["PROPERTIES"]['CLIENT_CODE']['VALUE']?></a></p>

                <? if($arResult['CLIENT']['CONTACTS']['PHONE']): ?>
                    <p class="contact-phone"><img src="<?= SITE_TEMPLATE_PATH ?>/images/phone.png"> <?= implode(',',$arResult['CLIENT']['CONTACTS']['PHONE']) ?></p>
                <? endif ?>

                <? if($arResult['CLIENT']['CONTACTS']['EMAIL']['VALUE']):?>
                    <p class="contact-email"><img src="<?= SITE_TEMPLATE_PATH ?>/images/email.png"> <?= implode(',',$arResult['CLIENT']['CONTACTS']['EMAIL']) ?></p>
                <? endif ?>

                <select class="select2-contact form-control" required name="PROPERTY[155]">
                    <? if($arResult["ELEMENT"]["PROPERTIES"]['CLIENTID']['VALUE']):?>
                        <option value="<?=$arResult["ELEMENT"]["PROPERTIES"]['CLIENTID']['VALUE']?>">Изменить</option>
                    <?endif;?>
                </select>

            <? else:?>
                <label><? echo $arResult['PROPERTY_LIST'][155]['NAME']?></label>
                <p><?=getMessage('INFO_DENIED')?></p>
            <? endif ?>
            <hr>
        </div>

        <div class="form-group">
            <? if($arResult['ELEMENT']['ID']>0 and $arResult["ELEMENT"]['PROPERTIES']['CLIENTID']['VALUE']>0):?>
                <?$APPLICATION->IncludeComponent("strategy:request.rating", "", array("REQUEST_ID"=>$arResult['ELEMENT']['ID']), false);?>
                <hr>
            <? endif ?>
        </div>


        <div class="form-group">
            <p><?= getMessage('VISITS_COUNT')?></p>
            <div class="visit-count">
                <?= $arResult['ELEMENT']['VISITS_COUNT']?>
            </div>
        </div>     

    </div>

    <div class="col-lg-10 col-md-9 col-sm-12 right-content">

        <div class="row">
            <? foreach ($arResult['PROPERTY_LIST'] as $key=> $arProp): ?>
                <? if( in_array($key, $arParams['BLOCKLEFT']) ) continue; ?>
                <div class="col-lg-3 col-md-4 form-group <?=strtolower($arProp['CODE'])?>">
                    <? if($arProp['PROPERTY_TYPE']=='L' and $arProp['LIST_TYPE']!='C'):?>

                    <? if(  empty($arResult['ELEMENT']['ID']) and $arProp['CODE']=='REALTY_TYPE' and intval($_GET['type'])>0){
                          $value = $_GET['type'];
                      }else{
                          $value = $arResult['ELEMENT']['PROPERTIES'][$arProp['CODE']]['VALUE_ENUM_ID'];
                      }
                    ?>

                    <?$form->Select($arResult['PROPERTY_LIST'][$key], $value, $arResult['ACCESS']);?>
                    <?endif ?>

                    <? if( ($arProp['PROPERTY_TYPE']=='S' OR $arProp['PROPERTY_TYPE']=='N') and empty($arProp['USER_TYPE']) ):?>
                        <? $form->input($arProp, $arResult['ELEMENT']['PROPERTIES'][$arProp['CODE']]['VALUE'], $arResult['ACCESS'])?>
                    <? endif?>

                    <? if( $arProp['USER_TYPE'] ):?>
                        <? $form->showUserType($arResult['PROPERTY_LIST'][$key], $arResult["ELEMENT"]["PROPERTIES"][$arProp['CODE']], $arResult['ACCESS'])?>
                    <? endif?>

                    <? if($arProp['PROPERTY_TYPE']=='L' and $arProp['LIST_TYPE']=='C' ):?>
                    <? $form->chechbox($arProp, $arResult['ELEMENT']['PROPERTIES'][$arProp['CODE']]['VALUE_ENUM_ID'], $arResult['ACCESS'])?>
                    <? endif ?>
                </div>
            <? endforeach;?>
        </div>

        <hr>
        <div class="row">
            <? if($arResult['ACCESS']!='VIEW'):?>
            <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-success" name="btn_submit" value="Y"><i class="glyphicon glyphicon-floppy-disk"></i> <?=getMessage('BTN_SUBMIT')?></button>
                <button  type="submit" class="btn btn-info" name="btn_apply" value="Y"><i class="glyphicon glyphicon-floppy-saved"></i> <?=getMessage('BTN_APPLY')?></button>
            </div>
            <? endif ?>
        </div>

    </div>
</form>


<script>
    $(document).ready(function() {

        // $(".chosen-select").chosen();

        $(".chosen-select").selectpicker({
            noneSelectedText : 'не выбрана',
        });


        $('.region-select').change(function (e) {
            // e.stopPropagation();
            let region = $(this).val();
            $.ajax({
                url:'/local/ajax/getcities.php',
                dataType:'html',
                data:'region='+region,
                success:function (data) {
                    $("select.city-select").html(data);
                    $("select.city-select").selectpicker('refresh');
                }
            });
        });


        $('.zone-select').change(function (e) {
            e.stopPropagation();
            let zone = $(this).val();
            $.ajax({
                url:'/local/ajax/getstreet.php',
                dataType:'html',
                data:'zone='+zone,
                success:function (data) {

                    let streetselect = $("select.street-select");

                    if( streetselect ){
                        streetselect.html(data);
                        streetselect.selectpicker('refresh');
                    }
                }
            });
        });


        $('.select2-contact').select2({
            minimumInputLength: 2,
            ajax: {
                url: '/local/ajax/getContactByIdent.php',
                dataType: 'json',
                data: function (params) {
                    var query = {q: params.term }
                    return query;
                },
                processResults: function(response) {
                    return {
                        results: response
                    };
                }
            }
        });

        $('.select-realty_type').change(function () {
            let val = $(this).val();
            let valID = '';
            <? if(intval($_GET['ID'])>0): ?>
            valID = '&ID=<?=$_GET['ID']?>';
            <? endif ?>
            location.href='/realty/client-requests/edit/?type='+val+valID;
        });

        $('.info-box .close').click(function(){
            $(this).parent().addClass('hide');
        });

    });
</script>



