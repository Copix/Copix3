<?php
/**
 * @package standard
 * @subpackage copixtest
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Test des fonctions de formattage
 * @package standard
 * @subpackage copixtest
 */
class CopixTest_FormatterTest extends CopixTest {
	function testReduce (){
		$this->assertTrue (strlen (CopixFormatter::getReduced ('une_super_longue_variable_qui_tue_tellement_elle_est_longue', 0, 20)) <= 20);			
		$this->assertTrue (strlen (CopixFormatter::getReduced ('uneSuperLongueVariable', 0, 20)) <= 20);
	}

	function testExplodeCapitalized (){
		$this->assertEquals (array ('Il', 'Etait', 'Une', 'Fois'), CopixFormatter::explodeCapitalized ('IlEtaitUneFois'));
		$this->assertEquals (array ('XML', 'String', 'For', 'You'), CopixFormatter::explodeCapitalized ('XMLStringForYou'));
		$this->assertEquals (array ('il', 'Etait', 'Une', 'Fois'), CopixFormatter::explodeCapitalized ('ilEtaitUneFois'));
		$this->assertEquals (array ('il', 'Etait', 'Un', 'XML'), CopixFormatter::explodeCapitalized ('ilEtaitUnXML'));
		$this->assertEquals (array ('il', 'Etait', 'Un', 'XML', 'File'), CopixFormatter::explodeCapitalized ('ilEtaitUnXMLFile'));
		$this->assertEquals (array ('il', 'Etait', 'Un', 'XML', 'File', 'S'), CopixFormatter::explodeCapitalized ('ilEtaitUnXMLFileS'));
		$this->assertEquals (array ('il', 'Fait', 'S4'), CopixFormatter::explodeCapitalized ('ilFaitS4'));
		$this->assertEquals (array ('il', 'Fait', 'S478W'), CopixFormatter::explodeCapitalized ('ilFaitS478W'));
		$this->assertEquals (array ('il', 'Fait', 'S478W', 'Milamber'), CopixFormatter::explodeCapitalized ('ilFaitS478WMilamber'));
		$this->assertEquals (array ('il', 'Fait', 'S478W', 'Milamber1'), CopixFormatter::explodeCapitalized ('ilFaitS478WMilamber1'));
		$this->assertEquals (array ('il', 'Fait', 'S478W', 'Milamber12'), CopixFormatter::explodeCapitalized ('ilFaitS478WMilamber12'));
		$this->assertEquals (array ('il', 'Fait', 'S478W', 'Milamber12nb'), CopixFormatter::explodeCapitalized ('ilFaitS478WMilamber12nb'));
		$this->assertEquals (array ('il', 'Fait', 'S478W', 'Milamber12', 'Super'), CopixFormatter::explodeCapitalized ('ilFaitS478WMilamber12Super'));
		$this->assertEquals (array ('il', 'Fait', 'S478W', 'Milamber12nb', 'Super'), CopixFormatter::explodeCapitalized ('ilFaitS478WMilamber12nbSuper'));
	}
}
?>