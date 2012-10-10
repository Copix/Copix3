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
 * Test des fonctions de formattage
 * @package standard
 * @subpackage test
 */
class Test_FormatterTest extends CopixTest {
	function testReduce (){
		$this->assertTrue (strlen (CopixFormatter::getReduced ('td_script_revente_historique_id', 30)) <= 30);
		$this->assertTrue (strlen (CopixFormatter::getReduced ('une_super_longue_variable_qui_tue_tellement_elle_est_longue', 20)) <= 20);			
		$this->assertTrue (strlen (CopixFormatter::getReduced ('uneSuperLongueVariable', 20)) <= 20);
		$this->assertEquals (strlen (CopixFormatter::getReduced ('abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz_abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz', 20)), 20);
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
	
	function testPrenom (){
		//On test la prise en charge des caractères accentués uniquement si MBSTRING est présent
		if (extension_loaded ('mbstring')){
			$this->assertEquals ('Émilie', CopixFormatter::getPrenom ('émilie'));
		}
		$this->assertEquals ('Emile', CopixFormatter::getPrenom ('emile'));
	}

	function testNom(){
		//On test la prise en charge des caractères accentués uniquement si MBSTRING est présent
		if (extension_loaded ('mbstring')){
			$this->assertEquals ('CROËS', CopixFormatter::getNom ('croës'));
		}
		$this->assertEquals ('JEAN-DUPONT', CopixFormatter::getNom ('Jean-Dupont'));
	}
}