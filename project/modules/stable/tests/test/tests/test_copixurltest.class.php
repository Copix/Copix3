<?php
/**
 * @package		standard
 * @subpackage	test
 * @author		2001-2008 Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Test de la classes CopixUrl
 * @package		standard
 * @subpackage	test
 */
class Test_CopixUrlTest extends CopixTest {

	function setUp (){
		CopixContext::push('default');
	}

	function testValues () {
		CopixConfig::instance ()->url_requestedscript_variable = 'PHP_SELF';
		// Verifions que l'on retourne le bon hote
		$this->assertEquals ($_SERVER ['HTTP_HOST'], CopixUrl::getRequestedDomain());
		$this->assertRegexp ('/^\/.*test\.php$/', CopixUrl::getRequestedScript());
		$this->assertEquals ('test.php', CopixUrl::getRequestedScriptName ());
		$this->assertEquals ( 'http://', CopixUrl::getRequestedProtocol());
		$currentUrl = CopixUrl::getCurrentUrl ();
	}

	function testConstructUrl () {
		$baseUrl = "test.php?var=value";

		// Tests de construction d'une url en mode default
		CopixConfig::instance ()->significant_url_mode = 'default';
		$this->assertEquals ("test.php?var=value", CopixUrl::appendToUrl ($baseUrl));
		$this->assertEquals ("test.php?var=value2", CopixUrl::appendToUrl ($baseUrl, array ('var'=>'value2')));
		$this->assertEquals ("test.php?var=value&var1=value1", CopixUrl::appendToUrl ($baseUrl, array ('var1'=>'value1')));

		// Test de construction d'une URL en mode prepend
		CopixConfig::instance ()->significant_url_mode = 'prepend';
		$baseUrl = "test.php?var=value";
		$this->assertEquals("test.php?var=value",CopixUrl::appendToUrl($baseUrl));
	}

	function testGet () {
		// Test si on récupère une adresse par défault
		$this->assertRegexp ('/^http:\/\/.*www\/$/', CopixUrl::get());
		$this->assertRegexp ('/^http:\/\/.*www/', CopixUrl::get("#"));

		// Test des URL récupérées en mode prepend
		CopixConfig::instance ()->significant_url_mode = 'prepend';

		// On attend ici une URL en utilisant tous les cas du paramètre $pDest
		// @todo : Changer le test quand on voudra enlever le deuxième default
		$this->assertRegexp ('/^http:\/\/.*\/default\/default\/test/', _url('test'));
		$params = new StdClass ();
		$this->assertRegexp ('/^http:\/\/.*\/default\/default\/test/', _url('test', $params));

		$this->assertRegexp ('/^http:\/\/.*\/default\/test/', _url('test|'));
		$this->assertRegexp ('/^http:\/\/.*\/test/', _url('test||'));
		// On cherche l'URL d'un module avec une variable définie dans une fichier significanturl
		$this->assertRegexp ('/^http:\/\/.*?test=value$/', _url('test||',array('test'=>'value')));
		$params = new StdClass ();
		$params->test = 'value';
		$this->assertRegexp ('/^http:\/\/.*?test=value$/', _url('test||', $params));

		// On cherche l'URL d'un module avec une variable non définie dans une fichier significanturl
		$this->assertRegexp ('/^http:\/\/.*\/value$/', _url('test||',array('var'=>'value')));
		// On cherche l'URL d'un module inexistant dans le site
		// @todo : Changer le test quand on voudra enlever le deuxième default
		$this->assertRegexp ('/^http:\/\/.*\/test$/', _url('test||'));

		// Test des URL récupérées en mode default
		CopixConfig::instance ()->significant_url_mode = 'default';

		// On cherche l'URL d'un module avec une variable définie dans une fichier significanturl
		$this->assertRegexp ('/^http:\/\/.*\?module=test.*&test=value$/', _url('test||', array('test'=>'value')));

		// On cherche l'URL d'un module avec une variable non définie dans une fichier significanturl
		$this->assertRegexp ('/^http:\/\/.*\?module=test.*&var=value$/', _url('test||', array('var'=>'value')));

		// On cherche l'URL d'un module inexistant dans le site
		$this->assertRegexp ('/^http:\/\/.*\?module=test/', _url('test||'));

		$this->assertEquals ('http://www.google.fr', _url ('test|google|'));
	}

	function testParse () {
		$testUrl = "test.php?var=value";
		CopixConfig::instance ()->significant_url_mode = 'default';
		$this->assertContains ("value",CopixUrl::parse ($testUrl, true));
		CopixConfig::instance ()->significant_url_mode = 'prepend';
		$this->assertContains ("value", CopixUrl::parse ($testUrl, true));
		
		//on v�rifie que var est trouv� et ne g�n�re pas de notice
		$testUrl = "test.php?var";
		$this->assertContains ("var", array_keys (CopixUrl::parse ($testUrl, true)));
	}

