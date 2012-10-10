<?php
/**
* @package	webtools
* @subpackage	blog
* @author	Patrice Ferlet
* @copyright CopixTeam
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Gestion des URL significatives pour le WIKI
 * @package webtools
 * @subpackage	blog
 */
class UrlHandlerblog extends CopixUrlHandler {
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
		if ($mode!='prepend'){
			return false;
		}
		
		if($path[0]!="blog"){
			return false;
		}

		if(isset($path[1]) && ($path[1]=="admin" || $path[1]=="ajax")){
				return false;
		}
		
		
		if(count($path)>=2 && is_numeric($path[1])){
			//blog/YYYY/MM/DD
			if(isset($path[1])){
				$toReturn['year'] = $path[1];	
			}
			if(isset($path[2])){
				$toReturn['month'] = $path[2];
			}
			if(isset($path[3])){
				$toReturn['day'] = $path[3];
			}
			$toReturn['module']  = 'blog';
			$toReturn['desc']    = 'default';
			$toReturn['action']  = 'default';
			return $toReturn;
		}
		elseif(count($path)>=3 && ($path[1]=="category" || $path[1]=="tag")){
			// blog/tag/name
			// blog/category/name
			$toReturn = array();
			$toReturn['module']  = 'blog';
			$toReturn['desc']    = 'default';
			$toReturn['action']  = 'default';
			if($path[1]=="category"){
				$toReturn['heading']  = $path[2];
			}
			elseif($path[1]=="tag"){
				$toReturn['tag']  = $path[2];
			}
			
			if(isset($path[3])){
				$toReturn['year'] = $path[3];	
			}
			if(isset($path[4])){
				$toReturn['month'] = $path[4];
			}
			if(isset($path[5])){
				$toReturn['day'] = $path[5];
			}
			
			return $toReturn;
		}
		else{
			//blog/ticket/y/m/d/name
			$toReturn = array();
			$toReturn['module']  = 'blog';
			$toReturn['desc']    = 'default';
			if(isset($path[1]) && $path[1]=="ticket"){
				$toReturn['action']  = 'showticket';
				$toReturn['year']  = $path[2];
				$toReturn['month']  = $path[3];
				$toReturn['day']  = $path[4];
				$toReturn['title']  = $path[5];
			}
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
			if ($dest['module'] == 'blog' 
				&& ($dest['group'] == 'default' || $dest['group'] == '') 
				&& $dest['action'] == 'showticket') {
				if (!isset($parameters['title'])) {
					return false;
				}
				$toReturn = new stdClass ();		
				$url = array ('blog','ticket',
								$parameters['year'],
								$parameters['month'],
								$parameters['day'],
								$parameters['title']);			
				unset($parameters['title']);
				unset($parameters['year']);
				unset($parameters['month']);
				unset($parameters['day']);
				$toReturn->path = $url;				
				$toReturn->vars = $parameters;
				return $toReturn;
			}elseif ($dest['module'] == 'blog' 
				&& ($dest['group'] == 'default' || $dest['group'] == '') 
				&& ($dest['action'] == 'default' || $dest['action'] == '')) {
				
				$toReturn = new stdClass ();		
				if(isset($parameters['heading']) && strlen(trim($parameters['heading']))){
					$action = 'category';
					$val=$parameters['heading'];
					$url = array ('blog',$action,$val);
				}elseif(isset($parameters['tag']) && strlen(trim($parameters['tag']))){
					$action = 'tag';
					$val=$parameters['tag'];
					$url = array ('blog',$action,$val);
				}
				else{
					$url = array ('blog');
				}
				
				
				
				if(isset($parameters['year']) && strlen(trim($parameters['year']))){
					$url[]=$parameters['year'];	
				}
				if(isset($parameters['month']) && strlen(trim($parameters['month']))){
					$url[]=$parameters['month'];	
				}
				if(isset($parameters['day']) && strlen(trim($parameters['day']))){
					$url[]=$parameters['day'];	
				}
				
							
				unset($parameters['heading']);
				unset($parameters['tag']);
				unset($parameters['year']);
				unset($parameters['month']);
				unset($parameters['day']);
				$toReturn->path = $url;				
				$toReturn->vars = $parameters;
				return $toReturn;
			}else{
				return false;
			}
		}
	}
}
