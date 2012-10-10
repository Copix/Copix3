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
 * Class de test unitaire des tpls
 * @package		standard
 * @subpackage	test
 */
class Test_CopixTplTest extends CopixTest {

	private $themeDir;
	
	private $defaultDir;
	
	private $moduleDir;
	
	public function setUp () {
		$config = CopixConfig::Instance ();
		$this->themeDir = CopixTpl::getThemePath ('testtheme');
		$this->defaultDir = CopixTpl::getThemePath ('default/');
		
		if(method_exists($config, 'copixtpl_clearPaths')) {
			$config->copixtpl_clearPaths();
			$config->copixtpl_addPath ($this->themeDir);
			$config->copixtpl_addPath ($this->defaultDir);
			CopixTpl::clearFilePathCache();
		}
		
		$config->i18n_path_enabled = true;
		
		CopixTpl::setTheme ('testtheme');
		CopixI18N::setLang('fr');
		CopixI18N::setCountry('FR');
		
		$this->moduleDir = CopixModule::getBasePath('test').'/test/'.COPIX_TEMPLATES_DIR;
	}
	
	private function assertPathEquals($expected, $actual, $message = null) {
		$this->assertEquals(
			empty($expected) ? $expected : CopixConfig::getRealPath($expected),
			empty($actual) ? $actual : CopixConfig::getRealPath($actual),
			$message
		);
	}
	
	/**
	 * Teste CopixTpl->getFilePath() avec l'internationalisation activée.
	 *
	 */
	public function testGetFilePathWithI18N (){
		
		$tpl = new CopixTpl();
		
		$this->assertPathEquals($this->themeDir.'test/testtheme.tpl', $tpl->getFilePath('test|testtheme.tpl'));
		$this->assertPathEquals($this->themeDir.'test/fr/testthemefr.tpl', $tpl->getFilePath('test|testthemefr.tpl'));
		$this->assertPathEquals($this->themeDir.'test/fr_FR/testthemefrfr.tpl', $tpl->getFilePath('test|testthemefrfr.tpl'));

		$this->assertPathEquals($this->defaultDir.'test/testdefault.tpl', $tpl->getFilePath('test|testdefault.tpl'));
		$this->assertPathEquals($this->defaultDir.'test/fr/testdefaultfr.tpl', $tpl->getFilePath('test|testdefaultfr.tpl'));
		$this->assertPathEquals($this->defaultDir.'test/fr_FR/testdefaultfrfr.tpl', $tpl->getFilePath('test|testdefaultfrfr.tpl'));

		$this->assertPathEquals($this->moduleDir.'testmodule.tpl', $tpl->getFilePath('test|testmodule.tpl'));
		$this->assertPathEquals($this->moduleDir.'fr/testmodulefr.tpl', $tpl->getFilePath('test|testmodulefr.tpl'));
		$this->assertPathEquals($this->moduleDir.'fr_FR/testmodulefrfr.tpl', $tpl->getFilePath('test|testmodulefrfr.tpl'));
		
		$this->assertPathEquals($this->themeDir.'test/testmoduleoverload.tpl', $tpl->getFilePath('test|testmoduleoverload.tpl'));
		
	}
		
	/**
	 * Teste CopixTpl->getFilePath() sans l'internationalisation activée.
	 *
	 */
	public function testGetFilePathWithoutI18N (){
		
		$tpl = new CopixTpl();
		
		CopixConfig::instance()->i18n_path_enabled = false;
		
		$this->assertPathEquals($this->themeDir.'test/testtheme.tpl', $tpl->getFilePath('test|testtheme.tpl'));
		$this->assertFalse($tpl->getFilePath('test|testthemefr.tpl')); // N'existe pas
		$this->assertPathEquals($this->themeDir.'test/testthemefrfr.tpl', $tpl->getFilePath('test|testthemefrfr.tpl'));

		$this->assertPathEquals($this->defaultDir.'test/testdefault.tpl', $tpl->getFilePath('test|testdefault.tpl'));
		$this->assertFalse($tpl->getFilePath('test|testdefaultfr.tpl')); // N'existe pas
		$this->assertPathEquals($this->defaultDir.'test/testdefaultfrfr.tpl', $tpl->getFilePath('test|testdefaultfrfr.tpl'));

		$this->assertPathEquals($this->moduleDir.'testmodule.tpl', $tpl->getFilePath('test|testmodule.tpl'));
		$this->assertFalse($tpl->getFilePath('test|testmodulefr.tpl')); // N'existe pas
		$this->assertFalse($tpl->getFilePath('test|testmodulefrfr.tpl')); // N'existe pas
		
		$this->assertPathEquals($this->themeDir.'test/testmoduleoverload.tpl', $tpl->getFilePath('test|testmoduleoverload.tpl'));
	}
	
	/**
	 * Teste CopixTpl->getFilePath() avec un thème inexistant.
	 *
	 */	
	public function testGetFilePathUnknownTheme (){
		
		$tpl = new CopixTpl();
		
		CopixTpl::setTheme ('themenonexistant');

		$this->assertFalse($tpl->getFilePath('test|testtheme.tpl')); // N'existe pas
		$this->assertFalse($tpl->getFilePath('test|testthemefr.tpl')); // N'existe pas
		$this->assertFalse($tpl->getFilePath('test|testthemefrfr.tpl')); // N'existe pas
		
		$this->assertPathEquals($this->defaultDir.'test/testdefault.tpl', $tpl->getFilePath('test|testdefault.tpl'));
		$this->assertPathEquals($this->defaultDir.'test/fr/testdefaultfr.tpl', $tpl->getFilePath('test|testdefaultfr.tpl'));
		$this->assertPathEquals($this->defaultDir.'test/fr_FR/testdefaultfrfr.tpl', $tpl->getFilePath('test|testdefaultfrfr.tpl'));
		
		$this->assertPathEquals($this->moduleDir.'testmodule.tpl', $tpl->getFilePath('test|testmodule.tpl'));
		$this->assertPathEquals($this->moduleDir.'fr/testmodulefr.tpl', $tpl->getFilePath('test|testmodulefr.tpl'));
		$this->assertPathEquals($this->moduleDir.'fr_FR/testmodulefrfr.tpl', $tpl->getFilePath('test|testmodulefrfr.tpl'));

		$this->assertPathEquals($this->defaultDir.'test/testmoduleoverload.tpl', $tpl->getFilePath('test|testmoduleoverload.tpl'));
		
	}	

	/**
	 * Fonction qui test si les templates dynamiques sont bien remontés
	 */
	function testDynTemplates (){
	   $arDyn = array();
	   $arDyn = CopixTpl::find('test','*.dyn.*');
       $this->assertTrue(in_array('test|testdyn.dyn.tpl',$arDyn) && in_array('test|test.dyn.tpl',$arDyn));
	}
}