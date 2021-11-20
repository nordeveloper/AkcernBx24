<?php
AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('Regions', 'GetUserTypeDescription'));

class Regions{

    public static function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => "N",
            "USER_TYPE" => "regions",
            "DESCRIPTION" => 'Regions',
            "GetPublicEditHTML" => array("Regions","GetPublicEditHTML"),
            "GetPropertyFieldHtml" => array("Regions","GetPropertyFieldHtml"),
            // "GetUIFilterProperty" => array("Regions","GetUIFilterProperty"),
            "GetAdminListViewHTML"=>array("Regions", "GetAdminListViewHTML")
        );
    }


    public static function GetPublicEditHTML($arProperty, $value, $strHTMLControlName){

        $disabled = '';
        $multi='';
        $regionList = Region::getList();

        if($arProperty['ACCESS']=='VIEW'){$disabled = ' disabled ';}

        if($arProperty['MULTIPLE']=='Y'){
            $multiple = ' multiple '; $multi='[]';
        };

        $str = '<select '.$disabled.$multiple.' class="region-select form-control c-form-text" name="'.$strHTMLControlName["VALUE"].$multi.'">';
        $str.= '<option value=""></option>';
        foreach($regionList as $arSelect){
            $selected = false;

            if($arProperty['MULTIPLE']=='Y'){
                foreach($value["VALUE"] as $val){
                    if($val==$arSelect['value'])
                        $selected = ' selected ';
                }
            }else{
                if($value["VALUE"]==$arSelect['id']){
                    $selected = ' selected ';
                }
            }

            $str.='<option'.$selected.' value="'.$arSelect['id'].'">'.$arSelect['name_'.LANGUAGE_ID].'</option>';
        }
        $str.= '</select>';
        return $str ;
        
    }


    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $regionList = Region::getList();
        $multi='';

        if($arProperty['MULTIPLE']=='Y'){ $multiple = ' multiple '; $multi='[]'; };

        $str = '<select '.$multiple.' class="form-control" name="'.$strHTMLControlName["VALUE"].$multi.'">';
        $str.= '<option value=""></option>';
        foreach($regionList as $arSelect){
            $selected = false;
            if($arProperty['MULTIPLE']=='Y'){
                foreach($value["VALUE"] as $val){
                    if($val==$arSelect['value'])
                        $selected = ' selected ';
                }
            }else{
                if($value["VALUE"]==$arSelect['id']){
                    $selected = ' selected ';
                }
            }
            $str.='<option'.$selected.' value="'.$arSelect['id'].'">'.$arSelect['name_'.LANGUAGE_ID].'</option>';
        }
        $str.= '</select>';
        return $str;
    }


    public static function GetUIFilterProperty($arProperty, $strHTMLControlName, &$fields=false){
        $items = array();
        $regionList = Region::getList();

        // dump($regionList);

        foreach ($regionList as $arSelect){
            $items[$arSelect['id']] = $arSelect['name_'.LANGUAGE_ID];
        }

        $fields["type"] = 'list';
        $fields['filterable'] = 'Y';
        $fields['items'] = $items;
    }

    public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName){
        $res = Region::getById($value['VALUE']);
        return $res['name_'.LANGUAGE_ID];
    }

}



AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('Cities', 'GetUserTypeDescription'));

class Cities{

