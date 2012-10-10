<?php
/**
 * @package		simplehelp
 * @author		Audrey Vassal, Brice Favre
 * @copyright	2001-2008 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		tools 
 * @subpackage	simplehelp 
 */
class ActionGroupAdmin extends CopixActionGroup {

    /**
     * Fonction appelée avant l'action pour vérifier les droits
     * 
     * @param string nom de l'action 
     */
    public function beforeAction ($actionName){
        // verification si l'utilisateur est connecte
        CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
    }
    
    /**
     * Action par défault : liste des aides
     * 
     */
	public function processDefault (){
		return $this->processListaide ();
	}

    /**
     * Fonction appellée lorsque l'on veut lister les aide saisies
     * 
     */
    public function processListAide (){
    	$ppo = new CopixPPO (array ('TITLE_PAGE' => _i18n ('simplehelp.title.list')));
    	// Récupération des élements
    	$ppo->arAides = _ioDao ('simplehelp')->findAll ();
		
    	return _arPpo ($ppo, 'simplehelp|simplehelp.list.tpl');
    }

    /**
     * Fonction qui est appellée pour afficher l'aide
     * 
     */
    public function processShowAide (){
        if(_request ('id_sh', null) === null){
            return CopixActionGroup::process ('generictools|Messages::getError',
            array ('message'=>_i18n ('simplehelp.error.missingParameters'),
            'back'=>_url ('simplehelp|admin|listAide')));
        }

        $ppo = new CopixPpo ();
        $aide = _ioDAO ('simplehelp')->get (_request ('id_sh', null));
        
        $ppo->TITLE_PAGE = $aide->title_sh;
        $ppo->MAIN = CopixZone::process ('ShowAide', array('id_sh'=>CopixRequest::get ('id_sh', null)));
        return _arDirectPPO ($ppo, 'popup.tpl');
    }
     
     
    /**
     * Prépare la création d'une aide simple
     * 
     */
   	public function processCreate (){
        // Initialisation d'un simplehelp
        $aide = _record ('simplehelp');
        $this->_setSessionSimpleHelp ($aide);
        return _arRedirect (_url ('simplehelp|admin|edit'));
    }
     
    /**
     * Préparation de l'édition d'une aide simple
     * 
     */
    public function processPrepareEdit (){
        if ((CopixRequest::get('id_sh',null) === null )){
            return CopixActionGroup::process ('generictools|Messages::getError',
            array ('message'=>_i18n ('simplehelp.error.missingParameters'),
            'back'=>_url ('simplehelp|admin|listAide')));
        }

        $dao = _ioDao ('simplehelp');
        if (!$toEdit = $dao->get (CopixRequest::get('id_sh'))){
            return CopixActionGroup::process ('generictools|Messages::getError',
            array ('message'=>_i18n ('simplehelp.unableToFind'),
            'back'=>_url ('simplehelp|admin|listAide')));
        }

        $this->_setSessionSimpleHelp ($toEdit);
        return _arRedirect (_url ('simplehelp|admin|edit'));
    }

    /**
     * Affichage de la page d'édition 
     * 
     */
    public function processEdit (){
		if (!$toEdit = $this->_getSessionSimpleHelp ()){
            return CopixActionGroup::process ('generictools|Messages::getError',
                                              array ('message'=>_i18n ('simplehelp.unableToGetEdited'),
                                              'back'=>_url ('simplehelp|admin|listAide')));
        }
        // Création de PPO
		$ppo = new CopixPPO (array ('TITLE_PAGE'=> strlen ($toEdit->id_sh) >= 1 ? _i18n ('simplehelp.title.update') : _i18n ('simplehelp.title.create')));
		$ppo->toEdit = $toEdit;
        $ppo->showErrors = _request ('e', null) !== null ? true : false;
		$ppo->errors = _ioDao ('simplehelp')->check($ppo->toEdit);
		return _arPPO ($ppo, 'simplehelp|simplehelp.edit.tpl');
    }

