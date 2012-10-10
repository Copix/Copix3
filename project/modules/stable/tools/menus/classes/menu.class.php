<?php
class MenusMenu {
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
	 * Adresse pointée
	 * 
	 * @var string
	 */
	private $_url = null;

	/**
	 * Indique si le menu est sélectionné
	 * 
	 * @var boolean
	 */
	private $_selected = false;

	/**
	 * Adresse de l'icone
	 *
	 * @var string
	 */
	private $_icon = null;

	/**
	 * Informations supplémentaires
	 *
	 * @var array
	 */
	private $_extras = array ();

	/**
	 * Menus enfants
	 *
	 * @var MenusMenu[]
	 */
	private $_children = array ();
	
	/**
	 * Indique si c'est ledernier menu de la liste
	 * 
	 * @var boolean
	 */
	private $_isLast = false;

	/**
	 * Constructeur
	 *
	 * @param string $pId Identifiant
	 * @param string $pCaption Libellé
	 * @param string $pUrl Adresse pointée
	 * @param boolean $pSelected Indique si l'élément est sélectionné
	 */
	public function __construct ($pId = null, $pCaption = null, $pUrl = null, $pSelected = false, $pIcon = null) {
		$this->setId ($pId);
		$this->setCaption ($pCaption);
		$this->setUrl ($pUrl);
		$this->setSelected ($pSelected);
		$this->setIcon ($pIcon);
	}

	/**
	 * Définit l'identifiant
	 *
	 * @param string $pId
	 */
	public function setId ($pId) {
		$this->_id = $pId;
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
	 * Définition du libellé
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
	 * Définition de l'adresse pointée
	 * 
	 * @param string $pUrl
	 */
	public function setUrl ($pUrl) {
		$this->_url = ($pUrl != null) ? _url ($pUrl) : null;
	}

	/**
	 * Retourne l'dresse pointée
	 * 
	 * @return string
	 */
	public function getUrl () {
		return $this->_url;
	}

	/**
	 * Définit si le menu est sélectionné
	 * 
	 * @param boolean $pSelected
	 */
	public function setSelected ($pSelected) {
		$this->_selected = $pSelected;
	}

	/**
	 * Indique si le menu est sélectionné
	 * 
	 * @return boolean
	 */
	public function getSelected () {
		return $this->_selected;
	}

	/**
	 * Définit l'adresse de l'icone
	 *
	 * @param string $pIcon
	 */
	public function setIcon ($pIcon) {
		$this->_icon = $pIcon;
	}

	/**
	 * Retourne l'adresse de l'icone
	 *
	 * @return string
	 */
	public function getIcon () {
		return $this->_icon;
	}

	/**
	 * Définit la valeur de l'information supplémentaire
	 *
	 * @param string $pName Nom
	 * @param mixed $pValue Valeur
	 */
	public function setExtra ($pName, $pValue) {
		$this->_extras[$pName] = $pValue;
	}

	/**
	 * Retourne la valeur de l'information supplémentaire
	 *
	 * @param string $pName Nom
	 * @param mixed $pDefault Valeur par défaut
	 * @return mixed
	 */
	public function getExtra ($pName, $pDefault = null) {
		return (array_key_exists ($pName, $this->_extras)) ? $this->_extras[$pName] : $pDefault;
	}

	/**
	 * Retourne toutes les informations supplémentaires
	 *
	 * @return array
	 */
	public function getExtras () {
		return $this->_extras;
	}
	
	/**
	 * Définit si c'est le dernier menu de la liste
	 * 
	 * @param boolean $pIsLast 
	 */
	public function setIsLast ($pIsLast) {
		$this->_isLast = _filter ('boolean')->get ($pIsLast);
	}
	
	/**
	 * Indique si c'est le dernier menu de la liste
	 * 
	 * @return boolean
	 */
	public function isLast () {
		return $this->_isLast;
	}

	/**
	 * Ajoute un enfant et le retourne
	 *
	 * @param string $pId Identifiant
	 * @param string $pCaption Libellé
	 * @param string $pUrl Adresse pointée
	 * @param boolean $pSelected Indique si l'élément est sélectionné
	 * @return MenusMenu
	 */
	public function addChild ($pId, $pCaption = null, $pUrl = null, $pSelected = null, $pIcon = null) {
		$child = new MenusMenu ($pId, $pCaption, $pUrl, $pSelected, $pIcon);
		if (count ($this->_children) > 0) {
			$this->getChild (count ($this->_children) - 1)->setIsLast (false);
		}
		$child->setIsLast (true);
		$this->_children[] = $child;
		return $child;
	}
	
	/**
	 * Retourne l'enfant d'index donné
	 * 
	 * @param int $pIndex
	 * @return MenusMenu
	 * @throws MenusException 
	 */
	public function getChild ($pIndex) {
		if (array_key_exists ($pIndex, $this->_children)) {
			return $this->_children[$pIndex];
		}
		throw new MenusException ('Le menu d\'index "' . $pIndex . '" n\'existe pas');
	}

	/**
	 * Ajoute plusieurs enfants
	 *
	 * @param MenusMenu[] $pChildren
	 */
	public function addChildren ($pChildren) {
		$this->_children = array_merge ($this->_children, $pChildren);
	}

	/**
	 * Retourne tous les enfants
	 *
	 * @return MenusMenu[]
	 */
	public function getChildren () {
		return $this->_children;
	}

	/**
	 * Supprime tous les enfants
	 */
	public function clearChildren () {
		$this->_children = array ();
	}
}