<?php
/**
 * @package standard
 * @subpackage test
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package standard
 * @subpackage test
 * Class de test unitaire pour les ressources
 */
class test_copixurlresourcetest extends CopixTest {

	private $saveConfigi18n;

	function setUp (){
		CopixContext::push ('test');

		$this->saveConfigi18n = CopixConfig::Instance()->i18n_path_enabled;

		CopixConfig::Instance()->i18n_path_enabled=true;
		CopixTpl::setTheme ('testtheme');
		CopixI18N::setLang('fr');
		CopixI18N::setCountry('FR');
	}
	function tearDown (){
		CopixConfig::Instance()->i18n_path_enabled = $this->saveConfigi18n;

		CopixContext::pop ();
	}

	/**
	 * Fonction qui test la localistation des fichiers template par rapports aux thèmes langues et pays
	 */
	function testThemeI18N (){

		$prefix = CopixUrl::get();

		$this->assertEquals($prefix.'themes/testtheme/img/fr_FR/testthemefrFR.jpg',CopixUrl::getResource('img/testthemefrFR.jpg'));
		$this->assertEquals($prefix.'themes/testtheme/img/fr/testthemefr.jpg',CopixUrl::getResource('img/testthemefr.jpg'));
		$this->assertEquals($prefix.'themes/testtheme/img/testtheme.jpg',CopixUrl::getResource('img/testtheme.jpg'));

		CopixTpl::setTheme ('themenonexistant');
		$this->assertEquals($prefix.'themes/default/img/fr_FR/testdefaultfrFR.jpg',CopixUrl::getResource('img/testdefaultfrFR.jpg'));
		$this->assertEquals($prefix.'themes/default/img/fr/testdefaultfr.jpg',CopixUrl::getResource('img/testdefaultfr.jpg'));
		$this->assertEquals($prefix.'themes/default/img/testdefault.jpg',CopixUrl::getResource('img/testdefault.jpg'));

		$this->assertEquals($prefix.'img/notFound.jpg',CopixUrl::getResource('img/notFound.jpg'));
	}

	/**
	 * Fonction qui test la localistation des fichiers template par rapports aux thèmes langues et pays
	 */
	function testThemeWithoutI18N (){

		$prefix = CopixUrl::get();
		CopixConfig::Instance()->i18n_path_enabled=false;

		$this->assertEquals($prefix.'themes/testtheme/img/testthemefrFR.jpg',CopixUrl::getResource('img/testthemefrFR.jpg'));
		$this->assertEquals($prefix.'themes/testtheme/img/testthemefr.jpg',CopixUrl::getResource('img/testthemefr.jpg'));

		CopixTpl::setTheme ('themenonexistant');
		$this->assertEquals($prefix.'themes/default/img/testdefaultfrFR.jpg',CopixUrl::getResource('img/testdefaultfrFR.jpg'));
		$this->assertEquals($prefix.'themes/default/img/testdefaultfr.jpg',CopixUrl::getResource('img/testdefaultfr.jpg'));

		$this->assertEquals($prefix.'img/notFound.jpg',CopixUrl::getResource('img/notFound.jpg'));
	}

	function testModuleURL (){

		$themePrefix = CopixUrl::get().'themes/testtheme/modules/test/';
		$modulePrefix = CopixUrl::get().'resource.php/testtheme/fr_FR/test/';

		// Attention ici on teste getResourcePath
		$this->assertEquals($modulePrefix.'img/module_fr_FR.jpg',CopixUrl::getResource('test|img/module_fr_FR.jpg'));
		$this->assertEquals($modulePrefix.'img/module_fr.jpg',CopixUrl::getResource('test|img/module_fr.jpg'));
		$this->assertEquals($modulePrefix.'img/module.jpg',CopixUrl::getResource('test|img/module.jpg'));

		$this->assertEquals($themePrefix.'img/fr_FR/overriden_fr_FR.jpg',CopixUrl::getResource('test|img/overriden_fr_FR.jpg'));
		$this->assertEquals($themePrefix.'img/fr/overriden_fr.jpg',CopixUrl::getResource('test|img/overriden_fr.jpg'));
		$this->assertEquals($themePrefix.'img/overriden.jpg',CopixUrl::getResource('test|img/overriden.jpg'));

		$this->assertEquals(CopixUrl::get().'img/notFound.jpg',CopixUrl::getResource('test|img/notFound.jpg'));
	}

	function testModuleI18N (){
		$modulePrefix = CopixModule::getPath('test').'www/';

		// Attention ici on teste getResourcePath
		$this->assertEquals($modulePrefix.'img/fr_FR/module_fr_FR.jpg',CopixUrl::getResourcePath('test|img/module_fr_FR.jpg'));
		$this->assertEquals($modulePrefix.'img/fr/module_fr.jpg',CopixUrl::getResourcePath('test|img/module_fr.jpg'));
		$this->assertEquals($modulePrefix.'img/module.jpg',CopixUrl::getResourcePath('test|img/module.jpg'));

		$this->assertEquals('themes/testtheme/modules/test/img/fr_FR/overriden_fr_FR.jpg',CopixUrl::getResourcePath('test|img/overriden_fr_FR.jpg'));
		$this->assertEquals('themes/testtheme/modules/test/img/fr/overriden_fr.jpg',CopixUrl::getResourcePath('test|img/overriden_fr.jpg'));
		$this->assertEquals('themes/testtheme/modules/test/img/overriden.jpg',CopixUrl::getResourcePath('test|img/overriden.jpg'));
	}

	function testModuleWithoutI18N (){

		CopixConfig::Instance()->i18n_path_enabled=false;
		$modulePrefix = CopixModule::getPath('test').'www/';

		// Attention ici on teste getResourcePath
		$this->assertEquals($modulePrefix.'img/module_fr_FR.jpg',CopixUrl::getResourcePath('test|img/module_fr_FR.jpg'));
		$this->assertEquals($modulePrefix.'img/module_fr.jpg',CopixUrl::getResourcePath('test|img/module_fr.jpg'));
		$this->assertEquals($modulePrefix.'img/module.jpg',CopixUrl::getResourcePath('test|img/module.jpg'));

		$this->assertEquals('themes/testtheme/modules/test/img/overriden_fr_FR.jpg',CopixUrl::getResourcePath('test|img/overriden_fr_FR.jpg'));
		$this->assertEquals('themes/testtheme/modules/test/img/overriden_fr.jpg',CopixUrl::getResourcePath('test|img/overriden_fr.jpg'));
		$this->assertEquals('themes/testtheme/modules/test/img/overriden.jpg',CopixUrl::getResourcePath('test|img/overriden.jpg'));
	}

	function testModuleContext (){
		$modulePrefix = CopixModule::getPath('test').'www/';

		// Attention ici on teste getResourcePath
		$this->assertEquals($modulePrefix.'img/fr_FR/module_fr_FR.jpg',CopixUrl::getResourcePath('|img/module_fr_FR.jpg'));
		$this->assertEquals($modulePrefix.'img/fr/module_fr.jpg',CopixUrl::getResourcePath('|img/module_fr.jpg'));
		$this->assertEquals($modulePrefix.'img/module.jpg',CopixUrl::getResourcePath('|img/module.jpg'));

		$this->assertEquals(CopixUrl::get().'img/notFound.jpg',CopixUrl::getResource('|img/notFound.jpg'));
	}
}