<?php
/**
 * Informations sur un commentaire
 */
class CommentsComment {
	/**
	 * Identifiant
	 * 
	 * @var int
	 */
	private $_id = null;

	/**
	 * Groupe de commentaire auquel appartient ce commentaire
	 *
	 * @var CommentsGroup
	 */
	private $_group = null;

	/**
	 * Auteur
	 * 
	 * @var string
	 */
	private $_author = null;

	/**
	 * Site web
	 * 
	 * @var string
	 */
	private $_website = null;

	/**
	 * E-mail
	 * 
	 * @var string
	 */
	private $_email = null;

	/**
	 * Commentaire
	 * 
	 * @var string
	 */
	private $_value = null;

	/**
	 * Date et heure
	 * 
	 * @var string
	 */
	private $_date = null;

	/**
	 * Constructeur
	 */
	public function __construct () {
		$this->_group = new CommentsGroupsGroup ();
	}

	/**
	 * Définit la valeur de Identifiant
	 * 
	 * @param int $pValue Valeur
	 */
	public function setId ($pValue) {
		$this->_id = $pValue;
	}

	/**
	 * Retourne la valeur de Identifiant
	 * 
	 * @return int
	 */
	public function getId () {
		return $this->_id;
	}

	/**
	 * Définit le groupe de commentaires
	 *
	 * @param CommentsGroupsGroup $pGroup Groupe de commentaires
	 */
	public function setGroup ($pGroup) {
		$this->_group = $pGroup;
	}

	/**
	 * Retourne le groupe
	 * 
	 * @return CommentsGroup
	 */
	public function getGroup () {
		return $this->_group;
	}

	/**
	 * Définit la valeur de Auteur
	 * 
	 * @param string $pValue Valeur
	 */
	public function setAuthor ($pValue) {
		$this->_author = $pValue;
	}

	/**
	 * Retourne la valeur de Auteur
	 * 
	 * @return string
	 */
	public function getAuthor () {
		return $this->_author;
	}

	/**
	 * Définit la valeur de Site web
	 * 
	 * @param string $pValue Valeur
	 */
	public function setWebsite ($pValue) {
		$this->_website = $pValue;
	}

	/**
	 * Retourne la valeur de Site web
	 * 
	 * @return string
	 */
	public function getWebsite () {
		return $this->_website;
	}

	/**
	 * Définit la valeur de E-mail
	 * 
	 * @param string $pValue Valeur
	 */
	public function setEmail ($pValue) {
		$this->_email = $pValue;
	}

	/**
	 * Retourne la valeur de E-mail
	 * 
	 * @return string
	 */
	public function getEmail () {
		return $this->_email;
	}

	/**
	 * Définit la valeur de Commentaire
	 * 
	 * @param string $pValue Valeur
	 */
	public function setComment ($pValue) {
		$this->_value = $pValue;
	}

	/**
	 * Retourne la valeur de Commentaire
	 * 
	 * @return string
	 */
	public function getComment () {
		return $this->_value;
	}

	/**
	 * Définit la valeur de Date et heure
	 * 
	 * @param string $pDateTime Date et heure au format yyyymmddhhiiss
	 */
	public function setDate ($pDateTime) {
		$this->_date = CopixDateTime::yyyymmddhhiissToTimestamp ($pDateTime);
	}

	/**
	 * Retourne la valeur de Date et heure
	 * 
	 * @param string $pFormat Format de retour, null pour le format de la langue courante
	 * @return string
	 */
	public function getDate ($pFormat = null) {
		if ($this->_date == null) {
			return null;
		}
		if ($pFormat == null) {
			$pFormat = CopixI18N::getDateTimeFormat ();
		}
		return date ($pFormat, $this->_date);
	}

	/**
	 * Indique si l'objet est valide
	 * 
	 * @return mixed
	 */
	public function isValid () {
		return _validator ('comments|CommentsValidator')->check ($this);
	}
}