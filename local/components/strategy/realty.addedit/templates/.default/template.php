<?php
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
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/js/chosen/chosen.css');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/chosen/chosen.jquery.min.js');

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/select2.min.css');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/select2.min.js');

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/bootstrap-select.min.css');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/bootstrap-select.min.js');

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery-ui.min.js');

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.MultiFile.min.js');

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.mask.min.js');


\CJSCore::init("sidepanel");

$form = new Forms();

$skipeProps = array_merge($arParams['BLOCKLEFT'],$arParams['BLOCK_TOP']);
?>
<div class="row add-edit-box">

    <? if (!empty($arResult["ERRORS"])):?>
        <div class="info-box alert-danger">
            <button type="button" class="close">&times;</button>
            <p><? echo $arResult["ERRORS"] ?></p>            
        </div>
    <?endif;?>

    <form class="col-lg-9 col-md-9 col-xs-12 main-form" name="realty_form" id="realty-form" method="POST" action="" enctype="multipart/form-data">
        <?= bitrix_sessid_post() ?>
        <? if($arResult['ELEMENT']):?>
        <input type="hidden" id="elID" name="ID" value="<?=$arResult['ELEMENT']['ID']?>">
        <input type="hidden" name="ident" value="<?=$arResult['ELEMENT']['NAME']?>">
        <? endif ?>

        <div class="row">

    <div class="aside col-lg-3 col-md-3 col-xs-12">
        
        <div class="info-image">
            <img src="<?= SITE_TEMPLATE_PATH ?>/images/commercial-buldings-1.png">
            <hr>
        </div>

        <? if( !empty($arResult['ELEMENT']['ID']) ):?>
            <div class="form-group identificator">                
                <? echo $arResult['ELEMENT']['NAME'];?>
            </div>
            <hr>
            <p>Дата создания։ <?=$arResult['ELEMENT']['DATE_CREATE']?></p>
            <p>Дата обновления։ <?=$arResult['ELEMENT']['TIMESTAMP_X']?></p>
        <? endif ?>
       

        <? if($arResult['ELEMENT']['ID']>0):?>
            <?$APPLICATION->IncludeComponent("strategy:realty.rating", "", array("REALTY_ID"=>$arResult['ELEMENT']['ID']), false);?>
            <hr>
        <? endif ?>

        <? if($arResult['ELEMENT']['ID']>0):?>
        <div class="form-group visits-box">
            <p><?=getMessage('VISITS')?></p>
            <p><span class="visit-count"><?=$arResult['ELEMENT']['VISITS_COUNT']?></span></p>
        </div>
        <hr>
        <? endif?>

        <div class="form-group text-center">
            <p>
                <?=getMessage('AGENCY')?><br><?echo $arResult['USER']['DEPARTMENT_NAME'] ?>
            </p>
            <p><img src="<?= SITE_TEMPLATE_PATH ?>/images/user.png"></p>
            <p><b><?=getMessage('REALTOR')?></b></p>

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
                <label>Контакт <span class="required">*</span></label>
                <? if(empty($arResult["ELEMENT"]['ID'])):?> Выбрать со списка <input type="radio" name="sk" class="show-cont" value="1"><?endif?><br>
                <? if(empty($arResult["ELEMENT"]['ID'])):?> Выбрать с Лида <input type="radio" name="sk" class="show-cont" value="2"><?endif?>

                <p><a target="_blank" class="contact-link" href="/crm/contact/details/<?=$arResult["ELEMENT"]["PROPERTIES"]['OWNERID']['VALUE']?>/"><?=$arResult['OWNER']['CONTACTS']['IDENTIFICATOR']?></a></p>
                <? if($arResult['OWNER']['CONTACTS']['CONTACT']['PHONE']): ?>
                    <p class="contact-phone"><img src="<?= SITE_TEMPLATE_PATH ?>/images/phone.png"> <?= $arResult['OWNER']['CONTACTS']['CONTACT']['PHONE'] ?></p>
                <? endif ?>

                <? if($arResult['OWNER']['CONTACTS']['CONTACT']['EMAIL']):?>
                    <p class="contact-email"><img src="<?= SITE_TEMPLATE_PATH ?>/images/email.png"> <?= $arResult['OWNER']['CONTACTS']['CONTACT']['EMAIL'] ?></p>
                <? endif ?>

                <div class="contactName-wrapp" id="owner">
                    <select class="select2-contact form-control" required name="PROPERTY[88]">
                        <? if($arResult["ELEMENT"]["PROPERTIES"]['OWNERID']['VALUE']):?>
                            <option value="<?=$arResult["ELEMENT"]["PROPERTIES"]['OWNERID']['VALUE']?>">Изменить</option>
                        <?endif;?>
                    </select>
                </div>

            <? else:?>
                <label><? echo $arResult['PROPERTY_LIST'][88]['NAME']?></label>
                <p><?=getMessage('INFO_DENIED')?></p>
            <? endif ?>
            <hr>

            <? if( empty($arResult["ELEMENT"]['ID']) ): ?>
                <div id="contact-list" class="contactName-wrapp hide">
                    <select name="PROPERTY[305]" class="select2-contactName form-control"></select>
                </div>

                <div id="lead-contact" class="contactWrapp hide">
                    <select name="PROPERTY[305]" class="select2-leadContact form-control"></select>
                </div>
            <?endif?>

        </div>

        
        <div class="form-group">
            <? $form->showUserType($arResult['PROPERTY_LIST'][143], $arResult["ELEMENT"]["PROPERTIES"]['RENTDATEFROM'], $arResult['ACCESS']); ?>
        </div>

        <div class="form-group">
            <? $form->showUserType($arResult['PROPERTY_LIST'][144], $arResult["ELEMENT"]["PROPERTIES"]['RENTDATETO'], $arResult['ACCESS']); ?>
        </div>

        <div class="form-group">
            <? $form->input($arResult['PROPERTY_LIST'][244], $arResult['ELEMENT']['PROPERTIES']['CERTNUM']['VALUE'], $arResult['ACCESS']);?>
        </div>

       <? if($arResult['ACCESS'] =='GRANTED' or $arResult['RULE']=='ADDPHOTO'): ?>
            <div class="form-group file-box">
                <?  if( !empty($arResult['ELEMENT']['PROPERTIES']['CERTDOC']['VALUE']) ):?>                
                    <? foreach ($arResult['ELEMENT']['PROPERTIES']['CERTDOC']['VALUE'] as $fk=> $file): ?>
                    <div class="file-item">
                        <a target="_blank" href="<? echo CFile::GetPath($file) ?>"><i class="glyphicon glyphicon-file"></i> Файл <?=($fk+1)?></a>
                        <button type="button" data-elID="<?= $arResult['ELEMENT']['ID'] ?>" data-id="<?= $file ?>" data-propvalID='<?=$arResult['ELEMENT']['PROPERTIES']['CERTDOC']['PROPERTY_VALUE_ID'][$fk]?>' class="btn btn-delete"><i class="glyphicon glyphicon-remove"></i></button>
                        <br>
                    </div>
                    <?endforeach;?>
                <? endif?>
                <? $form->inputFile($arResult['PROPERTY_LIST'][245], true);?>
            </div>
        <? endif ?>

        <? if(  !empty($arResult['ELEMENT']['ID']) and !empty($arResult['ELEMENT']['PROPERTIES']['VERIFAED']['VALUE_ENUM_ID']) and $arResult['ACCESS']=='GRANTED'):?>
            <div class="form-group">
                <? $form->Select($arResult['PROPERTY_LIST'][239], $arResult['ELEMENT']['PROPERTIES']['VERIFAED']['VALUE_ENUM_ID'])?>
            </div>
        <? endif ?>

        <? if( !empty($arResult['ELEMENT']['PROPERTIES']['MATCHED']['VALUE_ENUM_ID']) and $arResult['GROUPS']=='ADMINS'):?>
        <div class="form-group">
            <hr>
            <input type="hidden" name="matched" value="Y">
            <? $form->select($arResult['PROPERTY_LIST'][301], $arResult['ELEMENT']['PROPERTIES']['MATCHED']['VALUE_ENUM_ID'], $arResult['ACCESS'])?>
        </div>
        <? endif?>
        
    </div>

    <!--end aside--->
    <div class="col-lg-9 col-md-9 col-xs-12 right-content">

        <div class="row form-block-top">
            <? foreach ($arResult['PROPERTY_LIST'] as $key=> $arProp): ?>
            <? if( in_array( $key, $arParams['BLOCK_TOP'] ) ):?>
            <div class="form-group col-lg-3 col-md-6 col-sm-12">
                <?
                if($arProp['CODE']=='REALTY_TYPE'){
                    if($_GET['type']){$value = intval($_GET['type']);}
                }else{
                    $value = $arResult['ELEMENT']['PROPERTIES'][$arProp['CODE']]['VALUE_ENUM_ID'];
                }
                $form->Select($arResult['PROPERTY_LIST'][$key], $value, $arResult['ACCESS']);
                ?>
            </div>
            <?endif?>
            <? endforeach;?>
        </div>

        <div class="row block-middle">

            <? foreach ($arResult['PROPERTY_LIST'] as $key=> $arProp): ?>

                <? if( in_array($key, $arParams['BLOCKLEFT'])  OR in_array($key, $arParams['BLOCK_TOP'])  ) continue; ?>
                
                <? if( ($arProp['PROPERTY_TYPE']=='S' OR $arProp['PROPERTY_TYPE']=='N') and empty($arProp['USER_TYPE']) ):?>
                    <div class="form-group col-lg-3 col-md-6 col-sm-12">
                        <?
                        $value = $arResult['ELEMENT']['PROPERTIES'][$arProp['CODE']]['VALUE'];
                        if( $arResult['HIDE']=='Y' and ($arProp['CODE']=='HOME' OR $arProp['CODE']=='APARTMENT' OR $arProp['CODE']=='PHONE') ){
                            $value = getMessage('INFO_DENIED');
                        }

                        if($arProp['CODE']=='ROOMS' and $_GET['type']==72){
                            $arProp['IS_REQUIRED'] = 'Y';
                        }
                        ?>
                        <? $form->input($arProp, $value, $arResult['ACCESS'])?>
                    </div>
                <? endif?>

                <? if($arProp['PROPERTY_TYPE']=='L' and $arProp['LIST_TYPE']!='C' and $arProp['HINT']!='BLOCK_TOP' and empty($arProp['USER_TYPE']) ):?>
                <div class="form-group col-lg-3 col-md-6 col-sm-12">
                    <? $form->Select($arResult['PROPERTY_LIST'][$key], $arResult['ELEMENT']['PROPERTIES'][$arProp['CODE']]['VALUE_ENUM_ID'], $arResult['ACCESS'])?>
                </div>
                <? endif?>

                <? if( $arProp['USER_TYPE'] ):?>
                <div class="form-group col-lg-3 col-md-6 col-sm-12">
                    <? $form->showUserType($arResult['PROPERTY_LIST'][$key], $arResult["ELEMENT"]["PROPERTIES"][$arProp['CODE']], $arResult['ACCESS'])?>
                </div>
                <? endif?>

            <? endforeach;?>
        </div>

        <div class="row block-middle">
            <? foreach ($arResult['PROPERTY_LIST'] as $key=> $arProp): ?>
                <? if($arProp['PROPERTY_TYPE']=='L' and $arProp['LIST_TYPE']=='C' and !in_array($key,$skipeProps)):?>
                    <div class="form-group col-lg-3 col-md-6 col-sm-6">
                        <?//dump($arProp)?>
                        <? $form->chechbox($arProp, $arResult['ELEMENT']['PROPERTIES'][$arProp['CODE']]['VALUE_ENUM_ID'], $arResult['ACCESS'])?>
                    </div>
                <? endif ?>
            <? endforeach;?>
        </div>

        <? if($arResult['ACCESS'] !='VIEW'):?>
        <hr>
        <div class="row">
            <div class="col-sm-12 text-center buttons-save">
                <button type="submit" class="btn btn-success btn-save" name="btn_submit" value="Y"><i class="glyphicon glyphicon-floppy-disk"></i> <?=getMessage('BTN_SUBMIT')?></button>
                <button type="submit" class="btn btn-info btn-save" name="btn_apply" value="Y"><i class="glyphicon glyphicon-floppy-saved"></i> <?=getMessage('BTN_APPLY')?></button>
            </div>
        </div>
        <? endif ?>
        <br>

    </div>
            <!----end col-lg-7-->
        </div>
    </form>

    <div class="col-lg-3 col-md-3 col-xs-12 gallery-wrapp text-center">

        <div class="gallery">
            <? if($arResult['ELEMENT']['ID']>0): ?>
            <div class="main-image-box">
                <? $main_image = SITE_TEMPLATE_PATH . '/images/nophoto.png';
                if ( !empty($arResult['ELEMENT']['DETAIL_PICTURE']['SRC']) ) {
                    $main_image = $arResult['ELEMENT']['DETAIL_PICTURE']['SRC'];
                }?>
                <a data-fancybox="images" href="<?= $main_image ?>"><img class="img-responsive" src="<?= $main_image ?>"></a>

                <? if (empty($arResult['ELEMENT']['DETAIL_PICTURE']['SRC'])) {
                    $label_upload = getMessage('ADD_MAIN_PHOTO');
                } else {
                    $label_upload = getMessage('CHANGE_MAIN_PHOTO');
                } ?>

                <? if( $arResult['ACCESS'] =='GRANTED' or $arResult['RULE']=='ADDPHOTO'): ?>
                    <form name="uploadimage" class="upload-main-image" enctype="multipart/form-data">
                        <input type="hidden" name="elID" value="<?= $arResult['ELEMENT']['ID'] ?>">
                        <label class="btn btn-file">
                            <i class="glyphicon glyphicon-camera"></i> <?=getMessage('ADD_MAIN_PHOTO')?>
                            <input type="file" class="hide main-image" name="image">
                        </label>
                    </form>
                <? endif ?>

                <div class="loading">Loading...</div>
                <?if($arResult['ELEMENT']['DETAIL_PICTURE']['SRC'] and $arResult['ACCESS'] == 'GRANTED' ):?>
                    <button class="btn btn-delete btn-detele-main-photo" data-elID="<?= $arResult['ELEMENT']['ID'] ?>" data-id="<?= $arResult['ELEMENT']['DETAIL_PICTURE']['ID'] ?>"><i class="glyphicon glyphicon-remove"></i></button>
                <?endif?>
            </div>

            <? if( !empty($arResult['MORE_PHOTO']) ):?>
            <div class="gallery-images gallery-part-hide" id="gallery-images">
                <div class="col">
                <? if($arResult['ACCESS'] =='GRANTED' OR $arResult['RULE']=='ADDPHOTO'): ?>    
                <button type="button" id="removeAllPhotos" class="btn btn-link"><i class="glyphicon glyphicon-remove"></i> Удалить все картинки</button>
                <a href="/local/ajax/getImages.php?elID=<?=$arResult['ELEMENT']['ID']?>" class="btn btn-link"><i class="glyphicon glyphicon-download"></i> Скачать все картинки</a>
                <? endif ?>
                </div>
                <? foreach ($arResult['MORE_PHOTO'] as $fk=>$arImg) : ?>
                    <div class="gallery-image" id="img_<?=$fk?>" data-id="<?=$arImg['ID']?>">
                        <a href="<?= $arImg['SRC'] ?>" data-fancybox="images"><img src="<?= $arImg['SRC'] ?>"></a>
                        <? if( $arResult['ACCESS'] =='GRANTED' or $arResult['RULE']=='ADDPHOTO' ): ?>
                        <?$imgStat = getImageStatus($arImg['ID']);?>
                        <label class="hide-image">
                            <input type="checkbox" value="<?= $arImg['ID'] ?>" <?if($imgStat['IMAGE_ID']==$arImg['ID']):?>checked<?endif?> >
                        </label>
                        <button type="button" data-elID="<?= $arResult['ELEMENT']['ID'] ?>" data-imgID="<?=$arImg['ID']?>" data-propvalID='<?= $arResult['ELEMENT']['PROPERTIES']['MORE_PHOTO']['PROPERTY_VALUE_ID'][$fk]?>' class="btn btn-delete"><i class="glyphicon glyphicon-remove"></i></button>
                        <? endif ?>
                    </div>
                <? endforeach ?>
            </div>
            <button type="button" class="btn btn-outline-info show-all-photos">Показать все</button>
            <? endif ?>

        </div>

        <? if( $arResult['ACCESS'] =='GRANTED' or $arResult['RULE']=='ADDPHOTO'): ?>
            <div class="gallery-buttons">
                <form name="uploadimages" enctype="multipart/form-data">
                    <input type="hidden" name="elID" value="<?= $arResult['ELEMENT']['ID'] ?>">
                    <label class="btn btn-info"><i class="glyphicon glyphicon-camera"></i> <?=getMessage('ADD_MORE_PHOTO');?>
                        <input type="file" class="hide" accept="image/*" multiple name="images[]" id="images">
                    </label>
                </form>
            </div>
        <?endif?>

        <?else:?>
            <img class="img-responsive" src="<?=SITE_TEMPLATE_PATH . '/images/nophoto.png'?>">
            <p class="alert alert-info text-center"><i class="glyphicon glyphicon-info-sign"></i> <?=getMessage('INFO_MORE_PHOTO')?></p>
        <?endif?>

    </div>

