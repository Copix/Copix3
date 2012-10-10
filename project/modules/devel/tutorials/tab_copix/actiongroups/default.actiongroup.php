<?php
/**
 * @package		tutorials
 * @subpackage 	tab_copix
 * @author		Brice Favre
 * @copyright	2001-2008 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Actions par défaut de tab_copix
 * @package		tutorials
 * @subpackage 	tab_copix
 */
class ActionGroupDefault extends CopixTabActionGroup {

    /**
     * Action d'accueil
     *
     * @return CopixActionReturn
     */
    public function processDefault (){
        $ppo = new CopixPpo ();
        $ppo->TITLE_PAGE = 'Bienvenue';
        return _arPpo ($ppo, 'default.tpl');
    }

    /**
     * Deuxième étape de nos onglets
     *
     * @return CopixActionReturn
     */
    public function processForm (){
        $ppo = new CopixPpo ();
        $ppo->TITLE_PAGE = 'Formulaire';
        return _arPpo ($ppo, 'default.tpl');
    }

    /**
     * Troisième étape : confirmation des données
     *
     * @return CopixActionReturn
     */
    public function processConfirm (){
        $ppo = new CopixPpo ();
        $ppo->TITLE_PAGE = 'Confirmation des informations';
        return _arPpo ($ppo, 'default.tpl');
    }


    /**
     * Quatrième étape : affichage du résultat
     *
     * @return CopixActionreturn
     */
    public function processResult () {
        $ppo = new CopixPpo ();
        $ppo->TITLE_PAGE = 'Resultat';
        return _arPpo ($ppo, 'default.tpl');
    }
   
}
?>