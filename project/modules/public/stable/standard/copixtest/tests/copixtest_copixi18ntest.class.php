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
class CopixTest_CopixI18NTest extends CopixTest {
	function test_i18n (){
		$this->markTestIncomplete ('Faire fonctionner avec CopixI18N::setLang ("en") avant');
		$this->assertEquals (CopixI18N::get ('copix:common.none'), _i18n('copix:common.none'));
	}
	
	function testExists (){
		$this->assertTrue (CopixI18N::exists ('copix:common.none'));
		$this->assertFalse (CopixI18N::exists ('copix:common.fooooooooooooo'));
		$this->assertFalse (CopixI18N::exists ('fooooooooooooo'));
	}
}
?>