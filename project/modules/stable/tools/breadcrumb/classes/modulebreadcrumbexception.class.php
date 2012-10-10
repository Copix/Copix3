<?php
/**
 * @package tools
 * @subpackage breadcrumb
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Exception pour le module breadcrumb
 * 
 * @package tools
 * @subpackage breadcrumb
 */
class ModuleBreadCrumbException extends CopixException {
	/**
	 * Lien invalide
	 */
	const INVALID_LINK = 1;
	
	/**
	 * Contenu du paramètre path pour l'événement breadcrumb invalide
	 */
	const INVALID_PATH = 2;
	
	/**
	 * Contenu du paramètre complexpath pour l'événement breadcrumb invalide
	 */
	const INVALID_COMPLEXPATH = 3;
}