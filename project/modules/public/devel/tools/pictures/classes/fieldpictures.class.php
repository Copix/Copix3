<?php
    class FieldPictures {
        public function picturesHTML ($pField, $pMode) {
            if ($pMode == 'view') {
                $toReturn = '<img src="'.CopixUrl::get('pictures||getimage',array('picture_id'=>$pField->value)).'" />';
            } else {
                $toReturn  = '<input name="'.$pField->name.'" type="hidden" value="'.$pField->value.'" />';
                $toReturn .= '<input type="file" name="'.$pField->name.'_data" />';
            }
            return $toReturn;
        }
        
        public function picturesValue ($pField) {
            $value = '';
            if (($file = CopixRequest::getFile($pField->name.'_data'))!==false) {
                $value = _class('pictures|pictures')->addUploadedImage ($file);
            } else {
                $value = _request($pField->name);
            }
            return $value;
        }
    }
?>