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
class CopixModuleLinksGroup {
	/**
	 * Pour la compatibilité avec Copix 3.0.x, on autorise l'accès aux propriétés suivantes
	 * 
	 * @var array
	 */
	private $_allowGet = array ('id' => 'getId', 'caption' => 'getCaption', 'icon' => 'getIcon');
	
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
	 * Icone du groupe
	 * 
	 * @var string
	 */
	private $_icon = null;

	/**
	 * Liens du groupe
	 *
	 * @var CopixModuleLink[]
	 */
	private $_links = array ();
	
	/**
	 * Constructeur
	 *
	 * @param string $pId Identifiant
	 * @param string $pCaption Nom
	 * @param string $pIcon Nom de l'icone, chaine à passer à _resource
	 */
	public function __construct ($pId, $pCaption, $pIcon = null) {
		$this->_id = $pId;
		$this->_caption = $pCaption;
		try {
			$this->_icon = ($pIcon != null && is_readable (_resourcePath ($pIcon))) ? _resource ($pIcon) : null;
		} catch (Exception $e) {
			$this->_icon = null;
		}
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
	
	/**
	 * Retourne l'adresse de l'icone si il existe, sinon, null
	 * 
	 * @return string
	 */
	public function getIcon () {
		return $this->_icon;
	}

	/**
	 * Retourne les liens du groupe
	 *
	 * @return CopixModuleLink[]
	 */
	public function getLinks () {
		return $this->_links;
	}

	/**
	 * Ajoute un lien
	 *
	 * @param string $pShortCaption Nom court
	 * @param string $pCaption Nom long
	 * @param string $pURL Adresse pointée
	 * @param string $pCredentials Chaine de droit
	 * @param string $pIcon Icone 16x16
	 * @param string $pBigIcon Icone 48x48
	 */
	public function addLink ($pShortCaption, $pCaption, $pURL, $pCredentials, $pIcon = null, $pBigIcon = null) {
		$this->_links[] = new CopixModuleLink ($pShortCaption, $pCaption, $pURL, $pCredentials, $pIcon, $pBigIcon);
	}
}