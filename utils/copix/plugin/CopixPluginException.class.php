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
 * Exception pour les plugins
 * 
 * @package copix
 * @subpackage plugin
 */
class CopixPluginException extends CopixException {
	/**
	 * Plugin requis mais non enregistré
	 */
	const REQUIRED = 1;
	
	/**
	 * Plugin non trouvé
	 */
	const NOT_FOUND = 2;
}