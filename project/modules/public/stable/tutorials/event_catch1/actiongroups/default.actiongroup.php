<?php
/**
 * @package		tutorials
 * @subpackage	event_catch1
 * @author		Estelle Fersing
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */


/**
 * Pages par défaut pour le module 
 * @package		tutorials
 * @subpackage	event_catch1
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Formulaire pour afficher la liste des évènements sans paramètre capturés
	 */
	public function processDefault (){
		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>"Liste des évènements capturés", 'arrEvent'=>_ioDAO ('tutorial_event1')->findAll () )), 'event_catch1.list.tpl');
	}
}
?>