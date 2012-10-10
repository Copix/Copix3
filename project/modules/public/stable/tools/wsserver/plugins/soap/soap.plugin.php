<?php
/**
 * @package standard
 * @subpackage soap
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Diverses opérations pour faciliter l'utilisation de SOAP, et notamment le cache
 * 
 * @package standard
 * @subpackage soap
 */
class PluginSOAP extends CopixPlugin {
	/**
	 * Redéfinit le répertoire des caches de SOAP, pour pouvoir le vider facilement
	 */
	public function beforeSessionStart () {
		$cache = COPIX_TEMP_PATH . 'soap/cache/';
		// création du répertoire si il n'existe pas, sinon soap ne le fait pas, et ne génère jamais de cache
		if (!is_dir ($cache)) {
			CopixFile::createDir ($cache);
		}
		ini_set ('soap.wsdl_cache_dir', $cache);
	}
}