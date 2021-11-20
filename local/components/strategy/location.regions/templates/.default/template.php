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
<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="ModalAdd">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title"> <?=getMessage('BTN_ADD')?></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">        
        <form action="" id="street-form-add">
            <div class="info-box"></div>
            <?/*
            <div class="form-group">
                <label><input type="checkbox" name="active" value="1" checked> <?=getMessage('ACTIVE')?></label>
            </div>*/?>
            <div class="form-group">
                <label><?=getMessage('NAME_AM')?></label>
                <input type="text" class="form-control" name="name_am" placeholder="Название на Армянском">
            </div>
            <div class="form-group">
                <label><?=getMessage('NAME_RU')?></label>
                <input type="text" class="form-control" name="name_ru" placeholder="<?=getMessage('NAME_RU')?>">
            </div>           
            <div class="form-group">
                <label><?=getMessage('NAME_EN')?></label>
                <input type="text" class="form-control" name="name_en" placeholder="Название на Англиском">
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
          <h4 class="modal-title"> Редактировать</h4>
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
                success:function(data){
                    if(data.status==='success'){
                        $('.info-box', frm).html('<p class="alert alert-success">'+data.message+'</p>');
                    }else{
                        $('.info-box', frm).html('<p class="alert alert-danger">'+data.message+'</p>');
                    }
                }
            });
            return false;
        });


    $('#EditModal').on('submit', '#region-form-edit', function(){
        frm = $(this);
        $.ajax({
            url:'<?=$componentPath?>/ajax.php',
            data:frm.serialize(),
            method:'post',
            dataType:'json',
            success:function(data){
                if(data.status==='success'){
                    $('.info-box', frm).html('<p class="alert alert-success">'+data.message+'</p>');
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                }else{
                    $('.info-box', frm).html('<p class="alert alert-danger">'+data.message+'</p>');
                }
            }
        });
        return false;        
    });


    });
</script>
