<?php
/**
 * @package selectcomplex
 * @author  Damien Duboeuf
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions par défaut réalisées par le framework
 * @package selectcomplex
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
		$ppo->options      = array ('1'=>'valeur 1', '2'=>'valeur 2', '3'=>'valeur 3');
		$ppo->alternatives = array ('1'=>'valeur 1', '2'=>'valeur 2', '3'=>'valeur 3');
		return _arPPO ($ppo, 'exemples/main.tpl');
	}
	
}
?>