<?php
/**
 * @package standard
 * @subpackage test
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Tests des différentes possibilités de création de chaine avec CopixI18N
 * @package standard
 * @subpackage test
 */
class Test_CopixI18N extends CopixTest {
	public function setUp (){
		static $deleted = false;
		if ($deleted === false){
        	CopixFile::removeFileFromPath (COPIX_CACHE_PATH . 'php/resources/', false);
        	$deleted = true;
		}
		CopixContext::push ('test');
	}
	
	public function tearDown (){
		CopixContext::pop ();
	}
	
	public function testLangAndCountries (){
		CopixI18N::setLang ('fr');
		CopixI18N::setCountry ('FR');

		$this->assertEquals (_i18n ('i18n.default'), 'default');
		
		$this->assertEquals (_i18n ('i18n.key'), 'fr_FR');
		$this->assertEquals (_i18n ('i18n.lang'), 'fr');
		
		$this->assertEquals (_i18n ('i18n.lang', null, 'en_NOTEXISTS'), 'en');		
		$this->assertEquals (_i18n ('i18n.lang', null, 'en_US'), 'en');		
		$this->assertEquals (_i18n ('i18n.key', null, 'en_US'), 'en_US');		
		$this->assertEquals (_i18n ('i18n.key', null, 'en_EN'), 'en_EN');		
	}
}