</div>

<script>
    $(document).ready(function() {

        $('.info-box .close').click(function(){
            $(this).parent().hide();
        });        

        $(".chosen-select").selectpicker({
            noneSelectedText : 'не выбран',
        });

        $('.region-select').change(function (e) {
            e.stopPropagation();
            let region = $(this).val();
            $.ajax({
                url:'/local/ajax/getcities.php',
                dataType:'html',
                data:'region='+region,
                success:function (data) {
                    let citySelect = $("select.city-select");
                    if( citySelect ){
                        citySelect.html(data);
                        citySelect.selectpicker('refresh');
                    }
                }
            });

            $.ajax({
                url:'/local/ajax/getzone.php',
                dataType:'html',
                data:'region_id='+region,
                success:function (data) {
                    let zoneSelect = $("select.zone-select");
                    if( zoneSelect ){
                        zoneSelect.html(data);
                        zoneSelect.selectpicker('refresh');
                    }
                }
            });

        });


        $('.zone-select').change(function (e) {
            e.stopPropagation();
            let zone = $(this).val();
            $.ajax({
                url:'/local/ajax/getstreet.php',
                dataType:'html',
                data:'zone='+zone
            }).done(function(data){
                 let streetSelect = $("select.street-select");

                 if( streetSelect ){
                     $(streetSelect).html(data);
                     $(streetSelect).selectpicker('refresh');
                 }
            });
        });


        $('.select-realty_type').change(function () {
            let val = $(this).val();
            let valID = '';
            <? if(intval($_GET['ID'])>0): ?>
            valID = '&ID=<?=$_GET['ID']?>';
            <? endif ?>
            location.href='/realty/realtyinfo/?type='+val+valID;
        });

        //Основной фото
        $('.main-image').change(function() {
            var formData = new FormData(document.forms.uploadimage);

            $.ajax({
                url: '/local/ajax/mainphoto.php',
                type: 'POST',
                contentType: false,
                processData: false,
                async: false,
                cache: false,
                dataType: 'json',
                beforeSend: function(loading) {
                    $('.loading').css("display", "block");
                },
                data: formData,
                success: function(data) {
                    $('.loading').css("display", "none");
                    location.reload();
                }
            });
        });

        $('.main-image-box .btn-delete').click(function() {

            let conf = confirm("Вы уверены что, хотите удалить?");
            if(conf){
                var imgid = $(this).attr('data-id');
                var elID = $(this).attr('data-elID');
                $.ajax({
                    url: '/local/ajax/mainphoto.php',
                    method: 'POST',
                    dataType:'json',
                    data: 'elID=' + elID + '&imgID=' + imgid,
                    success: function(data) {
                        location.reload();
                    }
                });
            }
        });


        $('.file-item .btn-delete').click(function() {

            let conf = confirm("Вы уверены что, хотите удалить?");
            if(conf){
                let fileid = $(this).attr('data-id');
                let elID = $(this).attr('data-elID');
                let propvalID  = $(this).attr('data-propvalID');
                $.ajax({
                    url: '/local/ajax/fileRemove.php',
                    method: 'POST',
                    dataType:'json',
                    data: 'elID=' + elID + '&fileID=' + fileid+'&propvalID='+propvalID,
                    success: function(data) {
                        console.log(data.status);
                        if(data.status=='OK'){
                            location.reload();
                        }
                    }
                });
            }
        });

        //Доп фото
        $('#images').change(function() {
            var formData = new FormData(document.forms.uploadimages);
            $.ajax({
                url: '/local/ajax/addphotos.php',
                type: 'POST',
                contentType: false,
                processData: false,
                dataType: 'json',
                beforeSend: function(loading) {
                    $('.loading').css("display", "block");
                },
                data: formData,
                success: function(data) {
                    $('.loading').css("display", "none");
                    location.reload();
                }
            });
        });


        $('.gallery-image .btn-delete').click(function(){
            let conf = confirm("Подверждайте уделание?");
            var elID = $(this).attr('data-elID');
            var imgID = $(this).attr('data-imgID');
            var propvalID = $(this).attr('data-propvalID');

            if(conf){
                $.ajax({
                    url: '/local/ajax/removePhotos.php',
                    method: 'POST',
                    dataType:'json',
                    data: 'elID='+elID+'&propvalID='+propvalID+'&imgID='+imgID,
                    success: function(data) {
                        if(data.STATUS=='OK'){
                            location.reload();
                        }
                    }
                });
            }
        });

        $('.show-all-photos').click(function () {
            if($('.gallery-images').hasClass('gallery-part-hide')){
                $('.gallery-images').removeClass('gallery-part-hide');
                $(this).text('Скрыть');
            }else {
                $('.gallery-images').addClass('gallery-part-hide');
                $(this).text('Показать все');
            }
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


        $('.select2-contactName').select2({
            placeholder: "Выбрать со списка",
            minimumInputLength: 2,
            ajax: {
                url: '/local/ajax/getElementContact.php',
                dataType: 'json',
                data: function (params) {
                    var query = {listContact: params.term }
                    return query;
                },
                processResults: function(response) {
                    return {
                        results: response
                    };
                }
            }
        });


        $('.select2-leadContact').select2({
            minimumInputLength: 2,
            placeholder: "Выбрать c лида",
            ajax: {
                url: '/local/ajax/getLeads.php',
                dataType: 'json',
                data: function (params) {
                    var query = {contact: params.term }
                    return query;
                },
                processResults: function(response) {
                    return {
                        results: response
                    };
                }
            }
        });


        $('.show-cont').change(function (){
            console.log($(this));
            if( $(this).prop('checked')==true ){

                if($(this).val()==1){

                    $('#contact-list').removeClass('hide');
                    $('#contact-list select').attr('required','required');

                    $('#lead-contact').addClass('hide');
                    $('#lead-contact select').removeAttr('required');

                }else{
                    $('#lead-contact').removeClass('hide');
                    $('#lead-contact select').attr('required','required');

                    $('#contact-list').addClass('hide');
                    $('#contact-list select').removeAttr('required');
                }

                $('#owner').addClass('hide');
                $('#owner select').removeAttr('required');

            }else{
                $('#owner').removeClass('hide');
                $('#owner select').attr('required','required');
            }
        });


        $('.hide-image input').click(function () {
            let elID = $('#elID').val();
            let photo_id = $(this).val();
            let state = 0;        

            if($(this).prop('checked')==true){
                state = 1
            }

            $.ajax({
                url: '/local/ajax/hideImage.php',
                type: 'POST',
                dataType:'json',
                data: 'el_id=' + elID + '&photo_id='+photo_id+'&state='+state
            }).done(function(){
                // console.log(data);
            });
        });

        $('.btn-file input, .input-file input').MultiFile();

        $('#input-price').mask('000 000 000 000 000', {reverse: true});

        $('#realty-form').submit(function(){
            $('.buttons-save').append('<div class="lock-save">Ждите ․․․ идет сохранение</div>');
        });


        $('#removeAllPhotos').click(function(){
            let confImg = confirm('Вы уверены, что хотите удалить картинки');
            elID = $('#elID').val();
            $.ajax({
                url:'/local/ajax/removePhotos.php',
                data:{elID:elID, rmPhotos:'Y'},
                dataType: 'json',
            }).done(function(data){
                // console.log(data);
                if(data.STATUS=='OK'){
                    location.reload();
                }
            });
        });


        // $('.gallery-image img').click(function () {
        //     $('.main-image-box img').attr('src', $(this).attr('src'));
        // });

                // $('.btn-file input').change(function () {
        //     let d = $(this);

        //     if($(this)[0].files){

        //         let inpfiles = d[0].files;
        //         $.each(inpfiles, function (fk,fv){
        //             let fileHtml ='<p>'+fv.name+'</p>';
        //             d.parent().parent().append(fileHtml);
        //         });
        //     }
        // });


        // $("#gallery-images").sortable({
        //     connectWith: '.gallery-images',
        //     update: function(event, ui) {
        //         console.log(this.id);
        //         var changedList = this.id;
        //         var imgsort = $(this).sortable('toArray');
        //         // var positions = order.join(' ; ');
        //         // console.log(imgsort);

        //         $.ajax({
        //             type: 'POST',
        //             data:{imgsort:imgsort},
        //             url:'/local/ajax/sortImage.php',
        //         }).done(function (data) {
        //             console.log(data);
        //         });
        //     }
        // });

        

        // $("#show-cont-lead").change(function (){
        //     // console.log($('#show-cont').prop('checked'));
        //     if( $('#show-cont-lead').prop('checked')==true ){
        //         $('#contact-list').removeClass('hide');
        //         $('#owner').addClass('hide');
        //         $('#owner select').removeAttr('required');
        //         $('#contact-list select').attr('required','required');
        //
        //     }else{
        //         $('#contact-list').addClass('hide');
        //         $('#owner').removeClass('hide');
        //         $('#owner select').attr('required','required');
        //         $('#contact-lis select').removeAttr('required');
        //     }
        // });
    });
</script>
