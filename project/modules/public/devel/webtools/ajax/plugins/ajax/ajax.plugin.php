<?php
class PluginAjax extends CopixPlugin {
	/**
	 * Before process, if "byajax" is given in url, we change the main template to blank
	 * to get only main and headers (css, javascripts...)
	 * 
	 */
	function beforeProcess(& $action){
		if(in_array(CopixRequest::get('byajax',false),array('1',"true"))){
			$config = CopixConfig::instance();
			$config->mainTemplate = "ajax|blank.tpl";
		}
	}
	
	
}
?>