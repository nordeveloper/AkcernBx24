<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
$this->setFrameMode(true);

//use \Bitrix\Main\Page\Asset;
//Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/js/slick/slick.css');
//Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/js/slick/slick-theme.css');
//Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/slick/slick.min.js');

$skipeFields = array('REALTYID', 'OWNERID', 'MORE_PHOTO')
?>

<div class="row realty-list">
<?foreach($arResult["ITEMS"] as $arItem):?>
    <?
    $image = SITE_TEMPLATE_PATH.'/images/nophoto.png';
    if($arItem['DETAIL_PICTURE']['SRC']){
        $image = $arItem['DETAIL_PICTURE']['SRC'];
    }
    ?>

<div class="realty-item col-sm-12 col-lg-6">

	<div class="row realty-item-wrapp">
	<div class="realty-img col-sm-4">		
		<div class="main-img" style="background-image:url(<?=$image?>)"></div>
		<? /*if( !empty($arItem['PROPERTIES']['MORE_PHOTO']['VALUE']) ): ?>
			<div class="gallery">
			<? foreach( $arItem['PROPERTIES']['MORE_PHOTO']['VALUE'] as $imgid):?>
				<? $img = CFile::GetPath($imgid) ?>
				<div data-img="<?=$img?>" class="gallery-img">
				<img src="<?=$img?>">
				</div>
			<? endforeach?>
			</div>
		<? endif */ ?>
	</div>

	<div class="props-list col-sm-8">
		<p class="realty-number">Ндвижимость։ <b><?=$arItem['NAME']?></b></p>
		<? foreach( $arItem['PROPERTIES'] as $arProp):?>
            <? if(empty($arProp['VALUE'])) continue ?>
            <? if( in_array($arProp['CODE'], $skipeFields) ) continue; ?>
            <?
            if( ($arProp['CODE']=='HOME' or $arProp['CODE']=='APARTMENT' or $arProp['CODE']=='PHONE')
                and $USER->getID()!=$arItem['CREATED_BY'] and !$USER->isAdmin()
            ){
                $arProp['VALUE']=getMessage('INFO_HIDDEN');
            }
            ?>
            <p><b><?=$arProp['NAME']?>:</b> <span><?=$arProp['VALUE']?></span> </p>
        <? endforeach?>        
	</div>

	</div>

	<div class="col-sm-12 text-center">
        <a href="" class="show-more">Раскрыть</a>
    </div>

</div>

<?endforeach;?>
</div>


<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>

<script>

// $('.gallery').slick({slidesToShow:4,infinite: true, adaptiveHeight: true});
//
// 	$('.gallery-img').click(function(){
// 		var img = $(this).attr('data-img');
// 		$(this).parents('.realty-img').find('.main-img').css("background-image", 'url('+img+')');
// 	});

    $('.show-more').click(function(){
        var item = $(this).parents('.realty-item').find('.realty-item-wrapp');
        if(!item.hasClass('colpased')){
            item.addClass('colpased');
            $(this).text('Скрыть');
        }else{
            item.removeClass('colpased');
            $(this).text('Раскрыть');
        }
        
        return false;
    });
</script>