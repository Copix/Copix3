<?php
/**
* @package   copix
* @subpackage db
* @author   Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe qui regroupe les constantes pour les paramètres de requêtes
 * @package copix
 * @subpackage db
 */
class CopixDBQueryParam {
	/**
	 * Automatique, rien de défini
	 */
	const DB_AUTO = 0;

	/**
	 * Chaine de caractère
	 */
	const DB_STRING = 1;

	/**
	 * Entier
	 */
	const DB_INT = 2;

	/**
	 * Curseur
	 */
	const DB_CURSOR = 3;
	
	/**
	 * BLOB
	 */
	const DB_BLOB = 4;
	
	/**
	 * LOB
	 */
	const DB_LOB = 5;

	/**
	 * CLOB
	 */
	const DB_CLOB = 5;	
} 
?>