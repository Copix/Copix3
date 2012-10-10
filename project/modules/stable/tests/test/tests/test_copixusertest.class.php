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
 * Test de la classes CopixUser
 * @package		standard
 * @subpackage	test
 */
class Test_CopixUserTest extends CopixTest {

	/**
	 * Les handlers de départ
	 */
	private $_handlers = array ();

	function setUp (){
		$copixConfig = CopixConfig::instance();
		$this->_handlers = $copixConfig->copixauth_getRegisteredUserHandlers ();
	}

	function testConnexionUser() {
		// Connection avec un utilisateur test présent en base
		$this->assertTrue (CopixAuth::getCurrentUser ()->login (array ('login'=>'test', 'password'=>'test')));

		$this->assertTrue (CopixAuth::getCurrentUser ()->isLoggedWith('auth|dbuserhandler'));

		CopixAuth::getCurrentUser()->getGroups(array('login'=>'test'));
		// Verification du handler utilisé
	}
}