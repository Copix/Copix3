<?php
/**
 * URL Handlers for MOOCMS
 * 
 * @package MooCMS
 * @subpackage MooCMS
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * 
 */

class UrlHandlermoocms extends CopixUrlHandler {
	
	public function get($dest, $parameters, $mode){
		return false;
	}
	
	public function parse($path, $mode){
		if($mode=='none'){
			return false;
		}
		if($path[0] !="moocms"){
			return false;
		}
		$skip = array("admin","default");
		$toReturn = array();
		if(count($path)==2 && !in_array($path[1], $skip)){
			$toReturn['module'] = "moocms";
			$toReturn['group'] = "default";
			$toReturn['action'] = "showpage";
			$toReturn['title'] = $path[1];
			return $toReturn;
		}
		return false;
	}
	
}

?>