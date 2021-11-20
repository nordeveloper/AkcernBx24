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
$arRegions = Region::getList();
//$arCities = City::getList();
$arZones = Zone::getList();
?>
<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="ModalAdd">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title"> <?=getMessage('MODAL_ADD_TITLE')?></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">        
        <form action="" id="street-form-add">
            <div class="info-box"></div>
            <div class="form-group">
                <label><input type="checkbox" name="active" value="1" checked> <?=getMessage('ACTIVE')?></label>
            </div>
            <div class="form-group">
                <label><?=getMessage('REGION')?> <span class="required">*</span></label>
                <select class="form-control region-select" required name="region_id">
                    <option value=""></option>
                    <? foreach ($arRegions as $arRegion):?>
                        <option value="<?=$arRegion['id']?>"><?=$arRegion['name_'.LANGUAGE_ID]?></option>
                    <? endforeach;?>
                </select>
            </div>

            <div class="form-group">
                <label>Город/Село</label>
                <select class="form-control city-select" name="city_id">
                    <option value=""></option>
                </select>
            </div>

            <div class="form-group">
                <label><?=getMessage('ZONE')?> <span class="required">*</span></label>
                <select name="zone_code[]" multiple required class="form-control zone-select">
                    <option value="">Zone</option>
                    <? foreach ($arZones as $zone): ?>
                        <option value="<?=$zone['code']?>"><?=$zone['code']?></option>
                    <? endforeach ?>
                </select>
            </div>

            <div class="form-group">
                <label><?=getMessage('NAME_AM')?> <span class="required">*</span></label>
                <input type="text" required class="form-control" name="name_am">
            </div>
            <div class="form-group">
                <label><?=getMessage('NAME_RU')?> <span class="required">*</span></label>
                <input type="text"  required class="form-control" name="name_ru">
            </div>
            <div class="form-group">
                <label><?=getMessage('NAME_EN')?> <span class="required">*</span></label>
                <input type="text" required class="form-control" name="name_en">
            </div>

            <div class="form-group">
            <input type="hidden" name="add" value="Y">
            <button type="submit" class="btn btn-success btn-save"><?=getMessage('BTN_SAVE')?></button>
            </div>

        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->





<div class="modal fade" id="EditModal" tabindex="-1" role="dialog" aria-labelledby="ModalAdd">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title"><?getMessage('MODAL_EDIT_TITLE')?></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">    

      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->  




<script>
    $(document).ready(function(){

        $('#street-form-add').submit(function(){

            frm = $(this);
            $.ajax({
                url:'<?=$componentPath?>/ajax.php',
                data:frm.serialize(),
                dataType:'json',
                method:'post',
                success:function(data){
                    if(data.status==='success'){
                        $('.info-box', frm).html('<p class="alert alert-success">'+data.message+'<p>');
                        setTimeout(function () {
                            location.reload();
                        }, 1500)
                    }else{
                        $('.info-box', frm).html('<p class="alert alert-danger">'+data.message+'<p>');
                    }
                }
            });
            return false;
        });


        $('#EditModal').on('submit', '#street-form-edit', function(){
            frm = $(this);
            $.ajax({
                url:'<?=$componentPath?>/ajax.php',
                data:frm.serialize(),
                dataType:'json',
                method:'post',
                success:function(data){
                    if(data.status==='success'){
                        $('.info-box', frm).html('<p class="alert alert-success">'+data.message+'<p>');
                        // setTimeout(function () {
                        //     location.reload();
                        // }, 1500)
                    }else{
                        $('.info-box', frm).html('<p class="alert alert-danger">'+data.message+'<p>');
                    }
                }

            });
            return false;
        });

        $('body').on('change', '.region-select', function (){
            $.ajax({
                url:'/local/ajax/getcities.php?region='+$(this).val(),
                data:'',
                dataType:'html',
                method:'post',
                success:function(data){
                    console.log(data);
                    if(data){
                        $('.city-select').html(data);
                    }
                }
            });
            return false;
        });


    });
</script>
