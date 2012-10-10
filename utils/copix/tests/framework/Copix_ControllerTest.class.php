<?php
/**
* @package		copix
* @subpackage	tests
* @author		Croës Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package copix
 * @subpackage tests
 */
class Copix_ControllerTest extends Copixtest {
	/**
	 * Test de création d'un controller
	 */
	function testCreate (){
	   $project = new ProjectController ('../project/config/copix.conf.php');
	   $this->assertSame ($project, CopixController::instance ());
	}
}
?>