<?php

class WSClassGenerator {
    public function generate_class ($pForce = false) {
        foreach (_ioDao ('webserviceclient')->findAll () as $ws) {
            $file = COPIX_TEMP_PATH.'/webserviceclient/'.strtolower($ws->name).'.php';
            if (!file_exists($file) || $pForce) {
                $soap = new WebServiceSoapClient ($ws->wsdl, unserialize ($ws->options));
                CopixFile::write ($file, $soap->getWebServiceDeclaration($ws->name).$soap->getTypesDeclaration());
            }
        }
    }

    public function autoload ($pClassName) {
        $file = COPIX_TEMP_PATH.'/webserviceclient/'.strtolower($pClassName).'.php';
        if (!file_exists($file)) {
            $this->generate_class();
        }
        if (!file_exists($file)) {
            return false;
        }
        require ($file);
    }
}