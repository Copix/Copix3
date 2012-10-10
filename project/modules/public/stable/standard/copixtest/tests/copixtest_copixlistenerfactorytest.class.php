<?php
/**
* @package		standard
* @subpackage	copixtest
* @author		Croës Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package		standard
 * @subpackage	copixtest
 */
class CopixTest_CopixListenerFactoryTest extends CopixTest {
	public function testSingleton (){
		$singleton  = CopixListenerFactory::instance ();
		$singleton2 = CopixListenerFactory::instance ();
		$this->assertSame ($singleton, $singleton2);
	} 
}
?>