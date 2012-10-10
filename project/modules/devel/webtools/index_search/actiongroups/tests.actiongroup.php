<?php
class ActionGroupTests extends CopixActionGroup{

	const TEST_PATH = 'tests/';
	const FILETEST_NAME = 'documentTestIndex';
	
	/**
	 * Renvoie le fichier PDF pour les test
	 */
	public function processGetPDFFile (){
		
		$file = CopixModule::getPath ('index_search').
				COPIX_RESOURCES_DIR.
				self::TEST_PATH.
				self::FILETEST_NAME.
				'.pdf';
		return _arFile ($file);
	}
	
	/**
	 * Renvoie le fichier DOC pour les test
	 */
	public function processGetDOCFile (){
		
		$file = CopixModule::getPath ('index_search').
				COPIX_RESOURCES_DIR.
				self::TEST_PATH.
				self::FILETEST_NAME.
				'.doc';
		return _arFile ($file);
	}
	
	/**
	 * Renvoie le fichier TXT pour les test
	 */
	public function processGetTXTFile (){
		
		$file = CopixModule::getPath ('index_search').
				COPIX_RESOURCES_DIR.
				self::TEST_PATH.
				self::FILETEST_NAME.
				'.txt';
		return _arFile ($file);
	}
	
	/**
	 * Renvoie le fichier HTML pour les test
	 */
	public function processGetHTMLFile (){
		
		$ppo = new CopixPPO ();
		return _arDirectPPO ($ppo, 'test.tpl');
	}
}
?>