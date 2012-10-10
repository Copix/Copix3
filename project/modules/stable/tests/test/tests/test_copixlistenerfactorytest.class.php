<?php
/**
* @package		standard
* @subpackage	test
* @author		Croës Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package		standard
 * @subpackage	test
 */
class Test_CopixListenerFactoryTest extends CopixTest {
	public function testSingleton (){
		$singleton  = CopixListenerFactory::instance ();
		$singleton2 = CopixListenerFactory::instance ();
		$this->assertSame ($singleton, $singleton2);
	} 
}