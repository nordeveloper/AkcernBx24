
<?php 
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("NEED_AUTH", true); 

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(intval($_REQUEST['id'])>0){
    $arZone = Zone::getByID($_REQUEST['id']);
    $arRegions = Region::getList();
}?>

    <form action="" id="form-zone-edit">
            <div class="info-box">
            </div>

            <div class="form-group">
                <label><input type="checkbox" name="active" value="1" checked> Активность</label>
            </div>

            <div class="form-group">
                <label>Сортировка</label>
                <input type="text" class="form-control" name="sort" value="<?=$arZone['sort']?>">
            </div>

            <div class="form-group">
                <label>Регион <span class="required">*</span></label>
                <select class="form-control" required name="region_id">
                    <option value=""></option>
                    <? foreach ($arRegions as $arRegion):?>
                        <option <? if($arRegion['id']==$arZone['region_id']):?>selected<?endif?> value="<?=$arRegion['id']?>"><?=$arRegion['name_'.LANGUAGE_ID]?></option>
                    <? endforeach;?>
                </select>
            </div>

            <div class="form-group">
                <label>Зоны <span class="required">*</span></label>
                <input type="text" class="form-control" name="code" placeholder="Ведите код зоны" value="<?=$arZone['code']?>">
            </div>

            <div class="form-group">
                <label>Название AM <span class="required">*</span></label>
                <input type="text" class="form-control" required name="name_am" placeholder="Название на Армянском" value="<?=$arZone['name_am']?>">
            </div>

            <div class="form-group">
                <label>Название RU <span class="required">*</span></label>
                <input type="text" class="form-control" required name="name_ru" placeholder="Название на Русском" value="<?=$arZone['name_ru']?>">
            </div>

            <div class="form-group">
                <label>Название EN <span class="required">*</span></label>
                <input type="text" class="form-control" required name="name_en" placeholder="Название на Англиском" value="<?=$arZone['name_en']?>">
            </div>

            <div class="form-group">
            <input type="hidden" name="id" value="<?=$arZone['id']?>">
            <input type="hidden" name="save" value="Y">
            <button type="submit" class="btn btn-success btn-save">Сохранить</button>                  
            </div>                     
    </form>