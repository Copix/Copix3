<?php
/**
 * @package		tools
 * @subpackage	soap_server
 * @author		Brice Favre
 * @copyright 	2001-2008 CopixTeam
 * @link      	http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestionnaire d'url
 * @package		tools
 * @subpackage	soap_server
 */
class UrlHandlersoap_server extends CopixUrlHandler {

	/**
	 * Parse l'url du soap_server
	 *
	 * @param $path
	 * @param $mode
	 * @return array
	 */
	function parse ($path, $mode) {
		//Pas de prise en charge des modes autres que prepend
		if ($mode != 'prepend'){
			return false;
		}

		//Pas de prise en charge pour les autres modules
		if ($path[0] != 'soap_server') {
			return false;
		}
		
		//Cas des WSDL
		if (count ($path) == 3 &&  $path[1] == 'wsdl') {
			$toReturn['module']  = 'soap_server';
			$toReturn['group']   = 'default';
			$toReturn['action']  = 'wsdl';
			$toReturn['name'] = $path[2];
			return $toReturn;
		}
		
		//Cas des interrogations
		if (count ($path) == 2 && $path[1] !== 'admin' && $path[1] !== 'default'){
			$toReturn['module'] = 'soap_server';
			$toReturn['group'] = 'default';
			$toReturn['action'] = 'default';
			$toReturn['name'] = $path[1];
			return $toReturn;
		}
		return false;
	}

	/**
	 * Recupère les éléments de l'URL
	 *
	 * @param $dest
	 * @param $parameters
	 * @param $mode
	 * @return StdClass object
	 */
	function get ($dest, $parameters, $mode) {
		if ($mode == 'none') {
			return false;
		}
		
		//Pas d'url rewriting pour les autres modules
		if ($dest['module'] != 'soap_server'){
			return false;
		}

		//Pas d'url Rewriting pour les actions d'admin
		if ($dest['group'] == 'admin'){
			return false;
		}
		
		//Forme de l'url pour les WSDL
		if ($dest['group'] == 'default' && $dest['action'] == 'wsdl' && isset ($parameters['name'])){
			$toReturn = new StdClass ();
			$toReturn->path = array ('soap_server', 'wsdl', $parameters['name']);
			unset ($parameters['name']);
			$toReturn->vars = $parameters;
			return $toReturn;
		}
		
		//Forme des urls pour les interrogations
		if ($dest['group'] == 'default' && $dest['action'] == 'default' && isset ($parameters['name'])){
			$toReturn = new StdClass ();
			$toReturn->path = array_merge ($dest, array ('name' => $parameters['name']));
			unset ($parameters['name']);
			$toReturn->vars = $parameters;
			return $toReturn;
		}
		
		//Pour toute les autres formes, on ne fait rien de particulier
		return false;
	}
}