<?php

class validation {
    
    public function testordie ($value) {
        if ($value!='test' && $value!='die') {
            return htmlentities("Faut pas rever, je test !!");
        }
        return array();
    }
    
}

?>