<?php
/**
 * @package standard
 * @subpackage admin
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Exceptions pour le module admin
 *
 * @package standard
 * @subpackage admin
 */
class ModuleAdminException extends CopixException {
	/**
	 * Proxy non trouvé
	 */
	const PROXY_NOT_FOUND = 1;
	
	/**
	 * Proxy existant
	 */
	const PROXY_EXISTS = 2;
	
	/**
	 * Fichier de configuration des proxys interdit en écriture
	 */
	const PROXY_CONFIG_NOT_WRITABLE = 3;
	
	/**
	 * Le validateur n'a pas validé un proxy
	 */
	const PROXY_INVALID = 4;

	/**
	 * Fichier de configuration des base de données interdit en écriture
	 */
	const DB_CONFIG_NOT_WRITABLE = 5;
	
	/**
	 * Erreur lors de l'écriture du fichier de config des bases de données
	 */
	const DB_CONFIG_WRITE_ERROR = 6;
	
	/**
	 * Profil de log non trouvé
	 */
	const LOG_PROFILE_NOT_FOUND = 5;
	
	/**
	 * Fichier de configuration des profils de log interdit en écriture
	 */
	const LOG_CONFIG_NOT_WRITABLE = 6;
	
	/**
	 * Order de tri invalide
	 */
	const LOG_INVALID_ORDER = 7;
	
	/**
	 * Contenu du log non lisible
	 */
	const LOG_NOT_READABLE = 8;

	/**
	 * Order de tri invalide pour les plugins
	 */
	const PLUGIN_INVALID_ORDER = 9;

	/**
	 * Le module demandé n'est pas installé, et ne peut pas être configuré
	 */
	const MODULE_NOT_ENABLED = 10;
}