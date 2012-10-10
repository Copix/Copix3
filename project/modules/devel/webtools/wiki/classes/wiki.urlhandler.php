<?php
/**
 * @package	webtools
 * @subpackage	wiki
* @author	Patrice Ferlet
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Gestion des URL significatives pour le WIKI
 * @package	webtools
 * @subpackage	wiki
 */
class UrlHandlerWiki extends CopixUrlHandler {
	/**
    * parse
    *
    * Handle url decryption
    *
    * @param path          array of path element
    * @param parameters    array of parameters (eq $this-vars)
    * @param mode          urlsignificantmode : prepend / none
    * @return array([module]=>, [desc]=>, [action]=>, [parametre]=>)
    */
	function parse ($path, $mode) {
	    $toReturn = array();
	     
	    if ($mode!='prepend'){
	        return false;
	    }
	    if($path[0]!="wiki"){
	        return false;
	    }

	    if(isset($path[1]) && ($path[1]=="admin" || $path[1]=="file" || $path[1]=="specials")){
	        return false;
	    }

	    if(count($path)==2){
	        // wiki/Pagename
	        $title = $path[1];
	        $toReturn['module']  = 'wiki';
	        $toReturn['desc']    = 'default';
	        $toReturn['action']  = 'show';
	        $toReturn['title'] = $title;
	        return $toReturn;
	    }elseif(count($path)==3){
	        // wiki/Pagename/lang || wiki/Heading/Pagename
	        $langs = explode(";",CopixConfig::get('wiki|langs'));
	        $headings = _ioDao('wikiheadings')->get($path[1]);
            if(in_array($path[2],$langs)){
	            $title = $path[1];
	            $toReturn['module']  = 'wiki';
	            $toReturn['desc']    = 'default';
	            $toReturn['action']  = 'show';
	            $toReturn['lang'] = $path[2];
	            $toReturn['title'] = $title;
	            return $toReturn;
	        }elseif(count($headings)>0){
	            $title = $path[2];
	            $toReturn['module']  = 'wiki';
	            $toReturn['desc']    = 'default';
	            $toReturn['action']  = 'show';
	            $toReturn['heading'] = $path[1];
	            $toReturn['title'] = $title;
	            return $toReturn;
	        }
	    }elseif(count($path)==4){
	        // wiki/Heading/Pagename/lang
	        $title = $path[2];
	        $toReturn['module']  = 'wiki';
	        $toReturn['desc']    = 'default';
	        $toReturn['action']  = 'show';
	        $toReturn['heading'] = $path[1];
	        $toReturn['lang'] = $path[3];
	        $toReturn['title'] = $title;
	        return $toReturn;
	    }
	    return false;

	}

	/**
    * get
    *
    * Handle url encryption
    *
    * @param dest          array([module]=>, [desc]=>, [action]=>)
    * @param parameters    array of parameters (eq $this-vars)
    * @param mode          urlsignificantmode : prepend / none
    * @return object([path]=>, [vars]=>)
    */
	function get ($dest, $parameters, $mode) {
		if ($mode=='none'){
			return false;
		}else{
			if (!isset($dest['group'])) {
				$dest['group'] = '';  
			}
			if ($dest['module'] == 'wiki' && ($dest['group'] == 'default' || $dest['group'] == '') && $dest['action'] == 'show') {
				if (!isset($parameters['title'])) {
					return false;
				}
				$toReturn = new stdClass ();		
				$url = array ('wiki');
				
				if (isset ($parameters['heading'])){
					if (strlen ($parameters['heading'])>0){
						$url[]=$parameters['heading'];
					}
				}				
				$url[] = CopixUrl::escapeSpecialChars ($parameters['title']);				
				if(isset($parameters['lang'])){
					$url[]=$parameters['lang'];					
				}
				unset($parameters['heading']);
				unset($parameters['title']);
				unset($parameters['lang']);
				$toReturn->path = $url;				
				$toReturn->vars = $parameters;
				return $toReturn;
			}else{
				return false;
			}
		}
	}
}
?>