<?php
/**
* @package		standard
* @subpackage	copixtest
* @author		Croës Gérald
* @copyright	2001-2008 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Test de la classes CopixAuth
 * @package		standard
 * @subpackage	copixtest
 */
class CopixTest_CopixAuthTest extends CopixTest {
	/**
	 * Les handlers de départ
	 */
	private $_userHandlers = array ();	
	private $_groupHandlers = array ();
	private $_credentialHandlers = array();
	
	private $_userId; 
		
	function setUp (){		
		$copixConfig = CopixConfig::instance();
		
		$this->_userHandlers = $copixConfig->copixauth_getRegisteredUserHandlers ();
		$copixConfig->copixauth_clearUserHandlers ();
		$copixConfig->copixauth_registerUserHandler (array (
			'name'     => 'auth|dbuserhandler',
			'priority' => 10, 
			'required' => false,
		));
					
		$this->_groupHandlers = $copixConfig->copixauth_getRegisteredGroupHandlers ();
		$copixConfig->copixauth_clearGroupHandlers ();
		
		$this->_credentialHandlers = $copixConfig->copixauth_getRegisteredCredentialHandlers ();
		$copixConfig->copixauth_clearCredentialHandlers  ();
		
		$sp = _daoSP ();
		$sp->addCondition ('login_dbuser', '=', 'CopixTest');		
		_dao ('dbuser')->deleteBy ($sp);
		
		$record = _record ('dbuser');
		$record->login_dbuser = 'CopixTest';
		$record->password_dbuser = md5 ('CopixTestPassword');
		$record->enabled_dbuser = 1;
		$record->email_dbuser = 'test@test.com';
		
		_dao ('dbuser')->insert ($record);
		
		$this->_userId = $record->id_dbuser;
		
	}

	function tearDown (){
		$copixConfig = CopixConfig::instance();
		
		$copixConfig->copixauth_clearUserHandlers ();
		foreach ($this->_userHandlers as $handlerDefinition){
			$copixConfig->copixauth_registerUserHandler ($handlerDefinition);			
		}

		$copixConfig->copixauth_clearGroupHandlers ();
		foreach ($this->_groupHandlers as $handlerDefinition){
			$copixConfig->copixauth_registerGroupHandler ($handlerDefinition);			
		}
		
		$copixConfig->copixauth_clearCredentialHandlers ();
		foreach ($this->_credentialHandlers as $handlerDefinition){
			$copixConfig->copixauth_registerCredentialHandler ($handlerDefinition);			
		}
		
		CopixAuth::getCurrentUser ()->logout ();
	}

	function testRegisterUserHandlers (){
		//Enregistrement bateau d'un handler utilisateur
		CopixConfig::instance ()->copixauth_registerUserHandler  ('CopixTest');
		$this->assertTrue (CopixConfig::instance ()->copixauth_isRegisteredUserHandler ('CopixTest'));
		$this->assertContains ('CopixTest', array_keys (CopixConfig::instance ()->copixauth_getRegisteredUserHandlers ()));
		
		//Suppression des handlers, on vérifie que tout est encore ok
		CopixConfig::instance ()->copixauth_clearUserHandlers ();
		$this->assertEquals (0, sizeOf (CopixConfig::instance ()->copixauth_getRegisteredUserHandlers ()));
		
		//Réenregistrement
		CopixConfig::instance ()->copixauth_registerUserHandler  ('CopixTest');
		$this->assertTrue (CopixConfig::instance ()->copixauth_isRegisteredUserHandler ('CopixTest'));
		$this->assertContains ('CopixTest', array_keys (CopixConfig::instance ()->copixauth_getRegisteredUserHandlers ()));
		
		//Nouvel handler
		CopixConfig::instance ()->copixauth_registerUserHandler  ('CopixTest2');
		$this->assertTrue (CopixConfig::instance ()->copixauth_isRegisteredUserHandler ('CopixTest'));
		$this->assertTrue (CopixConfig::instance ()->copixauth_isRegisteredUserHandler ('CopixTest2'));

		$this->assertContains ('CopixTest', array_keys (CopixConfig::instance ()->copixauth_getRegisteredUserHandlers ()));
		$this->assertContains ('CopixTest2', array_keys (CopixConfig::instance ()->copixauth_getRegisteredUserHandlers()));
	}
	
