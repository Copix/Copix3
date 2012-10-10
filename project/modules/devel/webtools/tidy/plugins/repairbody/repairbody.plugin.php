<?php
/**
 * Plugin to repair only body of XHTML content
 *
 */
class PluginRepairBody extends CopixPlugin {
	public function beforeDisplay(&$display){
		$config = array();
		$config["show-body-only"]=true;
		$config["indent"]=true;
		$display = str_replace('<$HTML_HEAD />','<!-- COPIXHTMLHEAD -->',$display);
		$html=tidy_repair_string($display,$config,"utf8");
		$html=trim($html);
		//var_dump($html);
		
		$display = preg_replace('/<\/head>(.*?)<body(.*?)>(.*?)<\/body>/s','</head>'."\n".'<body\\2>'.$html.'</body>',$display);
		$display = str_replace('<!-- COPIXHTMLHEAD -->','<$HTML_HEAD />',$display);
		
	}
}
?>