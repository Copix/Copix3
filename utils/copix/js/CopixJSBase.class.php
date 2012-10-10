<?php
/**
 * @package    copix
 * @subpackage utils
 * @author     Guillaume Perréal
 * @copyright  2001-2008 CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de base pour la construction de Javascript.
 */
class CopixJSBase implements ICopixJSONEncodable {

	/**
	 * Code Javascript.
	 *
	 * @var mixed
	 */
	protected $_code;

	/**
	 * Constructeur.
	 *
	 * @param mixed $pCode Code initial.
	 */
	public function __construct($pCode) {
		$this->_code = $pCode;
	}

	/**
	 * Représentation texte : retourne le code Javascript.
	 *
	 * @return string Code Javascript.
	 */
	public function __toString() {
		return _toString($this->_code);
	}

	/**
	 * Conversion en JSON : retourne la représentation texte.
	 *
	 * @return string Code Javascript.
	 */
	public function toJSON() {
		return $this->__toString();
	}

	/**
	 * Construit la représentation d'un appel de fonction.
	 *
	 * @param string $pName Nom de la fonction/méthode.
	 * @param array $pArgs Liste des arguments.
	 * @return string Appel de fonction.
	 */
	protected function _buildCall($pName, $pArgs) {
		return $pName.'('.implode(',', array_map(array('CopixJSONEncoder', 'encode'), $pArgs)).')';
	}
}