	function testRegisterGroupHandler (){
		//Enregistrement bateau d'un handler utilisateur
		CopixConfig::instance ()->copixauth_registerGroupHandler('CopixTest');
		$this->assertTrue (CopixConfig::instance ()->copixauth_isRegisteredGroupHandler ('CopixTest'));
		$this->assertContains ('CopixTest', array_keys (CopixConfig::instance ()->copixauth_getRegisteredGroupHandlers ()));

		//Suppression des handlers, on vérifie que tout est encore ok
		CopixConfig::instance ()->copixauth_clearGroupHandlers ();
		$this->assertEquals (0, sizeOf (CopixConfig::instance ()->copixauth_getRegisteredGroupHandlers ()));

		//Réenregistrement
		CopixConfig::instance ()->copixauth_registerGroupHandler('CopixTest');
		$this->assertTrue (CopixConfig::instance ()->copixauth_isRegisteredGroupHandler ('CopixTest'));
		$this->assertContains ('CopixTest', array_keys (CopixConfig::instance ()->copixauth_getRegisteredGroupHandlers ()));
		
		//Nouvel handler
		CopixConfig::instance ()->copixauth_registerGroupHandler('CopixTest2');
		$this->assertTrue (CopixConfig::instance ()->copixauth_isRegisteredGroupHandler ('CopixTest'));
		$this->assertTrue (CopixConfig::instance ()->copixauth_isRegisteredGroupHandler  ('CopixTest2'));

		$this->assertContains ('CopixTest', array_keys (CopixConfig::instance ()->copixauth_getRegisteredGroupHandlers()));
		$this->assertContains ('CopixTest2', array_keys (CopixConfig::instance ()->copixauth_getRegisteredGroupHandlers()));
	}
	
	function testRegisterCredentialHandler (){
		//Enregistrement bateau d'un handler utilisateur
		CopixConfig::instance ()->copixauth_registerCredentialHandler  ('CopixTest');
		$this->assertTrue (CopixConfig::instance ()->copixauth_isRegisteredCredentialHandler ('CopixTest'));
		$this->assertContains ('CopixTest', array_keys (CopixConfig::instance ()->copixauth_getRegisteredCredentialHandlers  ()));

		//Suppression des handlers, on vérifie que tout est encore ok
		CopixConfig::instance ()->copixauth_clearCredentialHandlers ();
		$this->assertEquals (0, sizeOf (CopixConfig::instance ()->copixauth_getRegisteredCredentialHandlers  ()));

		//Réenregistrement
		CopixConfig::instance ()->copixauth_registerCredentialHandler  ('CopixTest');
		$this->assertTrue (CopixConfig::instance ()->copixauth_isRegisteredCredentialHandler ('CopixTest'));
		$this->assertContains ('CopixTest', array_keys (CopixConfig::instance ()->copixauth_getRegisteredCredentialHandlers  ()));

		//Nouvel handler
		CopixConfig::instance ()->copixauth_registerCredentialHandler  ('CopixTest2');
		$this->assertTrue (CopixConfig::instance ()->copixauth_isRegisteredCredentialHandler  ('CopixTest'));
		$this->assertTrue (CopixConfig::instance ()->copixauth_isRegisteredCredentialHandler  ('CopixTest2'));

		$this->assertContains ('CopixTest', array_keys (CopixConfig::instance ()->copixauth_getRegisteredCredentialHandlers ()));
		$this->assertContains ('CopixTest2', array_keys (CopixConfig::instance ()->copixauth_getRegisteredCredentialHandlers  ()));
	}
	
	function testConnection (){
	
		// Connection avec un utilisateur test présent en base
		$user = CopixAuth::getCurrentUser();
		$this->assertTrue ($user->login (array ('login'=>'CopixTest', 'password'=>'CopixTestPassword')));
		$this->assertTrue ($user->isConnected());
		
		// Vérifie les infos de copixuser
		$responses = $user->getResponses();
		$this->assertEquals(1, count($responses));

		// Vérifie la première réponse
		$first = $responses[0];
		$this->assertTrue($first->getResult());
		$this->assertEquals("auth|dbuserhandler", $first->getHandler());
		$this->assertEquals("CopixTest", $first->getLogin());
		$this->assertEquals($this->_userId, $first->getId());
		
		// La méme chose via CopixUser
		$this->assertEquals("auth|dbuserhandler", $user->getHandler());
		$this->assertEquals("CopixTest", $user->getLogin());
		$this->assertEquals($this->_userId, $user->getId());
		
		// Vérifie IsConnectedAs
		$this->assertTrue($user->IsConnectedAs("auth|dbuserhandler", $this->_userId));
		$this->assertFalse($user->IsConnectedAs("dummyHandler", $this->_userId));
		$this->assertFalse($user->IsConnectedAs("auth|dbuserhandler", $this->_userId+1));
		
		// Vérifie isLoggedWith
		$this->assertTrue($user->isLoggedWith('auth|dbuserhandler'));
		$this->assertFalse($user->isLoggedWith('dummyHandler'));
		
		// Test de la deconnexion
		$user->logout(null);
		$this->assertFalse($user->isConnected());
		
		// Test de la connection avec un utilisateur présent en base mais avec un mauvais mot de passe
		$this->assertFalse ($user->login (array ('login'=>'CopixTest', 'password'=>'wrongpass')));
		$this->assertFalse ($user->isConnected());
		$this->assertEquals(0, count($user->getResponses()));
		
		// Test de la connection avec un utilisateur null
		$this->assertFalse ($user->login (array()));
		
		CopixAuth::destroyCurrentUser();
	}

