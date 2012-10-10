<?php
/**
 * @package		tutorials
 * @subpackage 	crud_copix
 * @author		Julien Salleyron
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Actions sur la table tutorial_crud_copix
 * @package		tutorials
 * @subpackage 	crud_copix
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Affichage de la liste des éléments
	 */
	public function processDefault (){
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = 'Liste des éléments';
		return _arPpo ($ppo, 'crud.list.tpl');
	}


	/**
	 * Formulaire de modification / création
	 */
	public function processEdit (){
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = 'Edition des éléments';
		return _arPpo ($ppo, 'crud.form.tpl');
	}
	
	/**
	 * Formulaire d'exemple
	 */
	public function processForm (){
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = 'Edition des éléments';
		return _arPpo ($ppo, 'crud2.form.tpl');
	}
	
	public function processCrudMore() {
	    $ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = 'Liste des éléments';
		return _arPpo ($ppo, 'crudmore.list.tpl');
	}
	
	public function processEditMore() {
	    $ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = 'Edition des éléments';
		return _arPpo ($ppo, 'crudmore.form.tpl');
	}

}
?>