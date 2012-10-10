<?php
/**
 * @package		simplehelp
 * @author		Audrey Vassal
 * @copyright	2001-2007 CopixTeam
 * @link		http://copix.org
 * @licence		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Opérations d'administration pour l'aide
 */
class ActionGroupAdmin extends CopixActionGroup {

    /**
     * Fonction appelée avant l'action pour vérifier les droits 
     */
    public function beforeAction ($actionName){
        // verification si l'utilisateur est connecte
        CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
    }

    /**
     * Fonction appellée lorsque l'on veut lister les aide saisies
     */
    function processListAide (){
        $tpl = new CopixTpl ();
        $tpl->assign ('TITLE_PAGE', _i18n ('simplehelp.title.list'));
        $tpl->assign ('MAIN'      , CopixZone::process ('SimpleHelpList'));
        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
    }

    /**
     * Fonction qui est appellée pour afficher l'aide
     */
    function processShowAide (){
        if(_request ('id_sh', null) === null){
            return CopixActionGroup::process ('generictools|Messages::getError',
            array ('message'=>_i18n ('simplehelp.error.missingParameters'),
            'back'=>_url ('simplehelp|admin|listAide')));
        }

        $tpl = new CopixTpl ();
        $aide = _ioDAO ('simplehelp')->get (_request ('id_sh', null));

        $tpl->assign ('TITLE_PAGE', $aide->title_sh);
        $tpl->assign ('MAIN'      , CopixZone::process ('ShowAide', array('id_sh'=>CopixRequest::get ('id_sh', null))));
        return new CopixActionReturn (CopixActionReturn::DISPLAY_IN, $tpl, 'popup.tpl');
    }
     
     
    /**
     * prepare a new simplehelp to edit.
     */
    function processCreate (){
        // Initialisation d'un simplehelp
        $aide = _record ('simplehelp');
        $this->_setSessionSimpleHelp ($aide);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, _url ('simplehelp|admin|edit'));
    }
     
    /**
     * prepare the simplehelp to edit.
     */
    function processPrepareEdit (){

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
        return new CopixActionReturn (CopixActionReturn::REDIRECT, _url ('simplehelp|admin|edit'));
    }

    /**
     * gets the edit page for the simplehelp.
     */
    function processEdit (){
        if (!$toEdit = $this->_getSessionSimpleHelp ()){
            return CopixActionGroup::process ('generictools|Messages::getError',
            array ('message'=>_i18n ('simplehelp.unableToGetEdited'),
            'back'=>_url ('simplehelp|admin|listAide')));
        }

        $tpl = new CopixTpl ();

        $tpl->assign ('TITLE_PAGE', strlen ($toEdit->id_sh) >= 1 ? _i18n ('simplehelp.title.update') : _i18n ('simplehelp.title.create'));
        $tpl->assign ('MAIN', CopixZone::process ('SimpleHelpEdit', array ('toEdit'=>$toEdit, 'e'=>(CopixRequest::get('e',null)!== null))));
        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
    }

    /**
     * apply updates on the edited simplehelp.
     * save to datebase if ok.
     */
    function processValid (){
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
     * Cancel the edition...... empty the session data
     */
    function processCancelEdit (){
        $simpleHelp = $this->_getSessionSimpleHelp();
        $id_sh      = $simpleHelp->id_sh;
        $this->_setSessionSimpleHelp(null);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, _url ('simplehelp|admin|listAide'));
    }
     

    function processDelete() {
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
        return new CopixActionReturn (CopixActionReturn::REDIRECT, _url ('simplehelp|admin|listAide'));
    }
     
    /**
     * updates informations on a single simplehelp object from the vars.
     * le formulaire.
     * @access: private.
     */
    private function _validFromForm (& $toUpdate){
        $toCheck = array ('title_sh', 'contenu_sh', 'page_sh', 'key_sh');
        CopixRequest::assert('title_sh', 'contenu_sh', 'page_sh', 'key_sh');

        foreach ($toCheck as $elem){
            $toUpdate->$elem = _request($elem);
        }
    }
     
    /**
     * sets the current edited simplehelp.
     * @access: private.
     */
    private function _setSessionSimpleHelp ($toSet){
        $_SESSION['MODULE_AIDESIMPLE_EDITED_AIDE'] = $toSet !== null ? serialize($toSet) : null;
    }
     
    /**
     * gets the current edited simplehelp.
     * @access: private.
     */
    private function _getSessionSimpleHelp () {
        _daoInclude ('simplehelp');
        return isset ($_SESSION['MODULE_AIDESIMPLE_EDITED_AIDE']) ? unserialize ($_SESSION['MODULE_AIDESIMPLE_EDITED_AIDE']) : null;
    }
}
?>