	/**
	 * Test avec 2 handlers, dont le second qui répond faux.
	 *
	 */
	function testMultipleHandlersSecondFails() {
		_classInclude('copixtest|testuserhandler');
				
		$user = CopixAuth::getCurrentUser();
		CopixConfig::instance()->copixauth_registerUserHandler (array (
			'name'     => 'copixtest|testuserhandler',
			'rank'     => 20, 
			'required' => false,
		));
		
		$this->assertTrue ($user->login (array ('login'=>'CopixTest', 'password'=>'CopixTestPassword')));
		
		$this->assertTrue ($user->isConnected());
		$this->assertEquals(2, count($user->getResponses()));
		
		$this->assertEquals(array('auth|dbuserhandler', $this->_userId), $user->getIdentity());
		$this->assertEquals(array(array('auth|dbuserhandler', $this->_userId)), $user->getIdentities());
		
		$this->assertTrue ($user->isConnectedAs('auth|dbuserhandler', $this->_userId));
		$this->assertFalse ($user->isConnectedAs('auth|dbuserhandler', $this->_userId+1));
		$this->assertTrue ($user->isConnectedWith('auth|dbuserhandler'));

		$this->assertFalse ($user->isConnectedAs('copixtest|testuserhandler', 1));
		$this->assertFalse ($user->isConnectedAs('copixtest|testuserhandler', $this->_userId));
		$this->assertFalse ($user->isConnectedWith('copixtest|testuserhandler'));
		
		$this->assertEquals("CopixTest", $user->getLogin());
		$this->assertEquals($this->_userId, $user->getId());
		$this->assertEquals("auth|dbuserhandler", $user->getHandler());
	}

	/**
	 * Test avec deux handlers qui répondent positiviement.
	 *
	 */
	function testMultipleHandlersBothAllow() {
	
		$user = CopixAuth::getCurrentUser();
		CopixConfig::instance()->copixauth_registerUserHandler (array (
			'name'     => 'copixtest|testuserhandler',
			'rank'     => 20, 
			'required' => false,
		));
				
		TestUserHandler::$result = true;
		TestUserHandler::$userId = 5;
		TestUserHandler::$login = 'FakeUser';
		
		$this->assertTrue ($user->login (array ('login'=>'CopixTest', 'password'=>'CopixTestPassword')));
		
		$this->assertTrue ($user->isConnected());
		$this->assertEquals(2, count($user->getResponses()));
		
		$this->assertEquals(array('auth|dbuserhandler', $this->_userId), $user->getIdentity());
		$this->assertEquals(array(array('auth|dbuserhandler', $this->_userId), array('copixtest|testuserhandler', 5)), $user->getIdentities());
				
		$this->assertTrue ($user->isConnectedAs('auth|dbuserhandler', $this->_userId));
		$this->assertFalse ($user->isConnectedAs('auth|dbuserhandler', $this->_userId+1));
		$this->assertTrue ($user->isConnectedWith('auth|dbuserhandler'));

		$this->assertTrue ($user->isConnectedAs('copixtest|testuserhandler', 5));
		$this->assertFalse ($user->isConnectedAs('copixtest|testuserhandler', 6));
		$this->assertTrue ($user->isConnectedWith('copixtest|testuserhandler'));

		$this->assertEquals("CopixTest", $user->getLogin());
		$this->assertEquals($this->_userId, $user->getId());
		$this->assertEquals("auth|dbuserhandler", $user->getHandler());		
	}
	
