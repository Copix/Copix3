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
 * Erreur de récupération de ressource : accès interdit
 *
 * @package		copix
 * @subpackage	utils
 */
class CopixResourceForbiddenException extends CopixResourceException {
	/**
	 * Constructeur
	 *
	 * @param string $pResourceName Nom de la resource dont on n'a pas le droit d'accès
	 */
	public function __construct ($pResourceName) {
		$this->message = _i18n ('copix:copixresource.resourceForbidden', $pResourceName);
	}
}