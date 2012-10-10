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
 * Test de la classes CopixAuth
 * @package		standard
 * @subpackage	copixtest
 */
class CopixTest_CopixAuthTest extends CopixTest {
	/**
	 * Les handlers de départ
	 */
	private $_handlers = array ();
	
	function setUp (){
		$copixConfig = CopixConfig::instance();
		$this->_handlers = $copixConfig->copixauth_getRegisteredUserHandlers ();
		
		$sp = _daoSP ();
		$sp->addCondition ('login_dbuser', '=', 'CopixTest');		
		_dao ('dbuser')->deleteBy ($sp);
		
		$record = CopixDAOFactory::createRecord ('dbuser');
		$record->login_dbuser = 'CopixTest';
		$record->password_dbuser = md5 ('CopixTestPassword');
		$record->enabled_dbuser = 1;
		$record->email_dbuser = 'test@test.com';
		
		_dao ('dbuser')->insert ($record);
	}

	function tearDown (){
		CopixConfig::instance ()->copixauth_clearUserHandlers ();
		CopixConfig::instance ()->copixauth_clearGroupHandlers ();
		CopixConfig::instance ()->copixauth_clearCredentialHandlers  ();
		foreach ($this->_handlers as $handlerDefinition){
			CopixConfig::instance ()->copixauth_registerUserHandler ($handlerDefinition);			
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
		$this->assertTrue (CopixAuth::getCurrentUser ()->login (array ('login'=>'CopixTest', 'password'=>'CopixTestPassword')));
		
		// On vérifie la connexion de l'utilisateur 
		$this->assertEquals ('CopixTest', CopixAuth::getCurrentUser()->getLogin ());
		$this->assertTrue(CopixAuth::getCurrentUser()->isLoggedWith('dbuserhandler'));
		
		// Test de la deconnexion
		CopixAuth::getCurrentUser()->logout(null);
		$this->assertFalse(CopixAuth::getCurrentUser()->isConnected());
		
		// Test de la connection avec un utilisateur présent en base mais avec un mauvais mot de passe
		$this->assertFalse (CopixAuth::getCurrentUser ()->login (array ('login'=>'CopixTest', 'password'=>'wrongpass')));
		
		// Test de la connection avec un utilisateur null
		$this->assertFalse (CopixAuth::getCurrentUser ()->login (array()));
		
		CopixAuth::destroyCurrentUser();
	}
	
	function testDBHandler() {
		// Connection avec un utilisateur test présent en base
		$this->assertTrue (CopixAuth::getCurrentUser ()->login (array ('login'=>'CopixTest', 'password'=>'CopixTestPassword')));
		$this->assertTrue(CopixAuth::getCurrentUser()->isLoggedWith('dbuserhandler'));

		// Verification du handler utilisé
		$this->assertTrue (CopixConfig::instance ()->copixauth_isRegisteredUserHandler ('dbuserhandler'));
			
		$dbhandler = CopixUserHandlerFactory::create('dbuserhandler');
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