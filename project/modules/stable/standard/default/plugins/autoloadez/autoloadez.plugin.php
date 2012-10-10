<?php
/**
 * @package standard
 * @subpackage default
 * @author Favre Brice
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package standard
 * @subpackage default
 */
class PluginAutoloadEz extends CopixPlugin implements ICopixBeforeSessionStartPlugin {
	/**
	 * Retourne le libellé
	 *
	 * @return string
	 */
	public function getCaption () {
		return 'Inclusion automatique de Easy Publish Component';
	}

	/**
	 * Retourne la description
	 *
	 * @return string
	 */
	public function getDescription () {
		return 'Avant le démarrage de la session :<br /><ul><li>Inclusion de "ezc/Base/base.php"</li><li>Ajout de l\'autoload "ezcBase"</li></ul>';
	}

	/**
	 * Inclusion du package Easy Publish Component avant le démarrage de la session
	 */
	public function beforeSessionStart () {
		if (@include_once ('ezc/Base/base.php')) {
			// retreive autoload registered functions
			$autoloadStack = spl_autoload_functions() ;

			// unregister all autoload
			foreach ($autoloadStack as $autoload) {
				spl_autoload_unregister (array($autoload[0], $autoload[1]));
			}

			spl_autoload_register (array ('ezcBase', 'autoload'));
				
		 //register previous autoload
			foreach ($autoloadStack as $autoload){
				spl_autoload_register (array($autoload[0], $autoload[1]));
			}

		}
	}
}