	/**
	 * Test avec deux handlers dont le premier répond faux.
	 *
	 */
	function testMultipleHandlersFirstFails() {
	
		$user = CopixAuth::getCurrentUser();
		CopixConfig::instance()->copixauth_registerUserHandler (array (
			'name'     => 'copixtest|testuserhandler',
			'rank'     => 20, 
			'required' => false,
		));
				
		TestUserHandler::$result = true;
		TestUserHandler::$userId = 5;
		TestUserHandler::$login = 'FakeUser';
		
		$this->assertTrue ($user->login (array ('login'=>'CopixTest', 'password'=>'BadPassword')));
		
		$this->assertTrue ($user->isConnected());
		$this->assertEquals(2, count($user->getResponses()));
		
		$this->assertEquals(array('copixtest|testuserhandler', 5), $user->getIdentity());
		$this->assertEquals(array(array('copixtest|testuserhandler', 5)), $user->getIdentities());
		
		$this->assertFalse ($user->isConnectedAs('auth|dbuserhandler', $this->_userId));
		$this->assertFalse ($user->isConnectedAs('auth|dbuserhandler', $this->_userId+1));
		$this->assertFalse ($user->isConnectedWith('auth|dbuserhandler'));

		$this->assertTrue ($user->isConnectedAs('copixtest|testuserhandler', 5));
		$this->assertFalse ($user->isConnectedAs('copixtest|testuserhandler', 6));
		$this->assertTrue ($user->isConnectedWith('copixtest|testuserhandler'));

		$this->assertEquals("FakeUser", $user->getLogin());
		$this->assertEquals(5, $user->getId());
		$this->assertEquals("copixtest|testuserhandler", $user->getHandler());		
	}
		
	/**
	 * Test avec deux handlers qui répondent positiviement, mais dont les rangs sont différants.
	 *
	 */
	function testMultipleHandlersBothAllowRankChanged() {
	
		$user = CopixAuth::getCurrentUser();
		CopixConfig::instance()->copixauth_registerUserHandler (array (
			'name'     => 'copixtest|testuserhandler',
			'rank'     => -5, 
			'required' => false,
		));
				
		TestUserHandler::$result = true;
		TestUserHandler::$userId = 5;
		TestUserHandler::$login = 'FakeUser';
		
		$this->assertTrue ($user->login (array ('login'=>'CopixTest', 'password'=>'CopixTestPassword')));
		
		$this->assertTrue ($user->isConnected());
		$this->assertEquals(2, count($user->getResponses()));

		$this->assertEquals(array('copixtest|testuserhandler', 5), $user->getIdentity());
		$this->assertEquals(array(array('copixtest|testuserhandler', 5), array('auth|dbuserhandler', $this->_userId)), $user->getIdentities());
		
		$this->assertTrue ($user->isConnectedAs('auth|dbuserhandler', $this->_userId));
		$this->assertFalse ($user->isConnectedAs('auth|dbuserhandler', $this->_userId+1));
		$this->assertTrue ($user->isConnectedWith('auth|dbuserhandler'));

		$this->assertTrue ($user->isConnectedAs('copixtest|testuserhandler', 5));
		$this->assertFalse ($user->isConnectedAs('copixtest|testuserhandler', 6));
		$this->assertTrue ($user->isConnectedWith('copixtest|testuserhandler'));

		$this->assertEquals("FakeUser", $user->getLogin());
		$this->assertEquals(5, $user->getId());
		$this->assertEquals("copixtest|testuserhandler", $user->getHandler());		
	}
		
	function testDBHandler() {
		// Connection avec un utilisateur test présent en base
		$this->assertTrue (CopixAuth::getCurrentUser ()->login (array ('login'=>'CopixTest', 'password'=>'CopixTestPassword')));
		$this->assertTrue(CopixAuth::getCurrentUser()->isLoggedWith('auth|dbuserhandler'));

		// Verification du handler utilisé
		$this->assertTrue (CopixConfig::instance ()->copixauth_isRegisteredUserHandler ('auth|dbuserhandler'));
			
		$dbhandler = CopixUserHandlerFactory::create('auth|dbuserhandler');
		$arUsers = $dbhandler->find (array ('login'=>'CopixTest'));

		$this->assertEquals (1, count ($arUsers));
		$this->assertEquals ($arUsers[0]->login, 'CopixTest');
	}
	
	function testGroup() {
		// Connection avec un utilisateur test présent en base
		CopixConfig::instance ()->copixauth_registerGroupHandler('dbgrouphandler');
		$this->assertTrue (CopixAuth::getCurrentUser ()->login (array ('login'=>'CopixTest', 'password'=>'CopixTestPassword')));

		$this->assertTrue (CopixConfig::instance ()->copixauth_isRegisteredGroupHandler  ('dbgrouphandler'));
		$this->markTestIncomplete('Manque un test sur les informations du groupe');
	}

	function testCredentials() {
		$this->assertTrue (CopixAuth::getCurrentUser ()->login (array ('login'=>'CopixTest', 'password'=>'CopixTestPassword')));
		try {
			$this->assertFalse(CopixAuth::getCurrentUser()->assertCredential("nodroits"));
			$this->assertTrue (false);
		}catch (Exception $e){
			$this->assertTrue (true);
		}
	}
}
?>