    /**
     * Mise à jour de l'aide simple.
     * Enregistre en base de donnée si tout va bien.
     * 
     */
    public function processValid (){
        if (!$toValid = $this->_getSessionSimpleHelp ()){
            return CopixActionGroup::process ('generictools|Messages::getError',
            array ('message'=>_i18n ('simplehelp.unableToGetEdited'),
            'back'=>_url ('simplehelp|admin|listAide')));
        }

        $this->_validFromForm($toValid);
         
        $dao = _ioDao ('simplehelp');
        if ($dao->check($toValid) !== true) {
            $this->_setSessionSimpleHelp($toValid);
            return new CopixActionReturn (CopixActionReturn::REDIRECT, _url ('simplehelp|admin|edit', array('e'=>'1')));
        }

        if ($toValid->id_sh !== null){
            $dao->update ($toValid);
        }else{
            $dao->insert ($toValid);
        }
        //on vide la session
        $this->_setSessionSimpleHelp(null);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, _url ('simplehelp|admin|listAide'));
    }
     
    /**
     * Annule l'édition et efface la session
     * 
     */
    public function processCancelEdit (){
        $simpleHelp = $this->_getSessionSimpleHelp();
        $id_sh      = $simpleHelp->id_sh;
        $this->_setSessionSimpleHelp(null);
        return _arRedirect (_url ('simplehelp|admin|listAide'));
    }
     

    /**
     * Effacer une aide simple
     * 
     */
    public function processDelete() {
        if ((CopixRequest::get('id_sh',null) === null )){
            return CopixActionGroup::process ('generictools|Messages::getError',
            array ('message'=>_i18n ('simplehelp.error.missingParameters'),
            'back'=>_url ('simplehelp|admin|listAide')));
        }

        $dao = _ioDao ('simplehelp');
        if (!$toDelete = $dao->get (CopixRequest::get('id_sh'))){
            return CopixActionGroup::process ('generictools|Messages::getError',
            array ('message'=>_i18n ('simplehelp.unableToFind'),
            'back'=>_url ('simplehelp|admin|listAide')));
        }

         
        //Confirmation screen ?
        if ((CopixRequest::get('confirm',null) === null )){
            return CopixActionGroup::process ('generictools|Messages::getConfirm',
            array ('title'=>_i18n ('simplehelp.title.confirmdelevent'),
            'message'=>_i18n ('simplehelp.message.confirmdelevent'),
            'confirm'=>_url('simplehelp|admin|delete', array('id_sh'=>$toDelete->id_sh, 'confirm'=>'1')),
            'cancel'=>_url('simplehelp|admin|listAide')));
        }

        //Delete aide
        $dao->delete($toDelete->id_sh);
        return _arRedirect (_url ('simplehelp|admin|listAide'));
    }
     
    /**
     * Mise à jour du formulaire d'aide simple
     * @access: private.
     * @todo Utiliser les validateurs
     */
    private function _validFromForm (& $toUpdate){
        $toCheck = array ('title_sh', 'content_sh', 'page_sh', 'key_sh');
        CopixRequest::assert('title_sh', 'content_sh', 'page_sh', 'key_sh');

        foreach ($toCheck as $elem){
            $toUpdate->$elem = _request($elem);
        }
    }
     
    /**
     * Mise en session de l'aide simpe édité
     * @access: private.
     */
    private function _setSessionSimpleHelp ($toSet){
    	CopixSession::set ('edithelp_object', serialize($toSet), 'simplehelp');
    }
     
    /**
     * Récupération en session de l'aide simple en édition
     * @access: private.
     */
    private function _getSessionSimpleHelp () {
        _daoInclude ('simplehelp');
        $oEdit = CopixSession::get ('edithelp_object', 'simplehelp');
        return isset ($oEdit) ? unserialize ($oEdit) : null;
    }
}