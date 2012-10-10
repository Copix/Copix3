<?php
/**
 * @package		standard
 * @subpackage	test
 * @author		Steevan BARBOYON
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Test de CopixProxy
 * 
 * @package		standard
 * @subpackage	test
 */
class Test_CopixProxyTest extends CopixTest {
	/**
	 * Sauvegarde l'adresse temporaire à tester
	 * 
	 * @var string
	 */
	private $_host = null;
	
	/**
	 * Ajoute un proxy
	 * 
	 * @return string
	 */
	private function _addProxy () {
		$id = uniqid ('copixproxy_test_');
		CopixConfig::instance ()->copixproxy_register (
			$id, $this->_getHost (), 18, 'myUser', 'myPassword', true,
			$this->_getNotForHosts (), $this->_getForHosts ()
		);
		return $id;
	}
	
	/**
	 * Retourne la liste des adresses pour lesquelles le proxy à tester ne sera pas configuré
	 * 
	 * @return array
	 */
	private function _getNotForHosts () {
		return array ('hostNotFor1', 'hostNotFor2');
	}
	
	/**
	 * Retourne la liste des adresses pour lesquelles le proxy à tester sera configuré
	 * 
	 * @return array
	 */
	private function _getForHosts () {
		return array ('hostFor1', $this->_getHost ());
	}
	
	/**
	 * Retourne l'adresse
	 * 
	 * @return string
	 */
	private function _getHost () {
		if ($this->_host === null) {
			$this->_host = uniqid ('__MyUnknowHost__');
		}
		return $this->_host;
	}
	
	/**
	 * Test les méthodes add, exists et delete
	 */
	public function testAddExistsDelete () {
		$id = $this->_addProxy ();
		$this->assertTrue (CopixConfig::instance ()->copixproxy_exists ($id));
		CopixConfig::instance ()->copixproxy_unregister ($id);
		$this->assertFalse (CopixConfig::instance ()->copixproxy_exists ($id));
	}
	
	/**
	 * Test la méthode get, et la lecture du proxy
	 */
	public function testGet () {
		$id = $this->_addProxy ();
		$proxy = CopixConfig::instance ()->copixproxy_get ($id);
		$this->assertEquals ($this->_getHost (), $proxy->getHost ());
		$this->assertEquals (18, $proxy->getPort ());
		$this->assertEquals ('myUser', $proxy->getUser ());
		$this->assertEquals ('myPassword', $proxy->getPassword ());
		$this->assertTrue ($proxy->isEnabled ());
		$this->assertEquals ($this->_getNotForHosts (), $proxy->getNotForHosts ());
		$this->assertEquals ($this->_getForHosts (), $proxy->getForHosts ());
		CopixConfig::instance ()->copixproxy_unregister ($id);
	}
	
	/**
	 * Test la méthode getForHost
	 */
	public function testGetForHost () {
		$this->assertEquals (null, CopixConfig::instance ()->copixproxy_getForHost ($this->_getHost ()));
		$id = $this->_addProxy ();
		$this->assertTrue (CopixConfig::instance ()->copixproxy_getForHost ($this->_getHost ()) instanceof CopixProxy);
		CopixConfig::instance ()->copixproxy_unregister ($id);
	}
	
	/**
	 * Test la méthode getProxys
	 */
	public function testGetProxys () {
		$proxys = CopixConfig::instance ()->copixproxy_getProxys ();
		$id = $this->_addProxy ();
		$this->assertEquals (count ($proxys) + 1, count (CopixConfig::instance ()->copixproxy_getProxys ()));
		CopixConfig::instance ()->copixproxy_unregister ($id);
	}
}