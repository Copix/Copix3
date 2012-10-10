<?php
/**
* @package		tools 
 * @subpackage	wsserver
 * @author		Brice Favre
 * @copyright 	2001-2007 CopixTeam
 * @link      	http://copix.org
 * @licence  	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */


/**
 * Gestionnaire d'url
* @package		tools 
 * @subpackage	wsserver
 */
class UrlHandlerWSServer extends CopixUrlHandler {
    
    /**
     * Parse l'url du wsserver
     *
     * @param $path
     * @param $mode
     * @return array
     */
     function parse ($path, $mode) {
        if ($mode!='prepend'){
            return false;
        }

        if ($path[0] != 'wsserver') {
            return false;
        }

        if (count ($path) == 2 &&  $path[1] == 'wsdl') {
            $toReturn['module']  = 'wsserver';
            $toReturn['desc']    = 'default';
            $toreturn['action'] = $path[1];
        } else if (count ($path) == 2) {
            $toReturn['module']  = 'wsserver';
            $toReturn['desc']    = 'default';
            $toReturn['action']  = 'default';
            $toReturn['wsname']  = $path[1];
        } else if (count ($path) == 3 && $path[1] != 'admin') {
            $toReturn['module']  = 'wsserver';
            $toReturn['desc']    = 'default';
            if ($path [2] == 'wsdl') {
                $toReturn['action']  = $path[2];
                $toReturn['wsname']  = $path[1];
            } else {
                $toReturn['action']  = $path[1];
                $toReturn['wsname']  = $path[2];
            }
        } else if ( count ($path) == 4 ) {
            $toReturn['module']  = $path[0];
            $toReturn['desc']    = $path[1];
            $toReturn['action']  = $path[2];
            $toReturn['wsname']  = $path[3];
        } else if ( count ($path) == 5 ) {
            $toReturn['module']  = $path[0];
            $toReturn['desc']    = $path[1];
            $toReturn['action']  = $path[2];
            $toReturn['wsname']  = $path[3];
            $toReturn['version']  = $path[4];
        } else {
            return false;
        }
        return $toReturn;
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
		} else {
			$toReturn = new stdClass ();
			if (isset ($parameters['wsname'])) {
				$toReturn->path = array_merge($dest,array ('wsname' => CopixUrl::escapeSpecialChars ($parameters['wsname'])));
				unset ($parameters['wsname']);				
			} else {
				$toReturn->path = $dest;
			}
			$toReturn->vars = $parameters;
			return $toReturn;
		}
		
	}
}