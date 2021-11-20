
<?php 
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("NEED_AUTH", true); 

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if($_REQUEST['id']>0){
    $arRegion = Region::getById($_REQUEST['id']);
}?>
<form action="" id="region-form-edit" method="post">
    <div class="info-box"></div>

    <div class="form-group">
        <label>Название на Армянском</label>
        <input type="text" class="form-control" name="name_am" placeholder="Название на Армянском" value="<?=$arRegion['name_am']?>">
    </div>

    <div class="form-group">
        <label>Название на Русском</label>
        <input type="text" class="form-control" name="name_ru" placeholder="<?=getMessage('NAME_RU')?>" value="<?=$arRegion['name_ru']?>">
    </div>
    <div class="form-group">
        <label>Название на Англиском</label>
        <input type="text" class="form-control" name="name_en" placeholder="Название на Английском" value="<?=$arRegion['name_en']?>">
    </div>
    <div class="form-group">
        <input type="hidden" name="id" value="<?=$arRegion['id']?>">
        <input type="hidden" name="save" value="Y">
        <button type="submit" class="btn btn-success btn-save">Сохранить</button>
    </div>
</form>
