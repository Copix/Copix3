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
 * @package		standard
 * @subpackage	test
 */
class Test_CopixRequestTest extends CopixTest {
	/**
	 * La requête avant exécution du test
	 *
	 * @var array
	 */
	private $_oldRequest = array ();
	
	/**
	 * Tableau à utiliser pour le setup
	 *
	 * @var unknown_type
	 */
	private $_tableau = array ();
	
	public function setUp (){
		$array = CopixRequest::asArray ();
		$this->_tableau = array ('element'=>'valeur', 'element2'=>'valeur-\\//& "3', 'element3'=>'valeur 4', 'elementvide'=>'');
		CopixRequest::setRequest($this->_tableau);
	}
	
	public function tearDown (){
		CopixRequest::setRequest ($this->_oldRequest);		
	}

	public function testBase (){
		$this->assertEquals ($this->_tableau, CopixRequest::asArray ());
		foreach ($this->_tableau as $key=>$value){
			$this->assertEquals ($value, CopixRequest::get ($key));
			$this->assertTrue (CopixRequest::exists ($key));
		}
		
		//définition d'un élément supplémentaire
		CopixRequest::set ('element4', 'valeur5');
		$this->assertEquals ('valeur5', CopixRequest::get ('element4'));
		//on vérifie que cela n'a pas perturbé le reste
		foreach ($this->_tableau as $key=>$value){
			$this->assertEquals ($value, CopixRequest::get ($key));
		}
	}
	
	public function testAssert (){
		try {
			CopixRequest::assert ('pouet');
			$this->fail ('CopixRequest::assert');
		}catch (CopixRequestException $e){}

		try {
			CopixRequest::assert ('element');
		}catch (CopixRequestException $e){
			$this->fail ('CopixRequest::assert');
		}
	}

	public function testDefaultValues (){
		$this->assertEquals ('defaultValue', CopixRequest::get ('foo', 'defaultValue'));
	}
	
	public function testInt (){
		$this->assertEquals (3, CopixRequest::getInt ('element2'));
		$this->assertEquals (null, CopixRequest::getInt ('elementFoo', null));
	}
	
	public function testAlphaNum () {
		$this->assertEquals ('valeur 3', CopixRequest::getAlphaNum ('element2'));
	}
	
	public function testFloat () {
		$this->assertEquals (3.0, CopixRequest::getFloat ('element2'));
	}

	public function testNumeric () {
		$this->assertEquals ('3', CopixRequest::getNumeric ('element2'));
	}

	public function testAlpha () {
		$this->assertEquals ('valeur ', CopixRequest::getAlpha ('element2'));
	}
	
	public function testVide (){
		$this->assertTrue (_request ('elementvide') === null);
		$this->assertTrue (_request ('elementvide', 'default') === 'default');
		$this->assertTrue (_request ('elementvide', 'default', false) === '');
		
	}
}