<?php
/**
* @package		standard
* @subpackage	test
* @author		Croës Gérald
* @copyright	2001-2008 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package		standard
 * @subpackage	test
 */
class Test_CopixContextTest extends CopixTest {
	private $_context;
	
	function setUp (){
		$this->_context = CopixContext::get ();
		CopixContext::clear ();
	}
	
	/**
	 * Tests sur les contextes
	 */
	function testContext (){
		$this->assertNotNull ($this->_context);
		
		//En premier lieu, le Contexte doit être default
		$this->assertEquals (CopixContext::get (), 'default');
		
		//On vérifie que l'on arrive à placer des Contextes
		CopixContext::push ('module1');
		$this->assertEquals (CopixContext::get (), 'module1');
		
		//On vérifie que l'on pop correctement le contexte
		$lastContext = CopixContext::pop ();
		$this->assertEquals ($lastContext, 'module1');
		
		//On vérifie que le contexte est revenu à default
		$this->assertEquals ('default', CopixContext::get ());
		
        //on vérifie que même si l'utilisateur fait n'importe quoi et pop de trop, 
		//on indique default comme dernier contexte (ce qui est le cas)
		$lastContext = CopixContext::pop ();
		$this->assertEquals ($lastContext, 'default');
		
		//On vérifie que l'on peut poser plusieurs éléments
		$push = array ('push1', 'push2', 'push3', 'push4');
		foreach ($push as $pushMe){
			CopixContext::push ($pushMe);
			$this->assertEquals ($pushMe, CopixContext::get ());
		}
		
		//et on vérifie que ces éléments reviennent dans le bon ordre
		for ($i = 0; $i<count ($push); $i++){
			$poped = CopixContext::pop ();
			$this->assertEquals ($push[count ($push)-($i+1)], $poped);
		}
		
		//on vérifie que l'on est bien retourné en default
		$this->assertEquals (CopixContext::get (), 'default');
		
		//on repousse le tableau pour contrôler la méthode clear.
		foreach ($push as $pushMe){
			CopixContext::push ($pushMe);
			$this->assertEquals ($pushMe, CopixContext::get ());
		}
		// On efface les contextes
		CopixContext::clear ();
		$this->assertEquals (CopixContext::get (), 'default');
	}
	
	function testgetStack (){
		//mise en place du stack
		$stack = array ('test', 'test2', 'test3');
		foreach ($stack as $element){
			CopixContext::push ($element);	
		}

		$stacked = CopixContext::getStack ();
		$reversedStack = array ('test3', 'test2', 'test');
		foreach ($reversedStack as $position => $element){
			$this->assertEquals ($element, $stacked[$position]);
		}
	}
	
	function tearDown (){
		CopixContext::push ($this->_context);
	}
}