<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Brice Favre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Création d'une table pour gérer les onglets
 * 
 * @package		copix
 * @subpackage	core
 */
class CopixTabActionGroup extends CopixActionGroup {

    /**
     * Liste des onglets
     * 
     * @var array
     */
    protected $_listTabs = array ();

    /**
     * Onglet actuel
     * 
     * @var string
     */
    protected $_currentTab = null;

    /**
     * Lien vers le template des onglets
     * 
     * @var string
     */
    protected $_tpl = 'copix:templates/onglets.tpl';

    /**
     * Liste des libellés de méthodes
     * 
     * @var array
     */
    protected $_arLibelle;

    /**
     * Constructeur
     */
    public function __construct () {
        $arObjectMethods = get_class_methods (get_class ($this));
        // Préinitiatilise les actions
        foreach ($arObjectMethods as $method) {
            if (preg_match ("/^process/", $method) && $method != "process") {
                $objTab = new stdClass ();
                $objTab->url = _url (substr ($method, 7));
                $objTab->caption = isset ($this->_arLibelle[$method]) ? $this->_arLibelle[$method] : $method;
                $objTab->enable = 1;
                $this->_listTabs[] = $objTab;
            }
        }
    }

    /**
     * Executée avant le processXXX. Sauvegarde du nom de l'onglet courant.
     *
     * @param string $pActionName Nom de l'action
     * @return mixed
     */
    public function _beforeAction ($pActionName) {
        // récupération de l'onglet courant
        $this->_currentTab = $pActionName;
        return parent::_beforeAction ($pActionName);
    }

    /**
     * Executée après processXXX. En fonction de l'onglet courant, on affiche les données dans le bon template.
     *
     * @param string $pActionName Nom de l'action
     * @param CopixActionReturn $pReturn Retour du processXXX
     */
    public function _afterAction ($pActionName, $pReturn) {
        if ($pReturn->code == CopixActionReturn::PPO) {
            $tpl = new CopixTpl ();
            $tpl->assign ('ppo', $pReturn->data);

            $ppo = new CopixPPO ();
            $ppo->TITLE_PAGE = $this->_TITLE_PAGE;

            $ppo->main = $tpl->fetch ($pReturn->more);

            $ppo->currentTab = $this->_currentTab;
            $ppo->arTabs = $this->_listTabs;
            if (($pReturn = parent::_afterAction ($pActionName, $pReturn)) !== null) {
                return $pReturn;
            }
            return _arPPO ($ppo, $this->_tpl);
        }

        return parent::_afterAction ($pActionName, $pReturn);
    }
}