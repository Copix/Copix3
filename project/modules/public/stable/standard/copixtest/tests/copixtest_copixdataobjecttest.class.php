<?php
/**
 * @package    standard
 * @subpackage copixtest
 * @author     Guillaume Perréal
 * @copyright  CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe dérivée de CopixDataObject utilisée pour les tests.
 *
 */
class CDOTestClass extends CopixDataObject {

	private   $_private     = "private-default";
	protected $_protected   = "protected-default";
	public    $public       = "public-default";
	public    $unsetPublic;
	
	function getPrivate() {
		return isset($this->_private) ? $this->_private : null;
	}
	
	function setPrivate($pValue) {
		if(is_null($pValue)) {
			unset($this->_private);
		} else {
			$this->_private = $pValue;
		}
	}

	function getProtected() {
		return isset($this->_protected) ? $this->_protected : null;
	}
	
	function setProtected($pValue) {
		if(is_null($pValue)) {
			unset($this->_protected);
		} else {
			$this->_protected = $pValue;
		}
	}
	
}

/**
 * Tests de CopixDataObject.
 * 
 */
class CopixTest_CopixDataObjectTest extends CopixTest {
	
	/**
	 * Teste le constructeur 
	 *
	 */
	public function testConstruct() {

		$i0 = new CDOTestClass();
		
		$this->assertEquals("public-default", $i0->public);
		$this->assertFalse(isset($i0->unsetPublic));
		$this->assertEquals('private-default', $i0->getPrivate());
		$this->assertEquals('protected-default', $i0->getProtected());
		
		$i1 = new CDOTestClass(array(
			'public'      => 2,
			'unsetPublic' => 5,
		));
		
		$this->assertEquals(2, $i1->public);
		$this->assertEquals(5, $i1->unsetPublic);
		$this->assertEquals('private-default', $i1->getPrivate());
		$this->assertEquals('protected-default', $i1->getProtected());
		
		$i2 = new CDOTestClass(array(
			'unsetPublic' => 5,
		));

		$this->assertFalse(isset($i2->public));
		$this->assertEquals(5, $i2->unsetPublic);
		$this->assertEquals('private-default', $i1->getPrivate());
		$this->assertEquals('protected-default', $i1->getProtected());
		
	}
	
	/**
	 * Teste la méthode LoadFromArray
	 *
	 */
	public function testLoadFromArray() {
	
		$i1 = new CDOTestClass();
		$i1->loadFromArray(array(
			'public'      => 2,
			'unsetPublic' => 5,
		));
		
		$this->assertEquals(2, $i1->public);
		$this->assertEquals(5, $i1->unsetPublic);
		$this->assertEquals('private-default', $i1->getPrivate());
		$this->assertEquals('protected-default', $i1->getProtected());
		
		$i2 = new CDOTestClass();
		$i2->loadFromArray(array(
			'unsetPublic' => 5,
		));

		$this->assertFalse(isset($i2->public));
		$this->assertEquals(5, $i2->unsetPublic);
		$this->assertEquals('private-default', $i1->getPrivate());
		$this->assertEquals('protected-default', $i1->getProtected());		
		
	}
	
	/**
	 * Teste la méthode writeToArray
	 */
	public function testWriteToArray() {
	
		$i1 = new CDOTestClass();
		$a1 = array();
		$i1->writeToArray($a1);
		
		$this->assertEquals(array('public' => 'public-default'), $a1);

		$i2 = new CDOTestClass();
		$a2 = array();
		$i2->unsetPublic = 5;
		$i2->setPrivate(8);
		$i2->writeToArray($a2);

		$this->assertEquals(array(
			'unsetPublic' => 5,
			'public'      => 'public-default',
		), $a2);
		
	}	
	
	/**
	 * Teste la méthode writeToArray
	 */
	public function testToArray() {
	
		$i1 = new CDOTestClass();
		$this->assertEquals(array('public' => 'public-default'), $i1->toArray());

		$i2 = new CDOTestClass();
		$i2->unsetPublic = 5;
		$i2->setPrivate(8);

		$this->assertEquals(array(
			'unsetPublic' => 5,
			'public'      => 'public-default',
		), $i2->toArray());
		
	}		
	/**
	 * Teste la sérialisation
	 */
	public function testSerialize() {
	
		$i0 = unserialize(serialize(new CDOTestClass()));
		$this->assertEquals("public-default", $i0->public);
		$this->assertFalse(isset($i0->unsetPublic));
		$this->assertEquals('private-default', $i0->getPrivate());
		$this->assertEquals('protected-default', $i0->getProtected());

		$i1Pre = new CDOTestClass();
		$i1Pre->public = 8;
		$i1 = unserialize(serialize($i1Pre));
		$this->assertEquals(8, $i1->public);
		$this->assertFalse(isset($i1->unsetPublic));
		$this->assertEquals('private-default', $i1->getPrivate());
		$this->assertEquals('protected-default', $i1->getProtected());
		
		$i2Pre = new CDOTestClass();
		$i2Pre->setProtected("blabla");
		$i2 = unserialize(serialize($i2Pre));
		$this->assertEquals("public-default", $i2->public);
		$this->assertFalse(isset($i2->unsetPublic));
		$this->assertEquals('private-default', $i2->getPrivate());
		$this->assertEquals('protected-default', $i2->getProtected());		
	}		
	
}

?>