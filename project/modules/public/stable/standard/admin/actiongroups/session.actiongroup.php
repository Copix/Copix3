<?php
/**
 * @package standard
 * @subpackage admin 
 * 
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Actions sur la session
 * @package standard
 * @subpackage admin 
 */
class ActionGroupSession extends CopixActionGroup {
    /**
	 * Executé avant toute action
	 *
	 * @param string $pActionName Nom de l'action
	 */
	public function beforeAction ($pActionName) {
        CopixAuth::getCurrentUser ()->assertCredential ('basic:registered');
    }

    /**
     * Affichage des données
     */
    function processDefault (){
        return $this->processShow ();
    }

    /**
     * Supression de la session (pour éviter de femer le navigateur, c'est mieux) 
     */
    public function processSessionDestroy (){
        CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
        session_destroy ();
        return _arRedirect (_url (CopixRequest::get ('popup') ? 'admin|session|popup' : 'admin|session|'));
    }

    /**
     * Affichage du contenu de la session dans une popup
     */
    public function processShow (){
        $ppo = $this->_getShowPPO ();
        $ppo->popup = CopixRequest::get ('popup');
        return _arPpo ($this->_getShowPPO (), $ppo->popup ? array ('template'=>'session.show.tpl',
        'mainTemplate'=>'|blank.tpl') : 'session.show.tpl');
    }

    /**
     * Supression d'une variable de session.
     */
    public function processRemove (){
        $namespace = CopixRequest::get ('for_namespace');
        $key = CopixRequest::get ('key');

        if ($namespace !== null){
            if ($key !== null){
                unset ($_SESSION['COPIX'][$namespace][$key]);
            }else{
                unset ($_SESSION['COPIX'][$namespace]);
            }
        } else {
            if (isset ($_SESSION[$key])){
                unset ($_SESSION[$key]);
            }
        }

        return _arRedirect (_url (CopixRequest::get ('popup') ? 'admin|session|popup' : 'admin|session|'));
    }

    /**
     * Création du PPO utilisé pour les fonctions d'affichage des variables de session
     */
    private function _getShowPPO (){
        $ppo = new CopixPPO ();
        $ppo->TITLE_PAGE = _i18n ('session.title');

        // Les informations de session se trouvent désormais dans le tableau de session COPIX
        $ppo->arSessionCopix = isset ($_SESSION['COPIX']) ? $_SESSION['COPIX'] : array ();
        ksort($ppo->arSessionCopix); 
        foreach ($ppo->arSessionCopix as $namespace => $values) {
            uksort($values, array($this, "sortArray"));
            $ppo->arSessionCopix[$namespace] = $values;
        }
        $ppo->arSession = $_SESSION;
        unset ($ppo->arSession['COPIX']);
        return $ppo;
    }
    
    function sortArray ($a, $b) {
         return strcmp (str_replace ("|",".",$a), str_replace ("|",".",$b));
    }
}

?>