    public static function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => "N",
            "USER_TYPE" => "city",
            "DESCRIPTION" => 'Cities',
            "GetPublicEditHTML" => array("Cities","GetPublicEditHTML"),
            "GetPropertyFieldHtml" => array("Cities","GetPropertyFieldHtml"),
            "GetAdminListViewHTML"=>array("Cities", "GetAdminListViewHTML")
        );
    }


    public static function GetPublicEditHTML($arProperty, $value, $strHTMLControlName){

        $disabled = '';
        $multi='';

        if($arProperty['ACCESS']=='VIEW'){$disabled = ' disabled ';}

        if($arProperty['MULTIPLE']=='Y'){
            $multiple = ' multiple '; $multi='[]';
        };

        $str = '<select '.$disabled.$multiple.' class="city-select chosen-select form-control c-form-text" data-live-search="true" data-placeholder=" " name="'.$strHTMLControlName["VALUE"].$multi.'">';
        $str.= '<option value="">Сначало выберите регион</option>';

        if(!empty($value["VALUE"])){
            $city = City::getById($value["VALUE"]);
            $str.='<option selected value="'.$city['id'].'">'.$city['name_'.LANGUAGE_ID].'</option>';
        }

        $str.= '</select>';
        return $str ;
    }


    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {

        $multi='';

        if($arProperty['MULTIPLE']=='Y'){ $multiple = ' multiple '; $multi='[]'; };

        $str = '<select '.$multiple.' class="form-control" name="'.$strHTMLControlName["VALUE"].$multi.'">';

        if(!empty($value["VALUE"])){
            $city = City::getById($value["VALUE"]);
            $str.='<option selected value="'.$city['id'].'">'.$city['name_'.LANGUAGE_ID].'</option>';
        }

        $str.= '</select>';
        return $str;
    }


    public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName){
        $res = City::getById($value['VALUE']);
        return $res['name_'.LANGUAGE_ID];
    }

}





AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('Zones', 'GetUserTypeDescription')); //построение списка свойств инфоблока

class Zones
{
    public static function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "zones",
            "DESCRIPTION" => 'Zones',
            "GetPublicEditHTML" => array("Zones","GetPublicEditHTML"),
            "GetPropertyFieldHtml" => array("Zones","GetPropertyFieldHtml"),
            "GetUIFilterProperty" => array("Zones","GetUIFilterProperty")
        );
    }

    public static function GetPublicEditHTML($arProperty, $value, $strHTMLControlName){

        $disabled = '';
        $multi='';
        $multipleCSS='';
        $cssClass = 'form-control c-form-text chosen-select '. strtolower($arProperty['CODE']).'-select';

        if($arProperty['ACCESS']=='VIEW'){ $disabled = ' disabled ';}
        if($arProperty['MULTIPLE']=='Y'){ $multiple = ' multiple '; $multi='[]';}

        $ZonesList = Zone::getList();

        $str = '<select '.$disabled.$multiple.' data-live-search="true" class="'.$cssClass.'" name="'.$strHTMLControlName["VALUE"].$multi.'" data-placeholder=" ">';
        $str.= '<option value=""></option>';
        foreach($ZonesList as $arSelect){
            $selected = false;

            if($arProperty['MULTIPLE']=='Y'){
                foreach($value["VALUE"] as $val){
                    if($val==$arSelect['code'])
                    $selected = ' selected ';
                }
            }else{
                if($value["VALUE"]==$arSelect['code']){
                    $selected = ' selected ';
                }
            }

            $str.='<option'.$selected.' value="'.$arSelect['code'].'">'.$arSelect['code'].'</option>';
        }
        $str.= '</select>';
        return $str ;
    }


    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $multi='';
        $multiple = '';
        $ZonesList = Zone::getList();
        if($arProperty['MULTIPLE']=='Y'){ $multiple = ' multiple '; $multi='[]';}

        //single and multiple
        $str = '<select '.$multiple.' name="'.$strHTMLControlName["VALUE"].$multi.'">';
        $str.= '<option value=""></option>';

        foreach($ZonesList as $arSelect){
            $selected = false;
            if($arProperty['MULTIPLE']=='Y'){
                foreach($value["VALUE"] as $val){
                    if($val==$arSelect['code'])
                        $selected = ' selected ';
                }
            }else{
                if($value["VALUE"]==$arSelect['code']){
                    $selected = ' selected ';
                }
            }

            $str.='<option'.$selected.' value="'.$arSelect['code'].'">'.$arSelect['code'].'</option>';
        }
        $str.= '</select>';

        return $str;
    }


    public static function GetUIFilterProperty($arProperty, $strHTMLControlName, &$fields=false){
        $items = array();
        $ZonesList = Zone::getList();

        if( !empty($ZonesList) ){
            foreach ($ZonesList as $arSelect){
                $items[$arSelect['id']] = $arSelect['code'];
            }

            $fields["type"] = 'list';
            $fields['filterable'] = 'Y';
            $fields['items'] = $items;
        }
    }
}


AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('Streets', 'GetUserTypeDescription'));

class Streets
{
    public static function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => "N",
            "USER_TYPE" => "streets",
            "DESCRIPTION" => 'Streets',
            "GetPublicEditHTML" => array("Streets","GetPublicEditHTML"),
            "GetPropertyFieldHtml" => array("Streets","GetPropertyFieldHtml"),
            // "GetUIFilterProperty" => array("Streets","GetUIFilterProperty"),
            "GetAdminListViewHTML"=>array("Streets", "GetAdminListViewHTML")
        );
    }


    public static function GetPublicEditHTML($arProperty, $value, $strHTMLControlName){
        
        $disabled = '';
        $multi='';
        $cssClass = 'form-control chosen-select '. strtolower($arProperty['CODE']).'-select';

        if($arProperty['ACCESS']=='VIEW'){$disabled = ' disabled ';}

        if($arProperty['MULTIPLE']=='Y'){ $multiple = ' multiple '; $multi='[]';}


        $str = '<select '.$disabled.$multiple.' data-live-search="true" class="'.$cssClass.'" name="'.$strHTMLControlName["VALUE"].$multi.'"  data-placeholder=" ">';
        $str.= '<option value="">Сначало выберите зону</option>';

        if( !empty($value["VALUE"]) ){
            $streetSelected = Street::getByID($value["VALUE"]);
            $str.='<option selected value="'.$streetSelected['id'].'">'.$streetSelected['name_'.LANGUAGE_ID].'</option>';
        }

        $str.= '</select>';
        return $str;
    }


    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $disabled = '';
        $multi='';
        $cssClass = 'form-control chosen-select '. strtolower($arProperty['CODE']).'-select';

        if($arProperty['MULTIPLE']=='Y'){ $multiple = ' multiple '; $multi='[]';}

        $str = '<select '.$disabled.$multiple.' data-live-search="true" class="'.$cssClass.'" name="'.$strHTMLControlName["VALUE"].$multi.'"  data-placeholder=" ">';
        $str.= '<option value="">Сначало выберите зону</option>';

        if( !empty($value["VALUE"]) ){
            $streetSelected = Street::getByID($value["VALUE"]);
            $str.='<option selected value="'.$streetSelected['id'].'">'.$streetSelected['name_'.LANGUAGE_ID].'</option>';
        }

        $str.= '</select>';
        return $str;
    }


    public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName){
        $res = Street::getById($value['VALUE']);
        return $res['name_'.LANGUAGE_ID];
    }

    public static function GetUIFilterProperty($arProperty, $strHTMLControlName, &$fields=false){
        $items = array();
        $streetList = Street::getList();

        foreach ($streetList['ITEMS'] as $arSelect){
            $items[$arSelect['id']] = $arSelect['name_'.LANGUAGE_ID];
        }

        $fields["type"] = 'list';
        $fields['filterable'] = 'Y';
        $fields['items'] = $items;
    }

}


AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('Floor', 'GetUserTypeDescription'));

