
<?php 
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("NEED_AUTH", true); 

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(intval($_REQUEST['id'])>0){
    $arRegions = Region::getList();
    $arCities = City::getList();
    $arStreet = Street::getByID($_REQUEST['id']);
    $arZones = Zone::getList();
}?>
        <form action="" id="street-form-edit">
            <div class="info-box">

            </div>
            <div class="form-group">
                <label><input type="checkbox" name="active" value="1" checked> Активность</label>
            </div>
            <div class="form-group">
                <label>Регион <span class="required">*</span></label>
                <select class="form-control region-select" required name="region_id">
                    <option value=""></option>
                    <? foreach ($arRegions as $arRegion):?>
                        <option <?if($arRegion['id']==$arStreet['region_id']):?>selected<?endif?> value="<?=$arRegion['id']?>"><?=$arRegion['name_'.LANGUAGE_ID]?></option>
                    <? endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <label>Город/Село</label>
                <select class="form-control city-select" name="city_id">
                    <option value=""></option>
                    <? foreach ($arCities['ITEMS'] as $arCity):?>
                        <option <?if($arCity['id']==$arStreet['city_id']):?>selected<?endif?> value="<?=$arCity['id']?>"><?=$arCity['name_'.LANGUAGE_ID]?></option>
                    <? endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <label>Зоны <span class="required">*</span></label>
                <select name="zone_code[]" multiple required class="form-control zone-select">
                    <option value="">Zone</option>
                    <? foreach ($arZones as $zone): ?>
                        <? $zones = explode(',', $arStreet['zone_code']); ?>
                        <option <? if( in_array($zone['code'], $zones) ):?>selected<?endif?> value="<?=$zone['code']?>"><?=$zone['code']?></option>
                    <? endforeach ?>
                </select>
                <input type="hidden" name="zone_id">
            </div>

            <div class="form-group">
                <label>Название на Армянском <span class="required">*</span></label>
                <input type="text" class="form-control" required name="name_am" value="<?=$arStreet['name_am']?>">
            </div>
            <div class="form-group">
                <label>Название улицы на Русском  <span class="required">*</span></label>
                <input type="text" class="form-control" required name="name_ru" value="<?=$arStreet['name_ru']?>">
            </div>           
            <div class="form-group">
                <label>Название на Англиском <span class="required">*</span></label>
                <input type="text" class="form-control" required name="name_en" value="<?=$arStreet['name_en']?>">
            </div>

            <div class="form-group">
            <input type="hidden" name="id" value="<?=$arStreet['id']?>">
            <input type="hidden" name="save" value="Y">
            <button type="submit" class="btn btn-success btn-save">Сохранить</button>                  
            </div>                     
        </form>
