<?php
class ActionGroupDefault extends CopixActionGroup {
    public function beforeAction ($pAction) {
        //en attendant l'autoload
        require (CopixModule::getPath('webserviceclient').'/classes/webservicefunction.class.php');
        require (CopixModule::getPath('webserviceclient').'/classes/webservicesoapclient.class.php');
        require (CopixModule::getPath('webserviceclient').'/classes/webservicecomplextype.class.php');
    }

    public function processDefault () {
        $ppo = new CopixPPO ();
        $ppo->TITLE_PAGE = 'Découverte de WSDL';
        return _arPpo ($ppo, 'wsdldiscover.tpl');
    }

    public function processTest () {
        $options = array();
        if (_request('options')!= null) {
            $options['login'] = _request ('login');
            $options['password'] = _request ('password');
        }
        
        if (_request('name') != null) {
            $record = _record ('webserviceclient');
            $record->wsdl = _request ('wsdl');
            $record->name = _request ('name');
            $record->options = serialize ($options);
            _ioDao('webserviceclient')->insert ($record);
            $ws_class_generator = _ioClass ('webserviceclient|wsclassgenerator');
            $ws_class_generator->generate_class (true);
        }
        //COPIX_TEMP_PATH.'testws.php', $soap->getWebServiceDeclaration('test').$soap->getTypesDeclaration();
        return _arNone();
    }

}

