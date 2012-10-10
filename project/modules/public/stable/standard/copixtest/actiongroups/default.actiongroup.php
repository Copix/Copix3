<?php
/**
 * @package standard
 * @subpackage copixtest
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Actions standards pour tester le fonctionnement global de Copix
 * @package standard
 * @subpackage copixtest
 */
 class ActionGroupDefault extends CopixActionGroup {
 	/**
 	 * On retourne sur la page de choix des tests unitaires à lancer
 	 */
 	public function processDefault (){
 		return _arRedirect (_url ('unittest|'));
 	}
 	
 	
 }
?>