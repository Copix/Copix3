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
 * Inclusion automatique du Zend Framework
 *
 * @package standard
 * @subpackage default
 */
class PluginAutoloadZf extends CopixPlugin implements ICopixBeforeSessionStartPlugin {
	/**
	 * Retourne le libellé
	 *
	 * @return string
	 */
	public function getCaption () {
		return 'Inclusion automatique de Zend Framework';
	}

	/**
	 * Retourne la description
	 *
	 * @return string
	 */
	public function getDescription () {
		return 'Avant le démarrage de la session :<br /><ul><li>Inclusion de "Zend/Loader.php"</li><li>Ajout de l\'autoload "Zend_Loader"</li></ul>';
	}

	/**
	 * Inclusion de la fonction d'autoload ZF avant le démarrage de la session
	 */
	public function beforeSessionStart () {
		if (@include_once('Zend/Loader.php')){
			// retreive autoload registered functions
			$autoloadStack = spl_autoload_functions() ;

			// unregister all autoload
			foreach ($autoloadStack as $autoload) {
				spl_autoload_unregister (array($autoload[0], $autoload[1]));
			}
			 
			spl_autoload_register(array('Zend_Loader', 'loadClass'));

		 //register previous autoload
			foreach ($autoloadStack as $autoload){
				spl_autoload_register (array($autoload[0], $autoload[1]));
			}
		}
	}
}