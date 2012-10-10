<?php

class WebServiceComplexType {

    private $_typeName = null;

    private $_properties = array ();

    public function __construct ($pTypeName) {
        $this->_typeName = $pTypeName;
    }

    public function addProperty (WebServiceComplexTypeProperty $pProperty) {
        $this->_properties[] = $pProperty;
    }

    public function getComplexTypeDeclaration () {
        $generator = new CopixPHPGenerator ();
        $toReturn = "class {$this->_typeName} {\n";
        foreach ($this->_properties as $property) {
            $toReturn .= $generator->getPHPDoc(array ("@var {$property->type} {$property->name}"), 1);
            $toReturn .= "\tpublic \${$property->name};\n\n";
        }
        $toReturn .= "}\n";
        return $toReturn;

    }
}

class WebServiceComplexTypeProperty {
    public $type = null;
    public $name = null;
}