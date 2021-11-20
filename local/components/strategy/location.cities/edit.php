
<?php 
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("NEED_AUTH", true); 

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arRegions = Region::getList();

if($_REQUEST['id']>0){
    $arCity = City::getById($_REQUEST['id']);
}?>
<form action="" id="region-form-edit" method="post">
    <div class="info-box"></div>

    <div class="form-group">
        <label>Регион <span class="required">*</span></label>
        <select class="form-control" required name="region_id">
            <option value=""></option>
            <? foreach ($arRegions as $arRegion):?>
                <option <?if($arRegion['id']==$arCity['region_id']):?>selected<?endif?> value="<?=$arRegion['id']?>"><?=$arRegion['name_'.LANGUAGE_ID]?></option>
            <? endforeach;?>
        </select>
    </div>

    <div class="form-group">
        <label>Название на Армянском <span class="required">*</span></label>
        <input type="text" class="form-control" name="name_am" required placeholder="Название на Армянском" value="<?=$arCity['name_am']?>">
    </div>
    <div class="form-group">
        <label><?=getMessage('NAME_RU')?> <span class="required">*</span></label>
        <input type="text" class="form-control" name="name_ru" required placeholder="<?=getMessage('NAME_RU')?>" value="<?=$arCity['name_ru']?>">
    </div>
    <div class="form-group">
        <label>Название на Англиском <span class="required">*</span></label>
        <input type="text" class="form-control" name="name_en" required placeholder="Название на Английском" value="<?=$arCity['name_en']?>">
    </div>
    <div class="form-group">
        <input type="hidden" name="id" value="<?=$arCity['id']?>">
        <input type="hidden" name="save" value="Y">
        <button type="submit" class="btn btn-success btn-save">Сохранить</button>
    </div>
</form>
