<?php
/**
 * Enter description here...
 *
 */

/**
 * Actions par défaut pour le module d'exploration
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Contrôle les droits
	 */
	public function beforeProcess (){
		CopixAuth::getCurrentUser()->assertCredential ('basic:admin');
	}
	
	/**
	 *
	 */
	public function processDefault (){
		$ppo = new CopixPpo (array ('TITLE_PAGE'=>'Exploration des fichiers'));
		$ppo->arFiles = new DirectoryIterator ($ppo->basePath = CopixFile::trailingSlash (_request ('path', './')));
		return _arPpo ($ppo, 'files.php'); 
	}
	
	public function processShow (){
		return _arFile (_request ('file'));
	}
	
	public function processDownload (){
		return _arFile (_request ('file'));
	}
}
?>