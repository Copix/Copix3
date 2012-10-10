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
 * Description d'un lien indiqué via un xml de description de module
 * 
 * @package copix
 * @subpackage modules
 */
class CopixModuleLink {
	/**
	 * Nom du lien (version courte)
	 *
	 * @var string
	 */
	private $_shortCaption = null;

	/**
	 * Nom du lien (version longue)
	 *
	 * @var string
	 */
	private $_caption = null;
	
	/**
	 * Adresse pointée
	 * 
	 * @var string
	 */
	private $_url = null;
	
	/**
	 * Chaine de droit à respecter pour afficher le lien
	 * 
	 * @var string
	 */
	private $_credentials = null;
	
	/**
	 * Icone du lien (version 16x16)
	 *
	 * @var string
	 */
	private $_icon = null;

	/**
	 * Icone du lien (version 48x48)
	 *
	 * @var string
	 */
	private $_bigIcon = null;
	
	/**
	 * Constructeur
	 *
	 * @param string $pId Identifiant
	 * @param string $pURL Adresse pointée
	 * @param string $pCaption Nom
	 * @param string $pIcon Nom de l'icone, chaine à passer à _resource
	 */
	public function __construct ($pShortCaption, $pCaption, $pURL, $pCredentials, $pIcon = null, $pBigIcon = null) {
		$this->_shortCaption = $pShortCaption;
		$this->_caption = $pCaption;
		$this->_url = _url ($pURL);
		$this->_credentials = $pCredentials;
		$this->_icon = ($pIcon != null && CopixResource::exists ($pIcon)) ? _resource ($pIcon) : null;
		$this->_bigIcon = ($pBigIcon != null && CopixResource::exists ($pBigIcon)) ? _resource ($pBigIcon) : _resource ('img/adminlink_big.png');
	}

	/**
	 * Retourne le nom court
	 *
	 * @return string
	 */
	public function getShortCaption () {
		return $this->_shortCaption;
	}

	/**
	 * Retourne le nom long
	 *
	 * @return string
	 */
	public function getCaption () {
		return $this->_caption;
	}
	
	/**
	 * Retourne l'adresse pointée par le lien
	 * 
	 * @return string
	 */
	public function getURL () {
		return $this->_url;
	}
	
	/**
	 * Retourne la chaine de droit à respecter pour l'affichage du lien
	 * 
	 * @var string
	 */
	public function getCredentials () {
		return $this->_credentials;
	}
	
	/**
	 * Retourne le chemin vers l'icone 16x16
	 *
	 * @return string
	 */
	public function getIcon () {
		return $this->_icon;
	}

	/**
	 * Retourne le chemin vers l'icone 48x48
	 *
	 * @return string
	 */
	public function getBigIcon () {
		return $this->_bigIcon;
	}
}