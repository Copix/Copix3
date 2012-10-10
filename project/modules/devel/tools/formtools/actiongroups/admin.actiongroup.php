<?php
/**
 * @package		tooks
 * @subpackage 	formtools
 * @author		Brice Favre
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * @package		tools
 * @subpackage 	formtools
 */
class ActionGroupAdmin extends CopixActionGroup {
	
	/**
	 * Administration des règles de routage
	 *
	 */
	public function processAdminRoute (){
		_classInclude ('formtools|formulaire');
		CopixRequest::assert ('form_id');
		
		$form_record = _ioDao ('formobject')->get (_request ('form_id'));
		$route_record = _ioDao ('routeobject')->get ($form_record->route_id);
		
		$form = unserialize ($form_record->form);
		$route = unserialize ($route_record->route);
		
		$ppo = _ppo ();
		$ppo->fieldList = $form->getFieldList ();
		$ppo->arConditions = array ('BEGINWITH'=>'commence par', 'EQUALS'=>'égal à');
		$ppo->rules = $route->getRules ();
		
		return _arPpo ($ppo, 'admin.adminroute.template.php');	
	}
	
	/**
	 * 
	 */
	public function processDoSaveRoute (){
		
		
	}
}