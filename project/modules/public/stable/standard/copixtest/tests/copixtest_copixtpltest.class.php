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
	/**
	 * Fonction qui test la localistation des fichiers template par rapports aux thèmes langues et pays
	 */
	function testTheme (){
		$tpl = new CopixTpl;
		$saveConfigi18n = CopixConfig::Instance()->i18n_path_enabled;
		
		CopixConfig::Instance()->i18n_path_enabled=true;
		CopixTpl::setTheme ('testtheme');
		CopixI18N::setLang('fr');
		CopixI18N::setCountry('FR');

		$this->assertEquals($tpl->fetch('testthemefrFR.tpl'),"testthemefrFR");
		$this->assertEquals($tpl->fetch('testthemefr.tpl'),"testthemefr");
		$this->assertEquals($tpl->fetch('testtheme.tpl'),"testtheme");
		CopixConfig::Instance()->i18n_path_enabled=false;
		$this->assertEquals($tpl->fetch('testthemefrFR.tpl'),"testtheme");
		CopixConfig::Instance()->i18n_path_enabled=true;
		CopixTpl::setTheme ('themenonexistant');
		$this->assertEquals($tpl->fetch('testdefaultfrFR.tpl'),"testdefaultfrFR");
		$this->assertEquals($tpl->fetch('testdefaultfr.tpl'),"testdefaultfr");
	    CopixConfig::Instance()->i18n_path_enabled=false;
		$this->assertEquals($tpl->fetch('testdefaultfrFR.tpl'),"testdefault");
		CopixConfig::Instance()->i18n_path_enabled=true;
		$this->assertEquals($tpl->fetch('testdefault.tpl'),"testdefault");
		$this->assertEquals($tpl->fetch('testmodule.tpl'),"testmodule");
		CopixConfig::Instance()->i18n_path_enabled=$saveConfigi18n;
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