	function testValueToUrl (){
		$this->assertEquals (CopixUrl::valueToUrl ('test', array (1, 2, 3, 4)), 'test[0]=1&test[1]=2&test[2]=3&test[3]=4');
		$this->assertEquals (CopixUrl::valueToUrl (null, array ('test'=>array (1, 2, 3, 4))), 'test[0]=1&test[1]=2&test[2]=3&test[3]=4');
		$this->assertEquals (CopixUrl::valueToUrl ('test', array (1, 2, 3, 4), true), '&test[0]=1&test[1]=2&test[2]=3&test[3]=4');
		$this->assertEquals (CopixUrl::valueToUrl (null, array ('test'=>array (1, 2, 3, 4)), true), '&test[0]=1&test[1]=2&test[2]=3&test[3]=4');
		$this->assertEquals (CopixUrl::valueToUrl ('test', array (1, 2, 3, 4), true, true), '&amp;test[0]=1&amp;test[1]=2&amp;test[2]=3&amp;test[3]=4');
		$this->assertEquals (CopixUrl::valueToUrl (null, array ('test'=>array (1, 2, 3, 4)),true, true), '&amp;test[0]=1&amp;test[1]=2&amp;test[2]=3&amp;test[3]=4');
	}

	function testEscapeSpecialChars (){
		$pString = 'àéïöùy';
		$this->assertEquals (CopixUrl::escapeSpecialChars($pString), 'aeiouy');

		$this->assertEquals (CopixUrl::escapeSpecialChars('Une ville du sud'), 'Une_ville_du_sud');
		$this->assertEquals (CopixUrl::escapeSpecialChars('Une ville / Un Village'), 'Une_ville__Un_Village');
		$this->assertEquals (CopixUrl::escapeSpecialChars('Une ville / Un Village', true), 'Une_ville_Un_Village');
	}

	function testParams (){

		$pUrl = 'test.php';
		$pUrlParam = 'test.php?param=value';
		$pUrlParams = 'test.php?param1=1&param2=2&param3=3';
		$pUrlParamsXml = 'test.php?param1=1&amp;param2=2&amp;param3=3';
		$pUrlEmptyParam = 'test.php?param1'; 
		$pUrlEmptyParam1 = 'test.php?param1&param2=value2';

		// Test des extrctions de paramètres
		$this->assertEquals (CopixUrl::extractParams ($pUrl, false), array ());
		$this->assertEquals (CopixUrl::extractParams ($pUrlParam, false), array ('param' => 'value'));
		$this->assertEquals (CopixUrl::extractParams ($pUrlParam, true), array ('param' => 'value'));
		$this->assertEquals (CopixUrl::extractParams ($pUrlParams, false), array ('param1' => 1, 'param2' => 2, 'param3' => 3));
		$this->assertEquals (CopixUrl::extractParams ($pUrlParamsXml, true), array ('param1' => 1, 'param2' => 2, 'param3' => 3));

		// Test des suppressions de paramètres
		$this->assertEquals (CopixUrl::removeParams ($pUrl, array('param1')), $pUrl);
		$this->assertEquals (CopixUrl::removeParams ($pUrlParam, array('param')), $pUrl);
		$this->assertEquals (CopixUrl::removeParams ($pUrlParams, array('param1')), 'test.php?param2=2&param3=3');
		$this->assertEquals (CopixUrl::removeParams ($pUrlParams, array('param2')), 'test.php?param1=1&param3=3');
		$this->assertEquals (CopixUrl::removeParams ($pUrlParams, array('param3')), 'test.php?param1=1&param2=2');
		$this->assertEquals (CopixUrl::removeParams ($pUrlParamsXml, array('param1'), true), 'test.php?param2=2&amp;param3=3');
		$this->assertEquals (CopixUrl::removeParams ($pUrlParamsXml, array('param2'), true), 'test.php?param1=1&amp;param3=3');
		$this->assertEquals (CopixUrl::removeParams ($pUrlParamsXml, array('param3'), true), 'test.php?param1=1&amp;param2=2');
		$this->assertEquals (CopixUrl::removeParams ($pUrlEmptyParam, array ('param1'), true), 'test.php');
		$this->assertEquals (CopixUrl::removeParams ($pUrlEmptyParam1, array ('param1'), true), 'test.php?param2=value');
		
		// Test des ajout de paramètres
		$this->assertEquals (CopixUrl::appendToUrl ($pUrl), $pUrl);
		$this->assertEquals (CopixUrl::appendToUrl ($pUrl, array('param'=>'value')), $pUrlParam);
		$this->assertEquals (CopixUrl::appendToUrl ($pUrl, array('param1'=>'1','param2'=>'2','param3'=>'3')), $pUrlParams);
		$this->assertEquals (CopixUrl::appendToUrl ($pUrl, array('param1'=>'1','param2'=>'2','param3'=>'3'), true), $pUrlParamsXml);
	}

	function tearDown (){
		CopixContext::pop ();
	}
}