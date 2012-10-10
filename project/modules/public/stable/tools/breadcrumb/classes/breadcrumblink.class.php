<?php
/**
 * @package tools
 * @subpackage breadcrumb
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Informations sur un lien
 * 
 * @package tools
 * @subpackage breadcrumb
 */
class BreadCrumbLink {
	/**
	 * Adresse du lien
	 *
	 * @var CopixURL
	 */
	private $_url = null;
	
	/**
	 * Libellé du lien
	 *
	 * @var string
	 */
	private $_caption = null;
	
	/**
	 * Indique si on doit afficher le lien ou uniquement le libellé
	 *
	 * @var boolean
	 */
	private $_showLink = null;
	
	/**
	 * Informations supplémentaires
	 *
	 * @var array
	 */
	private $_extras = array ();
	
	/**
	 * Constructeur
	 *
	 * @param string $pURL Adresse du lien
	 * @param string $pLabel Libellé du lien
	 * @param boolean $pShowLink Indique si on doit afficher le lien ou uniquement le libellé
	 * @param array $pExtras Informations supplémentaires
	 */
	public function __construct ($pURL, $pCaption, $pShowLink = true, $pExtras = array ()) {
		$this->_url = (substr ($pURL, 0, 11) != 'javascript:' && substr ($pURL, 0, 1) != '#') ? _url ($pURL) : $pURL;
		$this->_caption = $pCaption;
		$this->_showLink = $pShowLink;
		$this->_extras = $pExtras;
	}
	
	/**
	 * Retourne l'adresse du lien
	 *
	 * @return CopixURL
	 */
	public function getURL () {
		return $this->_url;
	}
	
	/**
	 * Retourne le libellé
	 *
	 * @return string
	 */
	public function getCaption () {
		return $this->_caption;
	}
	
	/**
	 * Indique si on doit afficher le lien ou juste le libellé
	 *
	 * @return boolean
	 */
	public function getShowLink () {
		return $this->_showLink;
	}
	
	/**
	 * Retourne les informations supplémentaires
	 *
	 * @param mixed $pId Identifiant de l'information supplémentaire, null pour toutes les infos
	 * @return array
	 */
	public function getExtras ($pId = null) {
		return ($pId === null) ? $this->_extras : (array_key_exists ($pId, $this->_extras)) ? $this->_extras[$pId] : null;
	}
}