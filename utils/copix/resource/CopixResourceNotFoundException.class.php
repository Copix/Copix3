<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Guillaume Perréal
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Erreur de récupération de ressource : ressource introuvable
 *
 * @package		copix
 * @subpackage	utils
 */
class CopixResourceNotFoundException extends CopixResourceException {
	/**
	 * Constructeur
	 *
	 * @param string $pResourceName Nom de la ressource qu'on n'a pu trouver
	 */
	public function __construct ($pResourceName) {
		$this->message = _i18n ('copix:copixresource.resourceNotFound', $pResourceName);
	}
}