class Floor
{
    public static function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "floor",
            "DESCRIPTION" => 'Floor',
            "GetPropertyFieldHtml" => array("Floor","GetPropertyFieldHtml"),
            "GetPublicEditHTML" => array("Floor","GetPublicEditHTML"),
            "GetUIFilterProperty" => array("Floor","GetUIFilterProperty")
        );
    }

    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $disabled = '';
        $multi = '';
        $multiple = '';
        $multipleCSS ='';
        $cssClass = strtolower($arProperty['CODE']).'-select';

        $floors = FloorList();

        if($arProperty['ACCESS']=='VIEW')  $disabled = ' disabled ';

        if($arProperty['MULTIPLE']=='Y'){ $multiple = ' multiple '; $multi='[]'; $multipleCSS = ' chosen-select '; }

        $str = '<select '.$disabled.$multiple.' class="form-control c-form-text '.$cssClass.$multipleCSS.'" name="'.$strHTMLControlName["VALUE"].$multi.'">';
        $str.= '<option value=""></option>';

        foreach($floors as $key=> $arSelect){
            $selected = false;
            if($arProperty['MULTIPLE']=='Y'){
                foreach($value["VALUE"] as $val){
                    if($val==$arSelect)
                        $selected = ' selected ';
                }
            }else{
                if($value["VALUE"]==$arSelect){
                    $selected = ' selected ';
                }
            }

            $str.='<option'.$selected.' value="'.$arSelect.'">'.$arSelect.'</option>';
        }

        $str.= '</select>';
        return $str;
    }


    public static function GetPublicEditHTML($arProperty, $value, $strHTMLControlName){

        $disabled = '';
        $multi = '';
        $multiple = '';
        $multipleCSS ='';
        $cssClass = strtolower($arProperty['CODE']).'-select';

        $floors = FloorList();

        if($arProperty['ACCESS']=='VIEW')  $disabled = ' disabled ';

        if($arProperty['MULTIPLE']=='Y'){ $multiple = ' multiple '; $multi='[]'; $multipleCSS = ' chosen-select '; }

        $str = '<select '.$disabled.$multiple.' class="form-control c-form-text '.$cssClass.$multipleCSS.'" name="'.$strHTMLControlName["VALUE"].$multi.'">';
        $str.= '<option value=""></option>';

        foreach($floors as $key=> $arSelect){
            $selected = false;
            if($arProperty['MULTIPLE']=='Y'){
                foreach($value["VALUE"] as $val){
                    if($val==$arSelect)
                    $selected = ' selected ';
                }
            }else{
                if($value["VALUE"]==$arSelect){
                    $selected = ' selected ';
                }
            }

            $str.='<option'.$selected.' value="'.$arSelect.'">'.$arSelect.'</option>';
        }

        $str.= '</select>';
        return $str;
    }


    public static function GetUIFilterProperty($arProperty, $strHTMLControlName, &$fields=false){
        $floors = FloorList();
        $items = array();
        foreach ($floors as $arSelect){
            $items[$arSelect] = $arSelect;
        }
        $fields["type"] = 'list';
        $fields['filterable'] = '';
        $fields['items'] = $items;
    }

}




AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('Floors', 'GetUserTypeDescription'));

class Floors
{
    public static function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => "N",
            "USER_TYPE" => "floors",
            "DESCRIPTION" => 'Floors',
            "GetPropertyFieldHtml" => array("Floors","GetPropertyFieldHtml"),
            "GetPublicEditHTML" => array("Floors","GetPublicEditHTML"),
            "GetUIFilterProperty" => array("Floors","GetUIFilterProperty")
        );
    }

    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $floors = FloorsList();

        $str = '<select class="floors-select form-control" name="'.$strHTMLControlName["VALUE"].'">';
        $str.= '<option value="">Floors</option>';
        foreach($floors as $arSelect){
            $selected = false;
            if($value["VALUE"]==$arSelect){
                $selected = ' selected ';
            }
            $str.='<option'.$selected.' value="'.$arSelect.'">'.$arSelect.'</option>';
        }
        $str.= '</select>';
        return $str;
    }


    public static function GetPublicEditHTML($arProperty, $value, $strHTMLControlName){

        $floors = FloorsList();

        if($arProperty['ACCESS']=='VIEW')  $disabled = ' disabled ';

        $str = '<select '.$disabled.' class="floors-select form-control" name="'.$strHTMLControlName["VALUE"].'">';
        $str.= '<option value=""></option>';
        foreach($floors as $arSelect){
            $selected = false;
            if($value["VALUE"]==$arSelect){
                $selected = ' selected ';
            }
            $str.='<option'.$selected.' value="'.$arSelect.'">'.$arSelect.'</option>';
        }
        $str.= '</select>';
        return $str;
    }


    public static function GetUIFilterProperty($arProperty, $strHTMLControlName, &$fields=false){
        $floors = FloorsList();
        $items = array();
        foreach ($floors as $arSelect){
            $items[$arSelect] = $arSelect;
        }
        $fields["type"] = 'list';
        $fields['filterable'] = '';
        $fields['items'] = $items;
    }

}