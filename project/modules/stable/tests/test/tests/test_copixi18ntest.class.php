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
class Test_CopixI18NTest extends CopixTest {
	protected  $_lang = null;
	protected  $_country = null;

	function setUp (){
		$this->_lang = CopixI18N::getLang ();
		$this->_country = CopixI18N::getCountry ();
	}
	
	function tearDown (){
		CopixI18N::setLang ($this->_lang);
		CopixI18N::setCountry ($this->_country);
	}

	function test_i18n (){
		CopixI18N::setLang ('fr');
		CopixI18N::setCountry ('FR');		
		$this->assertEquals (CopixI18N::get ('copix:common.none'), _i18n('copix:common.none'));
		CopixI18N::setLang ('en');
		CopixI18N::setCountry ('EN');		
		$this->assertEquals (CopixI18N::get ('copix:common.none'), _i18n('copix:common.none'));		
	}
	
	function testExists (){
		CopixI18N::setLang ('fr');
		CopixI18N::setCountry ('FR');		
		$this->assertTrue (CopixI18N::exists ('copix:common.none'));
		$this->assertFalse (CopixI18N::exists ('copix:common.fooooooooooooo'));
		$this->assertFalse (CopixI18N::exists ('fooooooooooooo'));
		CopixI18N::setLang ('en');
		CopixI18N::setCountry ('EN');		
		$this->assertTrue (CopixI18N::exists ('copix:common.none'));
		$this->assertFalse (CopixI18N::exists ('copix:common.fooooooooooooo'));
		$this->assertFalse (CopixI18N::exists ('fooooooooooooo'));		
	}
}