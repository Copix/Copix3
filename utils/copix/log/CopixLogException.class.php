<?php
/**
 * @package copix
 * @subpackage log
 * @author    Landry Benguigui
 * @copyright 2001-2008 CopixTeam
 * @link      http://copix.org
 * @license	  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion des exceptions de type log
 *
 * @package		copix
 * @subpackage	log
 */
class CopixLogException extends CopixException {
	/**
	 * Code d'erreur retourné avec l'exception lors d'une lecture d'un log qui ne peut pas être lu
	 */
	const NOT_READABLE = 1;
	
	/**
	 * Code d'erreur retourné avec l'exception lors d'une suppression d'un contenu de log qui ne peut pas être supprimé
	 */
	const NOT_WRITABLE = 2;
	
	/**
	 * Structure des données invalide pour un profil
	 */
	const INVALID_PROFILE_STRUCTURE = 3;
}