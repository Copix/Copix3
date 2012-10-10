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
 * Test de la classe CopixCheck
 * @package standard
 * @subpackage copixtest
 */
class CopixTest_CopixCheckTest extends CopixTest {
	
	private $_objectToCheck; 
	function setUp (){
		$this->_objectToCheck = new CopixPPO ();
		$this->_objectToCheck->nom = 'OK';
		$this->_objectToCheck->prenom = '';
	}
	
	function testSimpleCheck (){
		$arParams = array ('nom', 'prenom', 'autre');
		$chkObj = new CopixCheck ();
		$returnObj = $chkObj->addParams ($arParams)->check ($this->_objectToCheck);
		$this->assertTrue ($returnObj->nom);
		$this->assertFalse ($returnObj->prenom);
		$this->assertFalse ($returnObj->autre);
	}

}
?>