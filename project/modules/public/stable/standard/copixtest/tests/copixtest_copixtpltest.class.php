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
 * Class de test unitaire des tpls
 * @package		standard
 * @subpackage	copixtest
 */
class CopixTest_CopixTplTest extends CopixTest {

	private $themeDir;
	
	private $defaultDir;
	
	private $moduleDir;
	
	public function setUp () {
		$config = CopixConfig::Instance ();
		
		if(method_exists($config, 'copixtpl_clearPaths')) {
			$config->copixtpl_clearPaths();
			$config->copixtpl_addPath(COPIX_PROJECT_PATH.'themes/');
			CopixTpl::clearFilePathCache();
		}
		
		$config->i18n_path_enabled = true;
		
		CopixTpl::setTheme ('testtheme');
		CopixI18N::setLang('fr');
		CopixI18N::setCountry('FR');		
		
		$this->themeDir = COPIX_PROJECT_PATH.'themes/testtheme/';
		$this->defaultDir = COPIX_PROJECT_PATH.'themes/default/';
		$this->moduleDir = CopixModule::getBasePath('copixtest').'/copixtest/'.COPIX_TEMPLATES_DIR;
		
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
		
		$this->assertPathEquals($this->themeDir.'copixtest/testtheme.tpl', $tpl->getFilePath('copixtest|testtheme.tpl'));
		$this->assertPathEquals($this->themeDir.'copixtest/fr/testthemefr.tpl', $tpl->getFilePath('copixtest|testthemefr.tpl'));
		$this->assertPathEquals($this->themeDir.'copixtest/fr_FR/testthemefrfr.tpl', $tpl->getFilePath('copixtest|testthemefrfr.tpl'));

		$this->assertPathEquals($this->defaultDir.'copixtest/testdefault.tpl', $tpl->getFilePath('copixtest|testdefault.tpl'));
		$this->assertPathEquals($this->defaultDir.'copixtest/fr/testdefaultfr.tpl', $tpl->getFilePath('copixtest|testdefaultfr.tpl'));
		$this->assertPathEquals($this->defaultDir.'copixtest/fr_FR/testdefaultfrfr.tpl', $tpl->getFilePath('copixtest|testdefaultfrfr.tpl'));

		$this->assertPathEquals($this->moduleDir.'testmodule.tpl', $tpl->getFilePath('copixtest|testmodule.tpl'));
		$this->assertPathEquals($this->moduleDir.'fr/testmodulefr.tpl', $tpl->getFilePath('copixtest|testmodulefr.tpl'));
		$this->assertPathEquals($this->moduleDir.'fr_FR/testmodulefrfr.tpl', $tpl->getFilePath('copixtest|testmodulefrfr.tpl'));
		
		$this->assertPathEquals($this->themeDir.'copixtest/testmoduleoverload.tpl', $tpl->getFilePath('copixtest|testmoduleoverload.tpl'));
		
	}
		
	/**
	 * Teste CopixTpl->getFilePath() sans l'internationalisation activée.
	 *
	 */
	public function testGetFilePathWithoutI18N (){
		
		$tpl = new CopixTpl();
		
		CopixConfig::instance()->i18n_path_enabled = false;
		
		$this->assertPathEquals($this->themeDir.'copixtest/testtheme.tpl', $tpl->getFilePath('copixtest|testtheme.tpl'));
		$this->assertFalse($tpl->getFilePath('copixtest|testthemefr.tpl')); // N'existe pas
		$this->assertPathEquals($this->themeDir.'copixtest/testthemefrfr.tpl', $tpl->getFilePath('copixtest|testthemefrfr.tpl'));

		$this->assertPathEquals($this->defaultDir.'copixtest/testdefault.tpl', $tpl->getFilePath('copixtest|testdefault.tpl'));
		$this->assertFalse($tpl->getFilePath('copixtest|testdefaultfr.tpl')); // N'existe pas
		$this->assertPathEquals($this->defaultDir.'copixtest/testdefaultfrfr.tpl', $tpl->getFilePath('copixtest|testdefaultfrfr.tpl'));

		$this->assertPathEquals($this->moduleDir.'testmodule.tpl', $tpl->getFilePath('copixtest|testmodule.tpl'));
		$this->assertFalse($tpl->getFilePath('copixtest|testmodulefr.tpl')); // N'existe pas
		$this->assertFalse($tpl->getFilePath('copixtest|testmodulefrfr.tpl')); // N'existe pas
		
		$this->assertPathEquals($this->themeDir.'copixtest/testmoduleoverload.tpl', $tpl->getFilePath('copixtest|testmoduleoverload.tpl'));
	}
	
	/**
	 * Teste CopixTpl->getFilePath() avec un thème inexistant.
	 *
	 */	
	public function testGetFilePathUnknownTheme (){
		
		$tpl = new CopixTpl();
		
		CopixTpl::setTheme ('themenonexistant');

		$this->assertFalse($tpl->getFilePath('copixtest|testtheme.tpl')); // N'existe pas
		$this->assertFalse($tpl->getFilePath('copixtest|testthemefr.tpl')); // N'existe pas
		$this->assertFalse($tpl->getFilePath('copixtest|testthemefrfr.tpl')); // N'existe pas
		
		$this->assertPathEquals($this->defaultDir.'copixtest/testdefault.tpl', $tpl->getFilePath('copixtest|testdefault.tpl'));
		$this->assertPathEquals($this->defaultDir.'copixtest/fr/testdefaultfr.tpl', $tpl->getFilePath('copixtest|testdefaultfr.tpl'));
		$this->assertPathEquals($this->defaultDir.'copixtest/fr_FR/testdefaultfrfr.tpl', $tpl->getFilePath('copixtest|testdefaultfrfr.tpl'));
		
		$this->assertPathEquals($this->moduleDir.'testmodule.tpl', $tpl->getFilePath('copixtest|testmodule.tpl'));
		$this->assertPathEquals($this->moduleDir.'fr/testmodulefr.tpl', $tpl->getFilePath('copixtest|testmodulefr.tpl'));
		$this->assertPathEquals($this->moduleDir.'fr_FR/testmodulefrfr.tpl', $tpl->getFilePath('copixtest|testmodulefrfr.tpl'));

		$this->assertPathEquals($this->defaultDir.'copixtest/testmoduleoverload.tpl', $tpl->getFilePath('copixtest|testmoduleoverload.tpl'));
		
	}	

	/**
	 * Fonction qui test si les templates dynamiques sont bien remontés
	 */
	function testDynTemplates (){
	   $arDyn = array();
	   $arDyn = CopixTpl::find('copixtest','*.dyn.*');
       $this->assertTrue(in_array('copixtest|copixtestdyn.dyn.tpl',$arDyn) && in_array('copixtest|test.dyn.tpl',$arDyn));
	}
}
?>