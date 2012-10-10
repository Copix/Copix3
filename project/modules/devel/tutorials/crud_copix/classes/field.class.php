<?php

class field {
    
    public function captionHTML ($pField) {
        $toReturn = '';
        $arValue = explode('|', $pField->value);
        $pField->get('NOM')->value = $arValue[0];
        $pField->get('PRENOM')->value = isset ($arValue[1]) ? $arValue[1] : '';
        $toReturn .= '<input type="text" name="'.$pField->get('NOM')->name.'" value="'.$pField->get('NOM')->value.'" /><input type="text" name="'.$pField->get('PRENOM')->name.'" value="'.$pField->get('PRENOM')->value.'" />';
        return $toReturn;
    }
    
    public function captionValue ($pField, $pRecord) {
        $field = $pField->field;
        return ($pRecord->$field = ($pField->get('NOM')->value!=null && $pField->get('PRENOM')->value!=null) ? $pField->get('NOM')->value.'|'.$pField->get('PRENOM')->value : null);
    }
    
    public function validDescription ($pField) {
        if ($pField->value == 'ATTENTION') {
            return 'Ca ne va pas';
        }
        return null;
    }
}

?>