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
?>
<div class="rating-box">
<p><?=getMessage('RATING_TITLE')?></p>
    <p class="realty-rating">
    <? if( $arResult >0):?>
        <? for ($i=1; $i <= $arResult; $i++): ?> 
            <img data-rate="<?=$i?>" src="<?=SITE_TEMPLATE_PATH.'/images/star-active.png'?>"> 
        <? endfor?>
    <? endif?>
    
    <? for ($i=1; $i <= 7-$arResult; $i++): ?> 
        <img  data-rate="<?=$i?>" src="<?=SITE_TEMPLATE_PATH.'/images/star-deactive.png'?>"> 
    <? endfor?>
    </p>
    <div class="rate-info"></div>    
</div>

<script>
    $(document).ready(function(){
        $('.realty-rating img').click(function(){
            var rate = $(this).attr('data-rate');
            $.ajax({
            url:"<? echo $componentPath?>/ajax.php",
            type: 'POST',
            dataType: 'json',
            beforeSend: function(loading) {
                //$('.loading').css("display", "block");
            },
            data: 'realtyID=<?=$arParams['REALTY_ID']?>&rate='+rate,
            success: function(data) {
                $('.rating-box .rate-info').text(data.message).css("display", "block");                
                setTimeout(function(){
                    $('.rating-box .rate-info').css("display", "none")
                },1500);
            }
            
            });
        });
    });
</script>