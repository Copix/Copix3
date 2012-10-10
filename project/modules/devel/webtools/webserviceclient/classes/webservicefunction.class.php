<?php

class WebServiceFunction {

    private $_functionName = null;
    
    private $_functionType = null;

    private $_arParams     = array ();

    public function __construct ($pFunctionName, $pFunctionType) {
        $this->_functionName = $pFunctionName;
        $this->_functionType = $pFunctionType;
    }
   public function addParams (WebServiceParam $pParam) {
        $this->_arParams[] = $pParam;
    }

    public function generatePHP () {
        $generator = new CopixPHPGenerator ();

        $comments = array ();
        $paramList = array ();
        foreach ($this->_arParams as $param) {
            $comments[] = "@param {$param->type} {$param->name}";
            $paramList[] = $param->name;
        }
        $comments[] = "@return {$this->_functionType} {$this->_functionName}";
        $toReturn = $generator->getPHPDoc($comments);
        $toReturn .= "public function {$this->_functionName} (".implode(',', $paramList).") {\n";
        $toReturn .= $generator->getTabs()."return parent::__call ('{$this->_functionName}',array (".implode(',', $paramList)."));\n";
        $toReturn .= "}";
        return $toReturn;

    }
}

class WebServiceParam {
   public $type = null;
   public $name = null;
}