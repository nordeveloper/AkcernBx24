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
<p>Оценка клиента</p>
<div class="rating-box">
    <p class="realty-rating">
        <? if( $arResult >0):?>
            <? for ($i=0; $i < $arResult; $i++): ?>
                <img data-rate="<?=$i?>" src="<?=SITE_TEMPLATE_PATH.'/images/star-active.png'?>">
            <? endfor?>
        <? endif?>

        <? for ($i=0; $i <= 4-$arResult; $i++): ?>
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
            /*beforeSend: function(loading) {               
            },*/
            data: 'contact_id=<?=$arParams['CONTACT_ID']?>&user_id=<?=$USER->getID()?>&rate='+rate,
            success: function(data) {
                console.log(data.message);
                $('.rate-info').text(data.message).css("display", "block");
                
                setTimeout(function(){
                    $('.rate-info').css("display", "none")
                },2000);
            }
            
            });

        });

    });
</script>
