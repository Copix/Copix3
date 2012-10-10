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
class CopixModulePreference {
	/**
	 * Nom (module|id)
	 *
	 * @var string
	 */
	private $_name = null;

	/**
	 * Libellé
	 *
	 * @var string
	 */
	private $_caption = null;

	/**
	 * Description
	 *
	 * @var string
	 */
	private $_description = null;

	/**
	 * Type (text, int, bool, select)
	 *
	 * @var string
	 */
	private $_type = null;

	/**
	 * Valeur par défaut
	 *
	 * @var mixed
	 */
	private $_default = null;

	/**
	 * Liste de valeurs pour le cas ou $_type = 'select'
	 *
	 * @var array
	 */
	private $_listValues = array ();
	
	/**
	 * Constructeur
	 *
	 * @param string $pName Nom
	 * @param string $pCaption Libellé
	 * @param string $pDescription Description
	 * @param string $pType Type
	 * @param mixed $pDefaultValue Valeur par défaut
	 * @param array $pListValues Liste des valeurs possible, pour le cas ou $pType = 'select'
	 */
	public function __construct ($pName, $pCaption = null, $pDescription = null, $pType = 'text', $pDefaultValue = null, $pListValues = array ()) {
		$this->_name = $pName;
		$this->_caption = ($pCaption != null) ? $pCaption : $pName;
		$this->_description = $pDescription;
		$this->_type = $pType;
		$this->_default = $pDefaultValue;
		$this->_listValues = $pListValues;
	}

	/**
	 * Retourne le nom
	 *
	 * @return string
	 */
	public function getName () {
		return $this->_name;
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
	 * Retourne la description
	 *
	 * @return string
	 */
	public function getDescription () {
		return $this->_description;
	}

	/**
	 * Retourne le type
	 *
	 * @return string
	 */
	public function getType () {
		return $this->_type;
	}

	/**
	 * Retourne la valeur par défaut quand on veut lire la valeur de la config, et qu'elle n'est pas configurée
	 *
	 * @param boolean $pFormat Formater le retour
	 * @return mixed
	 */
	public function getDefaultValue ($pFormat = false) {
		if ($pFormat) {
			if ($this->getType () == 'bool') {
				return ($this->_default == 0) ? _i18n ('copix:common.buttons.no') : _i18n ('copix:common.buttons.yes');
			} else if ($this->_type == 'select') {
				return (array_key_exists ($this->_default, $this->_listValues)) ? $this->_listValues[$this->_default] : $this->_default;
			}
			return $this->_default;
		} else {
			return $this->_default;
		}
	}

	/**
	 * Retourne la liste des valeurs pour $_type = 'select'
	 *
	 * @return array
	 */
	public function getListValues () {
		return $this->_listValues;
	}
}