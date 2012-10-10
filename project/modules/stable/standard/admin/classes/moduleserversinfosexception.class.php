<?php
/**
 * @package tools
 * @subpackage serversinfos
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Exceptions pour le module serversinfos 
 * 
 * @package tools
 * @subpackage serversinfos 
 */
class ModuleServersInfosException extends CopixException {
	/**
	 * Profile de base de données demandé invalide
	 */
	const INVALID_DB_PROFILE = 1;
}