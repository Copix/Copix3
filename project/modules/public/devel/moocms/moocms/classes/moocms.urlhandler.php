<?php

class moocmsSignificantUrl extends CopixUrlHandler {
	
	public function get($dest, $parameters, $mode){
		return false;
	}
	
	public function parse($path, $mode){
		if($mode=='none'){
			return false;
		}
		if($path[0]!="moocms"){
			return false;
		}
		$skip = array("admin","default");
		$toReturn = array();
		if(!in_array($path[1],$skip) && count($path)==2){
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