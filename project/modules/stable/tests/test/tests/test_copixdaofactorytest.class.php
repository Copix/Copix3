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
class Test_CopixDAOFactoryTest extends CopixTest {
	public function testFoo (){
		$dao1 = _ioDAO ('copix:copixmodule');
		$dao2 = _ioDAO ('copix:copixmodule');
		$dao3 = _dao ('copix:copixmodule');
		
		$this->assertSame ($dao1, $dao2);
		$this->assertNotSame ($dao1, $dao3);
		
		_daoInclude ('copix:copixmodule');
	}
}