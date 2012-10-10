<?php
/**
 * @package		webtools
 * @subpackage	simplewiki
 * @author		Brice Favre
 * @copyright 	CopixTeam
 * @link      	http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion des URL significatives pour SimpleWiki
 * @package		webtools
 * @subpackage	simplewiki
 */
class UrlHandlerSimpleWiki extends CopixUrlHandler {
	
	/**
	 * parse
	 *
	 * Handle url decryption
	 *
	 * @param path          array of path element
	 * @param parameters    array of parameters (eq $this-vars)
	 * @param mode          urlsignificantmode : prepend / none
	 * @return array ([module]=>, [desc]=>, [action]=>, [parametre]=>)
	 */
	function parse ($path, $mode) {
		$toReturn = array();

		if ($mode != 'prepend'){
			return false;
		}
		if($path[0] != 'simplewiki'){
			return false;
		}

		if (count ($path) == 2){
			$toReturn['module']  = 'simplewiki';
			$toReturn['desc']    = 'default';
			$toReturn['action']  = 'show';
			$toReturn['WikiName'] = $path[1];
			return $toReturn;
		} else if (count ($path) == 3) {
			// Simplewiki/WikiName/action
			$toReturn['module']  = 'simplewiki';
			$toReturn['desc']    = 'default';
			$toReturn['action']  = $path[2];
			$toReturn['WikiName'] = $path[1];
			return $toReturn;	
		}
		return false;

	}

}