<?php
/**
 * @package standard
 * @subpackage copixtest
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * @package standard
 * @subpackage copixtest
 * Class de test unitaire pour les ressources
 */
class copixtest_copixurlresourcetest extends CopixTest {
	function setUp (){
		CopixContext::push ('copixtest');
	}
	function tearDown (){
		CopixContext::pop ();
	}

	/**
	 * Fonction qui test la localistation des fichiers template par rapports aux thèmes langues et pays
	 */
	function testTheme (){
		$saveConfigi18n = CopixConfig::Instance()->i18n_path_enabled;
		
		CopixConfig::Instance()->i18n_path_enabled=true;
		CopixTpl::setTheme ('testtheme');
		CopixI18N::setLang('fr');
		CopixI18N::setCountry('FR');
		
		$this->assertEquals(CopixUrl::getResource('img/testthemefrFR.jpg'),CopixUrl::get().'themes/testtheme/img/fr_FR/testthemefrFR.jpg');
		$this->assertEquals(CopixUrl::getResource('img/testthemefr.jpg'),CopixUrl::get().'themes/testtheme/img/fr/testthemefr.jpg');
		$this->assertEquals(CopixUrl::getResource('img/testtheme.jpg'),CopixUrl::get().'themes/testtheme/img/testtheme.jpg');
		
		CopixConfig::Instance()->i18n_path_enabled=false;
		$this->assertEquals(CopixUrl::getResource('img/testthemefrFR.jpg'),CopixUrl::get().'themes/testtheme/img/testthemefrFR.jpg');
		$this->assertEquals(CopixUrl::getResource('img/testthemefr.jpg'),CopixUrl::get().'themes/testtheme/img/testthemefr.jpg');
		CopixConfig::Instance()->i18n_path_enabled=true;
		
		CopixTpl::setTheme ('themenonexistant');
		$this->assertEquals(CopixUrl::getResource('img/testdefaultfrFR.jpg'),CopixUrl::get().'themes/default/img/fr_FR/testdefaultfrFR.jpg');
		$this->assertEquals(CopixUrl::getResource('img/testdefaultfr.jpg'),CopixUrl::get().'themes/default/img/fr/testdefaultfr.jpg');
		$this->assertEquals(CopixUrl::getResource('img/testdefault.jpg'),CopixUrl::get().'themes/default/img/testdefault.jpg');
		CopixConfig::Instance()->i18n_path_enabled=false;
		
		$this->assertEquals(CopixUrl::getResource('img/testdefaultfrFR.jpg'),CopixUrl::get().'themes/default/img/testdefaultfrFR.jpg');
		$this->assertEquals(CopixUrl::getResource('img/testdefaultfr.jpg'),CopixUrl::get().'themes/default/img/testdefaultfr.jpg');
		CopixConfig::Instance()->i18n_path_enabled=$saveConfigi18n;
	}
}
?>