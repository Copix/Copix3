<?php
/**
 * @package		tools
 * @subpackage	soap_server
 * @author		Favre Brice
 * @copyright	2001-2008 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Test des classes de services
 * @package		tools
 * @subpackage	soap_server
 */
class CopixTest_soap_server extends CopixTest {

	private $_client;

	function setUp (){

		$this->_client = new soapClient(CopixUrl::getRequestedProtocol().CopixUrl::getRequestedBasePath()."index.php/soap_server/default/wsdl");

		/*$sp = CopixDAOFactory::createSearchParams ();
		 $sp->addCondition ('login_dbuser', '=', 'WSUser');
		 _dao ('auth|dbuser')->deleteBy ($sp);

		 $record = CopixDAOFactory::createRecord ('auth|dbuser');
		 $record->login_dbuser = 'WSUser';
		 $record->password_dbuser = md5 ('WSUserPassword');
		 $record->email_dbuser = "mail@localhost";
		 $record->enabled_dbuser = "1";
		 _dao ('auth|dbuser')->insert ($record);*/
	}

	function tearDown (){
		CopixContext::pop ();
	}

	function testsoap_server () {
		$arFunctions = $this->_client->__getFunctions();
		$this->assertEquals (count($arFunctions),4);

		$this->assertEquals ($this->_client->returnParams("test"), "test");
		/*$this->_client->connect(array("login"=>"WSUser","password"=>"WSUserPassword"));
		 $this->assertEquals ($this->_client->protectedReturnParams("test"), "test");*/
	}
}
?>