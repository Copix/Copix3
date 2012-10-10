<?php
/**
 * @package copix
 * @subpackage utils
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Interface de gestion d'un fichier de configuration
 *
 * @package copix
 * @subpackage utils
 */
interface ICopixConfigFile {
	/**
	 * Retourne le chemin vers le fichier de configuration
	 *
	 * @return string
	 */
	public static function getPath ();
}