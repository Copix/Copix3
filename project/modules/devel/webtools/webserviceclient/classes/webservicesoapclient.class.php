<?php

class WebServiceSoapClient extends SoapClient {
    protected $wsdl = null;
    protected $options = null;

    public function __construct ($wsdl, $options = array ()) {
        $this->wsdl = $wsdl;
        $this->options = $options;
        parent::__construct ($wsdl, $options);
    }
    /**
     *
     * @return array[WebServiceFunction]
     */
    public function __getFunctions () {
        $arFunctions = array ();
        foreach (parent::__getFunctions() as $functions) {
            preg_match('%^(.*?)\s(.*?)\((.*?)\)$%', $functions, $matches);
            list (, $functionType, $functionName, $strParams) = $matches;
            $currentFunction = new WebServiceFunction ($functionName, $functionType);
            foreach (explode(',', $strParams) as $param) {
                $functionParam = new WebServiceParam ();
                preg_match('%^\s?(.*?)\s(.*?)$%', $param, $matches);
                list (, $functionParam->type, $functionParam->name) = $matches;
                $currentFunction->addParams($functionParam);
            }
            $arFunctions[] = $currentFunction;
        }
        return $arFunctions;
    }

    public function getFunctionsDeclaration () {
        $toReturn = '';
        foreach ($this->__getFunctions() as $function) {
            $toReturn .= $function->generatePHP ()."\n\n\n";
        }
        return $toReturn;
    }
    public function getTypesDeclaration () {
        $toReturn = "<?php \n\n";
        foreach ($this->__getTypes() as $type) {
            $toReturn .= $type->getComplexTypeDeclaration ();
        }
        return $toReturn."\n?>";
    }
    public function getWebServiceDeclaration ($pName) {
        $generator = new CopixPHPGenerator ();
        $toReturn  = "<?php \n\nclass $pName extends WebServiceSoapClient {\n";
        $toReturn .= $generator->getTabs()."public function __construct () {\n";
        $toReturn .= $generator->getVariableDeclaration('$options', $this->options);
        $toReturn .= $generator->getTabs(2)."parent::__construct ('{$this->wsdl}',\$options);\n";
        $toReturn .= $generator->getTabs()."}\n\n";
        $toReturn .= $this->getFunctionsDeclaration();
        $toReturn .= "}\n\n?>";
        return $toReturn;
    }

    public function __getTypes () {
        $arTypes = array ();
        foreach (parent::__getTypes() as $type) {
            if ($type == null) {
                continue;
            }
            
            preg_match('%struct\s(.*?)\s{\s*(.*?)\s*}%ms', $type, $matches);
            if (count ($matches) != 3) {
                continue;
            }
            list (, $typeName, $strProperty) = $matches;
            $currentType = new WebServiceComplexType ($typeName);
            foreach (explode ("\n", $strProperty) as $property) {
                if ($property == null) {
                    continue;
                }
                preg_match ('%\s*(.*?)\s(.*?);%', $property, $matches);
                $typeProperty = new WebServiceComplexTypeProperty ();
                list (, $typeProperty->type, $typeProperty->name) = $matches;
                $currentType->addProperty($typeProperty);
            }
            $arTypes[] = $currentType;
        }
        return $arTypes;
    }
}