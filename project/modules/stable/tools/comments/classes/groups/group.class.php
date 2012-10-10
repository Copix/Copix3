<?php
/**
 * Informations sur un groupe de commentaire
 */
class CommentsGroupsGroup {
	/**
	 * Identifiant
	 * 
	 * @var string
	 */
	private $_id = null;

	/**
	 * Libellé
	 * 
	 * @var string
	 */
	private $_caption = null;

	/**
	 * Auteur requis
	 * 
	 * @var boolean
	 */
	private $_authorRequired = true;

	/**
	 * Site web requis
	 * 
	 * @var boolean
	 */
	private $_websiteRequired = true;

	/**
	 * E-mail requis
	 * 
	 * @var boolean
	 */
	private $_emailRequired = true;

	/**
	 * Définit la valeur de Identifiant
	 * 
	 * @param string $pValue Valeur
	 */
	public function setId ($pValue) {
		$this->_id = $pValue;
	}

	/**
	 * Retourne la valeur de Identifiant
	 * 
	 * @return string
	 */
	public function getId () {
		return $this->_id;
	}

	/**
	 * Définit la valeur de Libellé
	 * 
	 * @param string $pValue Valeur
	 */
	public function setCaption ($pValue) {
		$this->_caption = $pValue;
	}

	/**
	 * Retourne la valeur de Libellé
	 * 
	 * @return string
	 */
	public function getCaption () {
		return $this->_caption;
	}

	/**
	 * Définit la valeur de Auteur requis
	 * 
	 * @param boolean $pValue Valeur
	 */
	public function setIsAuthorRequired ($pValue) {
		$this->_authorRequired = _filter ('boolean')->get ($pValue);
	}

	/**
	 * Retourne la valeur de Auteur requis
	 * 
	 * @return boolean
	 */
	public function isAuthorRequired () {
		return $this->_authorRequired;
	}

	/**
	 * Définit la valeur de Site web requis
	 * 
	 * @param boolean $pValue Valeur
	 */
	public function setIsWebsiteRequired ($pValue) {
		$this->_websiteRequired = _filter ('boolean')->get ($pValue);
	}

	/**
	 * Retourne la valeur de Site web requis
	 * 
	 * @return boolean
	 */
	public function isWebsiteRequired () {
		return $this->_websiteRequired;
	}

	/**
	 * Définit la valeur de E-mail requis
	 * 
	 * @param boolean $pValue Valeur
	 */
	public function setIsEmailRequired ($pValue) {
		$this->_emailRequired = _filter ('boolean')->get ($pValue);
	}

	/**
	 * Retourne la valeur de E-mail requis
	 * 
	 * @return boolean
	 */
	public function isEmailRequired () {
		return $this->_emailRequired;
	}

	/**
	 * Indique si l'objet est valide
	 * 
	 * @return mixed
	 */
	public function isValid () {
		return _validator ('comments|CommentsGroupsValidator')->check ($this);
	}
}