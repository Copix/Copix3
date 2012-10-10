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
 * Informations sur une dépendance d'un module
 * 
 * @package copix
 * @subpackage modules
 */
class CopixModuleDependency {
	/**
	 * Pour la compatibilité avec Copix 3.0.x, on autorise l'accès aux propriétés suivantes
	 * 
	 * @var array
	 */
	private $_allowGet = array ('name' => 'getName', 'kind' => 'getKind');
	
	/**
	 * Nom du module dépendant
	 *
	 * @var string
	 */
	private $_name = null;
	
	/**
	 * Type de la dépendance
	 * 
	 * @var string
	 */
	private $_kind = null;

	/**
	 * Version minimum
	 *
	 * @var string
	 */
	private $_version = null;
	
	/**
	 * Constructeur
	 *
	 * @param string $pName Nom du module dépendant
	 * @param string $pKind Type de la dépendance
	 */
	public function __construct ($pName, $pKind = 'module', $pVersion = null) {
		$this->_name = $pName;
		$this->_kind = $pKind;
		$this->_version = $pVersion;
	}
	
	/**
	 * Pour la compatibilité avec Copix 3.0.x
	 *
	 * @param string $pName Propriété dont on veut la valeur
	 */
	public function __get ($pName) {
		if (array_key_exists ($pName, $this->_allowGet)) {
			$method = $this->_allowGet[$pName];
			return $this->$method ();
		}
	}
	
	/**
	 * Retourne le nom du module dépendant
	 *
	 * @return string
	 */
	public function getName () {
		return $this->_name;
	}
	
	/**
	 * Retourne le type de la dépendance
	 *
	 * @return string
	 */
	public function getKind () {
		return $this->_kind;
	}

	/**
	 * Retourne la version minimum dépendante
	 *
	 * @return string
	 */
	public function getVersion () {
		return $this->_version;
	}
}