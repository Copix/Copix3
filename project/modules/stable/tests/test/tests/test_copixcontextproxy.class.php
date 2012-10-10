<?php
/**
 * @package		standard
 * @subpackage	test
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe FOO pour vérifier qu'elle s'exécute bien dans le bon contexte au travers du proxy
 * @package standard
 * @subpackage test
 */
class CopixToBeContexted {
	/**
	 * Récupération du cotnexte d'exécution
	 *
	 * @return string
	 */
	function getContext (){
		return CopixContext::get ();
	}
}

/**
 * Tests sur les proxy 
 * @package standard
 * @subpackage test
 */
class Test_CopixContextProxy extends CopixTest {
	public function testContext (){
		$class = new CopixToBeContexted ();
		$proxy = new CopixContextProxy ($class, 'default');

		$firstContext = CopixContext::get ();

		CopixContext::push ('tests');
		$this->assertEquals ('tests', CopixContext::get ());		
		$this->assertEquals ('default', $proxy->getContext ());
		$this->assertEquals ('tests', CopixContext::get ());
		CopixContext::pop ();

		$this->assertEquals ($firstContext, CopixContext::get ());
	} 
}