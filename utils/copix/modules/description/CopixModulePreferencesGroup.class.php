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
 * Groupe de préférences
 * 
 * @package copix
 * @subpackage modules
 */
class CopixModulePreferencesGroup {
	/**
	 * Identifiant
	 *
	 * @var string
	 */
	private $_id = null;
	
	/**
	 * Nom
	 *
	 * @var string
	 */
	private $_caption = null;
	
	/**
	 * Icone
	 * 
	 * @var string
	 */
	private $_icon = null;

	/**
	 * Préférences
	 *
	 * @var CopixModulePreference[]
	 */
	private $_preferences = array ();
	
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
		$this->_icon = ($pIcon != null && is_readable (_resourcePath ($pIcon))) ? _resource ($pIcon) : null;
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
	 * Retourne la préférence demandée
	 *
	 * @param string $pName Nom
	 * @return CopixModulePreference
	 */
	public function get ($pName) {
		if (!$this->exists ($pName)) {
			throw new CopixModuleException (_i18n ('copix:moduledescription.error.notFound', $pName));
		}
		return $this->_preferences[$pName];
	}

	/**
	 * Retourne les préférences
	 *
	 * @return CopixModulePreference[]
	 */
	public function getList () {
		return $this->_preferences;
	}

	/**
	 * Indique si la préférence existe
	 *
	 * @param string $pName Nom
	 * @return boolean
	 */
	public function exists ($pName) {
		return array_key_exists ($pName, $this->_preferences);
	}

	/**
	 * Ajoute une préférence
	 *
	 * @param CopixModulePreference $pPreference Préférence à ajouter
	 */
	public function add (CopixModulePreference $pPreference) {
		$this->_preferences[$pPreference->getName ()] = $pPreference;
	}

	/**
	 * supprime la préférence donnée
	 *
	 * @param string $pName Nom, forme module|id
	 */
	public function delete ($pName) {
		if (array_key_exists ($pName, $this->_preferences)) {
			unset ($this->_preferences[$pName]);
		}
	}
}