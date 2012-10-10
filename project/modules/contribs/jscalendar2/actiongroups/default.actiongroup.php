<?php
/**
 * @package jscalendar2
 * @author  Damien Duboeuf
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions par défaut réalisées par le framework
 * @package jscalendar2
 */
class ActionGroupDefault extends CopixActionController {
	
	/**
	* Par défaut, on redirige vers l'url de la page principal du CMS
	*/
	protected function processDefault () {
		
		return $this->processExemples ();
	}
	
	protected function processExemples () {
		$ppo = new CopixPPO ();
		return _arPPO ($ppo, 'exemples/main.php');
	}
	
}
?>