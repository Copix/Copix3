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
 * Description du groupe d'un module
 * 
 * @package copix
 * @subpackage modules
 */
class CopixModuleGroup {
	/**
	 * Pour la compatibilité avec Copix 3.0.x, on autorise l'accès aux propriétés suivantes
	 * 
	 * @var array
	 */
	private $_allowGet = array ('id' => 'getId', 'caption' => 'getCaption');
	
	/**
	 * Identifiant du groupe
	 *
	 * @var string
	 */
	private $_id = null;
	
	/**
	 * Nom du groupe
	 *
	 * @var string
	 */
	private $_caption = null;
	
	/**
	 * Constructeur
	 *
	 * @param string $pId Identifiant
	 * @param string $pCaption Nom
	 */
	public function __construct ($pId, $pCaption = null) {
		$this->_id = $pId;
		$this->_caption = ($pCaption === null) ? $pId : $pCaption;
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
	 * Retourne l'identifiant
	 *
	 * @return string
	 */
	public function getId () {
		return $this->_id;
	}
	
	/**
	 * Retourne le nom
	 *
	 * @return string
	 */
	public function getCaption () {
		return $this->_caption;
	}
}