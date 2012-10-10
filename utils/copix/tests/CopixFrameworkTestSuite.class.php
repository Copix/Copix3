<?php
/**
* @package		copix
* @subpackage	tests
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

require_once (COPIX_PATH.'tests/framework/Copix_ControllerTest.class.php');

/**
* @package copix
* @subpackage tests
*/
class CopixFrameworkTestSuite {
	/**
	 * Récupère la liste des tests du framework
	 *
	 */
	function getSuite (){
		$suite = new PHPUnit_Framework_TestSuite ('CopixFramework');
		$suite->addTestSuite ('Copix_ControllerTest');
		return $suite;
	}
}
?>