<?php
/**
 * @package copix
 * @subpackage modules
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Informations sur un droit d'un module
 * 
 * @package copix
 * @subpackage modules
 */
class CopixModuleCredential {	
	/**
	 * Dans Copix 3.0.x, CopixModule::getInformations renvoyait un stdClass avec des propriétés
	 * Pour conserver cette compatibilité, ce tableau indique les liens entre les anciennes propriétés et les nouvelles méthodes
	 *
	 * @var array
	 */
	private $_allowGet = array ('name' => 'getName', 'level' => 'getLevel');
	
	/**
	 * Nom
	 *
	 * @var string
	 */
	private $_name = null;
	
	/**
	 * Niveau
	 *
	 * @var int
	 */
	private $_level = null;
	
	/**
	 * Constructeur
	 *
	 * @param string $pName Nom
	 * @param int $pLevel Niveau
	 */
	public function __construct ($pName, $pLevel) {
		$this->_name = $pName;
		$this->_level = $pLevel;
	}
	
	/**
	 * Pour la compatibilité avec Copix 3.0.x
	 *
	 * @param string $pName Propriété dont on veut la valeur
	 * @return mixed
	 */
	public function __get ($pName) {
		if (array_key_exists ($pName, $this->_allowGet)) {
			$method = $this->_allowGet[$pName];
			return $this->$method ();
		}
	}
	
	/**
	 * Retourne le nom du droit
	 *
	 * @return string
	 */
	public function getName () {
		return $this->_name;
	}
	
	/**
	 * Retourne le niveau du droit
	 *
	 * @return int
	 */
	public function getLevel () {
		return $this->_level;
	}
	
	/**
	 * Test le droit
	 *
	 * @see _currentUser ()->testCredential ()
	 */
	public function testCredential () {
		return _currentUser ()->testCredential ($this->_name);
	}
	
	/**
	 * Certifie que le droit est respecté
	 *
	 * @see _currentUser ()->assertCredential ()
	 */
	public function assertCredential () {
		_currentUser ()->assertCredential ($this->_name);
	}
}