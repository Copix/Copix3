<?php
/**
 * @package copix
 * @subpackage proxy
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Exceptions pour les proxys
 * 
 * @package copix
 * @subpackage proxy
 */
class CopixProxyException extends CopixException {
	/**
	 * Le proxy n'a pu être trouvé
	 */
	const NOT_FOUND = 1;
	
	/**
	 * Le proxy existe déja
	 */
	const EXISTS = 2;

	/**
	 * Identifiant de proxy invalide
	 */
	const INVALID_ID = 3;
	
	/**
	 * Adresse invalide
	 */
	const INVALID_HOST = 4;
	
	/**
	 * Port invalide
	 */
	const INVALID_PORT = 5;
	
	/**
	 * Tableau d'adresses attendus mais type invalide
	 */
	const INVALID_HOSTS_TYPE = 6;
	
	/**
	 * Nom d'utilisateur invalide
	 */
	const INVALID_USER = 7;
	
	/**
	 * Mot de passe invalide
	 */
	const INVALID_PASSWORD = 8;
}