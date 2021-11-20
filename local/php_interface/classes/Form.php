<?php

class Forms
{
    function input($property, $value, $access=false, $namebycode=false){

        $disabled = false;
        $type = 'text';
        $inputId = '';

        if($access=='VIEW') $disabled = ' disabled ';

        $label = $property['NAME'];
        if(LANGUAGE_ID=='en'){ $label = $property['HINT']; }

        // if($property['PROPERTY_TYPE']=='S'){ $type='text';}
        // if($property['PROPERTY_TYPE']=='N'){ $type='number';}

        $required = '';
        $reqField = '';
        if($property['IS_REQUIRED']=="Y"){
            $required = ' <span class="required">*</span>';
            $reqField = 'required';
        }

        echo '<label class="c-label-text">'.$label.$required.'</label>';

        if(!$namebycode){
            $name = 'PROPERTY['.$property['ID'].']';
        }else{
            $name = $property['CODE'];
        }

        if( !empty($property['CODE']) ){
            $inputId = 'id="input-'.strtolower($property['CODE']).'"';
        }        

        echo '<input min="0" '.$reqField.' step="any" class="form-control c-form-text" '.$inputId.' name="'.$name.'" '.$disabled.' type="'.$type.'" value="'.$value.'">';
    }


    function Select($property, $selectedVal, $access=false, $namebycode=false){

        $multi = '';
        $multiple = '';
        $multipleCSS = '';
        $disabled = '';

        if($access=='VIEW') $disabled = ' disabled ';

        $label = $property['NAME'];
        if(LANGUAGE_ID=='en'){ $label = $property['HINT']; }

        if($property['MULTIPLE']=='Y'){
            $multiple = ' multiple ';
            $multipleCSS = ' chosen-select ';
            $multi = '[]';
        }

        $required = '';
        $reqField = '';
        if($property['IS_REQUIRED']=="Y"){
            $required = ' <span class="required">*</span>';
            $reqField = 'required';
        }

        if(!$namebycode){
            $name = 'PROPERTY['.$property['ID'].']'.$multi;
        }else{
            $name = $property['CODE'].$multi;
        }

        echo '<label class="c-label-text">'.$label.$required.'</label>';
        echo '<select '.$disabled.$multiple.$reqField.' class="form-control c-form-text '.$multipleCSS.strtolower('select-'.$property['CODE']).'" name="'.$name.'">';
        echo '<option value=""></option>';

        if(!empty($property['ENUM'])){

            foreach($property['ENUM'] as $el){
                $selected = false;

                if($property['MULTIPLE']=='Y'){
                    foreach ($selectedVal as $vl){
                        if($vl==$el['ID']){
                            $selected = 'selected';
                        }
                    }
                }else{
                    if($selectedVal == $el['ID']){
                        $selected = 'selected';
                    }
                }
                echo '<option '.$selected.' value="'.$el['ID'].'">'.$el['VALUE'].'</option>';
            }

        }
        echo '</select>';
    }
    

    function chechbox($property, $value, $access=false){

        $disabled = false;
        if($access=='VIEW') $disabled = ' disable';

        if(LANGUAGE_ID=='en'){ $name = $property['HINT'];} else{$name = $property['NAME'];}

        echo '<label>'.$name.'</label>';

        foreach ($property['ENUM'] as $key=> $item){
            $checked = '';
            
            if($key==$value) $checked = 'checked';

            if($property['MULTIPLE']=='Y'){

                if(in_array($key, $value)){
                    $checked = 'checked';
                }

                if($item['DEF']=='Y' and empty($_GET['ID']) ){
                    $checked = 'checked';
                }

                $label = $item['VALUE'];                               
                $inputname = 'PROPERTY['.$property['ID'].']['.$item['ID'].']';
                echo '<div class="custom-control custom-checkbox">';
                echo '<input type="checkbox" class="custom-control-input" '.$disabled.' id="customCheck'.$item['ID'].'" '.$checked.' name="'.$inputname.'" value="'.$item['ID'].'">';
                echo '<label class="custom-control-label" for="customCheck'.$item['ID'].'">'.$label.'</label>';
                echo '</div>';                
            }else{
                
                if($item['DEF']=='Y' and empty($_GET['ID']) ){
                    $checked = 'checked';
                }

                $label = $item['VALUE'];
                $inputname = 'PROPERTY['.$property['ID'].']';
                echo '<div class="custom-control custom-checkbox"><input type="hidden" name="'.$inputname.'">';
                echo '<input type="checkbox" class="custom-control-input" name="'.$inputname.'[]" id="customCheck'.$item['ID'].'" '.$checked.' '.$disabled.' value="'.$item['ID'].'">';
                echo '<label class="custom-control-label" for="customCheck'.$item['ID'].'">'.$label.'</label>';
                echo '</div>';
            }
        }

    }


    function TextArea($property, $value, $access=false){
        $disabled = false;
        if($access=='VIEW') $disabled = ' disable';
        echo '<textarea class="form-control" '.$disabled.' name="PROPERTY['.$property['ID'].']">'.$value.'</textarea>';
    }    


