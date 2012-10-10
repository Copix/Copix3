<?php
/**
 * @package copix
 * @subpackage plugin
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Description d'un plugin
 * 
 * @package copix
 * @subpackage plugin
 */
class CopixPluginDescription {
	/**
	 * Identifiant (partie après | de name)
	 *
	 * @var string
	 */
	private $_id = null;

	/**
	 * Nom (module|id)
	 *
	 * @var string
	 */
	private $_name = null;

	/**
	 * Nom du module dans lequel est ce plugin (partie avant | de name)
	 *
	 * @var string
	 */
	private $_module = null;

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
	 * Retourne l'identifiant (partie après | de name)
	 *
	 * @return string
	 */
	public function getId () {
		return $this->_id;
	}

	/**
	 * Définit le nom (module|id)
	 *
	 * @param string $pName Nom
	 */
	public function setName ($pName) {
		list ($this->_module, $this->_id) = explode ('|', $pName);
		$this->_name = $pName;
	}

	/**
	 * Retourne le nom (module|id)
	 *
	 * @return string
	 */
	public function getName () {
		return $this->_name;
	}

	/**
	 * Retourne le module auquel appartient ce plugin
	 *
	 * @return string
	 */
	public function getModule () {
		return $this->_module;
	}

	/**
	 * Définit le libellé
	 *
	 * @param string $pCaption Libellé
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
	 * Définit la description
	 *
	 * @param string $pDescription Description
	 */
	public function setDescription ($pDescription) {
		$this->_description = $pDescription;
	}

	/**
	 * Retourne la descriion
	 *
	 * @return string
	 */
	public function getDescription () {
		return $this->_description;
	}

	/**
	 * Retourne le chemin du plugin
	 *
	 * @return string
	 */
	public function getPath () {
		$path = CopixFile::getRealPath (CopixModule::getPath ($this->getModule ()) . COPIX_PLUGINS_DIR . $this->getId () . DIRECTORY_SEPARATOR);
		return $path . $this->getId () . '.plugin.php';
	}

	/**
	 * Retourne le chemin du fichier de config
	 *
	 * @return string
	 */
	public function getConfigPath () {
		$path = CopixFile::getRealPath (CopixModule::getPath ($this->getModule ()) . COPIX_PLUGINS_DIR . $this->getId () . DIRECTORY_SEPARATOR);
		$path = $path . $this->getId () . '.default.conf.php';
		return (file_exists ($path)) ? $path : null;
	}

	/**
	 * Indique si le plugin est activé
	 *
	 * @return boolean
	 */
	public function isRegistered () {
		return CopixPluginRegistry::isRegistered ($this->getName ());
	}
}