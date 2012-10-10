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
	 * @param boolean $pShowLastLink Indique si on doit afficher le lien ou uniquement le libellé
	 * @param array $pExtras Informations supplémentaires
	 */
	public function __construct ($pURL, $pCaption, $pShowLastLink = true, $pExtras = array ()) {
		$this->setURL ($pURL);
		$this->setCaption ($pCaption);
		$this->setShowLastLink ($pShowLastLink);
		foreach ($pExtras as $name => $value) {
			$this->setExtra ($name, $value);
		}
	}

	/**
	 * Définit l'adresse pointée
	 *
	 * @param string $pURL
	 */
	public function setURL ($pURL) {
		$this->_url = (substr ($pURL, 0, 11) != 'javascript:' && substr ($pURL, 0, 1) != '#') ? _url ($pURL) : $pURL;
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
	 * Définit le libellé du lien
	 *
	 * @param string $pCaption
	 */
	public function setCaption ($pCaption) {
		$this->_caption = $pCaption;
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
	 * Définit si on doit voir le dernier lien
	 *
	 * @param boolean $pShow
	 */
	public function setShowLastLink ($pShow) {
		$this->_showLink = _filter ('boolean')->get ($pShow);
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
	 * Définit l'information supplémentaire donnée
	 *
	 * @param string $pName
	 * @param mixed $pValue
	 */
	public function setExtra ($pName, $pValue) {
		$this->_extras[$pName] = $pValue;
	}

	/**
	 * Retourne l'information supplémentaire demanée
	 *
	 * @param string $pName
	 * @param mixed $pDefault
	 * @return mixed
	 */
	public function getExtra ($pName, $pDefault = null) {
		return (array_key_exists ($pName, $this->_extras)) ? $this->_extras[$pName] : $pDefault;
	}
	
	/**
	 * Retourne les informations supplémentaires
	 *
	 * @return array
	 */
	public function getExtras () {
		return $this->_extras;
	}
}