    function showUserType($property, $value=false, $access = false, $namebycode=false){

        $property['ACCESS']=$access;

        if($namebycode==true){
            $fieldName = $property['CODE'];
        }else{
            $fieldName = 'PROPERTY['.$property['ID'].']';
        }

        $label = $property['NAME'];
        if(LANGUAGE_ID=='en'){ $label = $property['HINT']; }

        $required = '';
        if($property['IS_REQUIRED']=="Y"){
            $required = ' <span class="required">*</span>';
        }

        echo '<label class="c-label-text">'.$label.$required.'</label>';

            if($property['USER_TYPE']=='Date' or $property['USER_TYPE']=='DateTime') {
                echo '<div class="input-date-box">';
            }

            echo call_user_func_array( $property['GetPublicEditHTML'] ,
                array(
                    $property,
                    array(
                        "VALUE" => $value['VALUE'],
                        "DESCRIPTION" => $property['NAME'],
                    ),
                    array(
                        "VALUE" => $fieldName,
                        "DESCRIPTION" => $property['NAME']
                    ),
                ));

            if($property['USER_TYPE']=='Date' or $property['USER_TYPE']=='DateTime') {
                echo '</div>';
            }
    }


    function SelectFloor($name, $label, $selectedItem, $access){

        $disabled = false;
        if($access=='VIEW') $disabled = ' disable';

        $arFloօrs = array(
            'Н', 'М', 'Б', 'П', 'Пп', 1, 2, 3, 4, 5, 6, 7, 8, 9,10, 11, 12, 13, 14, 15, 16, 17, 18,19,20,21,22,23,24,25
        );

        echo '<label>'.$label.'</label>';
        echo '<select '.$disabled.' class="form-control floor-select" name="'.$name.'">';
        echo '<option value=""></option>';
        foreach($arFloօrs as $floor ){
            $selected = '';
            if($selectedItem==$floor) $selected = 'selected';
            echo '<option '.$selected.' value="'.$floor.'">'.$floor.'</option>';
        }
        echo '</select>';
    }


    function SelectFloors($propName, $propLabel, $selectedItem, $access){

        $disabled = false;
        if($access=='VIEW') $disabled = ' disable';

        echo '<label>'.$propLabel.'</label>';
        echo '<select '.$disabled.' class="form-control floors-select" name="'.$propName.'">';
        echo '<option value=""></option>';
        for($i=1; $i<26; $i++ ){
            $selected = '';
            if($selectedItem==$i) $selected = 'selected';
            echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';
        }
        echo '</select>';
    }


    function SelectRegion($propID, $selectedVal=false, $access=false){
        if($access=='VIEW') $disabled = ' disable';
        $arData = Region::getList();
        echo '<select required '.$disabled.' name="PROPERTY['.$propID.']" class="region-select form-control">';
        echo '<option value="">Region</option>';
        foreach($arData as $item){
            $selected = '';
            if($selectedVal == $item['id']){
                $selected = 'selected';
            }
            echo '<option '.$selected.' value="'.$item['id'].'">'.$item['name_'.LANGUAGE_ID].'</option>';
        }
        echo '</select>';
    }


    function SelectCities($propID, $selectedVal=false, $access=false){
        if($access=='VIEW') $disabled = ' disable';
        $arData = City::getList();
        echo '<select '.$disabled.' name="PROPERTY['.$propID.']" class="city-select form-control">';
        echo '<option value="">City</option>';
        foreach($arData['ITEMS'] as $item){
            $selected = '';
            if($selectedVal == $item['id']){
                $selected = 'selected';
            }
            echo '<option '.$selected.' value="'.$item['id'].'">'.$item['name_'.LANGUAGE_ID].'</option>';
        }
        echo '</select>';
    }


    function SelectZone($propID, $selectedItem=false, $access=false, $required=''){

        $arData = Zone::getList();

        $disabled = '';
        if($access=='VIEW') $disabled='disabled';

        echo '<select '.$disabled.' '.$required.' name="PROPERTY['.$propID.']" class="zone-select form-control">';
        echo '<option value="">Zone</option>';
        foreach($arData as $item){
            $selected = '';
            if($selectedItem == $item['code']){
                $selected = 'selected';
            }
            echo '<option '.$selected.' value="'.$item['code'].'">'.$item['code'].'</option>';
        }
        echo '</select>';
    }


    function SelectStreet($propID, $selectedItem=false, $readOnly= ''){
        $arData = Street::getList();
        if($readOnly) $readOnly='disabled';

        //dump($arData);

        echo '<select '.$readOnly.' name="PROPERTY['.$propID.']" class="street-select form-control">';
        echo '<option value="">Сначало выберите зону</option>';
        if(!empty($selectedItem)){
            foreach($arData as $item){
                $selected = '';
                if($selectedItem == $item['id']){
                    $selected = 'selected';
                }
                echo '<option '.$selected.' value="'.$item['id'].'">'.$item['name_'.LANGUAGE_ID].'</option>';
            }
        }
        echo '</select>';
    }


    function InputFile($property, $namebycode=false, $access=false){

        if($property['PROPERTY_TYPE']=='F'){

            $multiple = false;
            $multi = '';
            if($property['MULTIPLE']=='Y'){ $multiple = ' multiple '; $multi='[]';}

            $label = $property['NAME'];

            if(LANGUAGE_ID=='en'){ $label = $property['HINT']; }
            if(!$namebycode){
                $name = 'PROPERTY['.$property['ID'].']';
            }else{
                $name = $property['CODE'];
            }

            echo '<label class="c-label-file btn btn-info btn-file"><i class="glyphicon glyphicon-file"></i><input '.$multiple.' type="file" name="'.$name.$multi.'">'.$label.'</label><div class="selected-files"></div>';
        }else{
            echo 'This property type not file';
        }
    }

}