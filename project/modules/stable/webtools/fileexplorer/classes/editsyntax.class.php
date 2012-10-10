<?php
class EditSyntax{
		
	private static $_arSyntax = array(
		  'bas' => 'basic'
		, 'c' => 'c'
		, 'cfm' => 'coldfusion'
		, 'cpp' => 'cpp'
		, 'css' => 'css'
		, 'html' => 'html' 
 		, 'java' => 'java'
 		, 'js' => 'js'
 		, 'pas' => 'pas'
 		, 'pl' => 'perl'
 		, 'php' => 'php'
 		, 'py' => 'python'
 		, 'txt' => 'robotstxt'
 		, 'sql' => 'sql'
 		, 'vbs' => 'vb'		
 		, 'xml' => 'xml'
 		, 'rb' => 'ruby'
	);
	
	public static function getSyntaxFromExtention($file){
		$ext = null;
		$arExt = array();
		if(preg_match('/^.*\.([^\.]+)$/', $file, $arExt)){
			$ext = $arExt[1];	
		}
		return  self::_getSyntax($ext);
	}
	
	private static function _getSyntax($extention){
		return (isset(self::$_arSyntax[$extention])) ? self::$_arSyntax[$extention] : 'html';  
	}
	
	public static function getListString(){
		$values = array_values(self::$_arSyntax);
		return join(',', $values);
	}
	
}