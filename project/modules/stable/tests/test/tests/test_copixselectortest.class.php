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
 * Test des classes de profil
 * @package		standard
 * @subpackage	test
 */
class Test_CopixSelectorTest extends CopixTest {
	/**
	 * Test des quelques fonctions de base
	 */
	function testZoneNormalization (){
		$zone = CopixSelectorFactory::getZone ('test|errors');
		$zone2 = CopixSelectorFactory::getZone ('module:test|errors');
		CopixContext::push ('test');
		$zone3 = CopixSelectorFactory::getZone ('module:errors');
		CopixContext::pop ();
		$zone4 = CopixSelectorFactory::getZone ('module:test|errors');
		$this->assertSame ($zone, $zone2);
		$this->assertSame ($zone, $zone3);
		$this->assertSame ($zone, $zone4);
		$this->assertEquals (CopixModule::getPath ('test').'zones/errors.zone.php', $zone->getFilePath ());
		$this->assertEquals (CopixModule::getPath ('test').'zones/errors.zone.php', $zone2->getFilePath ());
		$this->assertEquals ('zoneerrors', $zone->getClassName ());

		$zone = CopixSelectorFactory::getZone ('test|sous/chemin/errors');
		$this->assertNotSame ($zone, $zone2);
		$this->assertEquals (CopixModule::getPath ('test').'zones/sous/chemin/errors.zone.php', $zone->getFilePath ());
		$this->assertEquals ('zoneerrors', $zone->getClassName ());
		$this->assertNotSame ($zone, $zone2);
	}
	 
	function testClassNormalization (){

	}
}