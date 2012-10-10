<?php
/**
 * @package     cms
 * @subpackage  portal
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Alexandre JULIEN
 */
class portal_portletsxmlparse extends CopixTest {
	/**
	 * testParsingPortlet
	 */
	public function testParsingPortlet () {
		CopixClassesFactory::fileInclude('portal|portletservices');
		$this->assertTrue(_class('portal|portletservices')->getZoneId('diaporama') === 'libelle sans i18n');
	}
	
	/**
	 * testParsingPortlet
	 */
	public function testArrayPortlet () {
		CopixClassesFactory::fileInclude('portal|portletservices');
		$arData = array ();
		$arData['diaporama'] = 'libelle sans i18n';
		$arData['document'] = 'cheminI18N';
		$this->assertTrue(portletservices::getList() == $arData);
	}
	
}
?>