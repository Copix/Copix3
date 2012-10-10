<?php
class ActionGroupLoader extends CopixActionGroup{

	public function processJsCode(){
		if(CopixConfig::get('htmlheader|gzcompress')=='yes'){
			@ob_clean();
			@ob_end_clean();
			if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){
				ob_start ("ob_gzhandler");
			}
			header("Content-type: text/javascript; charset: UTF-8");
			header("Cache-Control: must-revalidate");
			$offset = Copixconfig::get('htmlheader|expire');
			$ExpStr = "Expires: " .	gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
			header($ExpStr);
		}
		
		$content = CopixSession::get ("copix:jscode");
		CopixSession::delete ("copix:jscode");
		return _arContent ($content);
	}

	
	public function processCss(){
		if(CopixConfig::get('htmlheader|gzcompress')=='yes'){
			@ob_clean();
			@ob_end_clean();
			
			if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){
				ob_start ("ob_gzhandler");
			}
			header("Content-type: text/css; charset: UTF-8");
			header("Cache-Control: must-revalidate");
			$offset = Copixconfig::get('htmlheader|expire');
			$ExpStr = "Expires: " .	gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
			header($ExpStr);
		}
				
		$content = CopixSession::get ("copix:css");
		CopixSession::delete ("copix:css");
		return _arContent ($content);
	}
}
?>