<?php
/**
 * @package		tutorials
 * @subpackage	event_launch
 * @author		Estelle Fersing
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */


/**
 * Pages par défaut pour le module 
 * @package		tutorials
 * @subpackage	event_launch
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Formulaire pour lancer un évènement avec paramètre ou un évènement sans paramètre
	 */
	public function processDefault (){
		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>"Exemple d'évènements")), 'even_launch.form.tpl');
	}
	
	/**
	 * Création d'un nouvel évènement sans paramètre
	 */
	public function processNewEventOnly (){
		//Sans aucun paramètre
		_notify ('newEventOnly');
		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>"Exemple d'évènements")), 'even_launch.form.tpl');
	}
	
	/**
	 * Création d'un nouvel évènement avec paramètre
	 */
	public function processNewEvent (){
		//Avec un paramètre
		_notify ('newEvent', array ('information'=>_request('information', "rien n'a été mis")));
		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>"Exemple d'évènements")), 'even_launch.form.tpl');
	}
	
}
?>