<?php
/**
 * @package copix
 * @subpackage events
 * @author Croës Gérald, Patrice Ferlet
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Représentation d'un événement
 *
 * @package copix
 * @subpackage events
 */
class CopixEvent {
	/**
	 * Nom de l'événement
	 *
	 * @var string
	 */
	private $_name = null;

	/**
	 * Paramètres de l'événement
	 *
	 * @var array
	 */
	private $_params = null;

	/**
	 * Constructeur
	 *
	 * @param string $pName Nom de l'événement
	 * @param array $pParams Paramètres passés à l'événement
	 */
	public function __construct ($pName, $pParams = array ()) {
		$this->_name = $pName;
		$this->_params = $pParams;
	}

	/**
	 * Retourne le nom de l'événement
	 *
	 * @return string
	 */
	public function getName () {
		return $this->_name;
	}

	/**
	 * Retourne la valeur d'un paramètre passé à l'événement
	 *
	 * @param string $pName Nom du paramètre dont on souhaites récupérer la valeur
	 * @return mixed
	 */
	public function getParam ($pName, $pDefaultValue = null) {
		return (isset ($this->_params[$pName])) ? $this->_params[$pName] : $pDefaultValue;
	}

	/**
	 * Retourne tous les paramètres
	 *
	 * @return array
	 */
	public function getParams () {
		return $this->_params;
	}
}