<?php
/**
 * @package		languages
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * @package		tools 
 * @subpackage	menu 
 */
class ActionGroupMessages extends CopixActionGroup {
	
	private $_pathFlags = 'img/flags/';
	private $_unkonwFlag = 'unknow.png';
	private $_canEditFile = false;
	private $_moduleName = null;	
	private $_fileName = null;
	private $_filePath = null;
	private $_fileDir = null;
	
	/**
	 * Exécutée avant toute action
	 */
	public function beforeAction ($actionName){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
		
		$functions = _class ('functions');
		$functions->updateLockedFiles ();
		// ces 2 paramètres sont obligatoires pour cet actiongroup
		CopixRequest::assert ('moduleName', 'file');
		$this->_moduleName = _request ('moduleName');
		$this->_fileName = _request ('file');
		$this->_fileDir = CopixModule::getPath ($this->_moduleName) . COPIX_RESOURCES_PATH;
		$this->_filePath = $this->_fileDir . $this->_fileName;
		
		// verification si on peut modifier ce fichier
		try {
			$this->_canEditFile = true;
			$functions->assertCanEditFile ($this->_moduleName, $this->_fileName);
		} catch (CopixException $e) {
			$this->_canEditFile = false;
		}
		
		// création ou mise à jour d'un lock sur ce fichier, si on peut le modifier
		if ($this->_canEditFile) {
			$functions->lockFile ($this->_moduleName, $this->_fileName);
		}
	}
	
	/**
	 * Affiche les messages contenus dans le fichier .properties
	 */
    public function processDefault () {
		$functions = _class ('functions');
    	$module = _request ('moduleName');
    	$file = _request ('file');
    	$fileInfos = $functions->getFileInfos ($file);
    	$dir = CopixModule::getPath ($module) . COPIX_RESOURCES_PATH;    	
    	    	
    	// recherche des messages du fichier à traduire
	    $messages = CopixI18n::getBundle ($module . '|' . $fileInfos->baseName, $fileInfos->lang)->getKeys ($fileInfos->country);
	    
    	$ppo = new CopixPPO ();
    	$ppo->TITLE_PAGE = '[' . $module . '] ' . $file;
    	$ppo->moduleName = $module;
    	$ppo->file = $file;
    	$fileInfos = $functions->getFileInfos ($file);
    	$ppo->mainSection = $fileInfos->baseName;
    	$ppo->filemtime = filemtime ($dir . $file);
    	
    	// verifications des erreurs passées dans l'url
    	if (_request ('error') !== null) {
    		$ppo->arErrors = array (_i18n ('global.error.' . _request ('error')));
    	} else {
    		$ppo->arErrors = array ();
    	}

    	// verification si un fichier module.xml est present dans ce repertoire, ou le precedent
    	$prev_dir = substr ($dir, 0, strrpos (substr ($dir, 0, strlen ($dir) - 1), '/') + 1);
    	if (file_exists ($dir . 'module.xml')) {
    		$moduleXml = $dir . 'module.xml';
    	} else if (file_exists ($prev_dir . 'module.xml')) {
    		$moduleXml = $prev_dir . 'module.xml';
    	} else {
    		$moduleXml = null;
    	}
    	
    	$module_description = null;
    	$module_longdescription = null;	
    	$module_caption = null;
    	
    	// ouverture du fichier module.xml
    	/*if ($moduleXml !== null) {
    		$xml = simplexml_load_file ($moduleXml);
    		if (isset ($xml->general->default)) {
    			$attributes = $xml->general->default->attributes ();
    			$module_description = (isset ($attributes['descriptioni18n'])) ? $attributes['descriptioni18n'] : null;
    			$module_longdescription = (isset ($attributes['longdescriptioni18n'])) ? $attributes['longdescriptioni18n'] : null;
    		}
    		if (isset ($xml->admin->link)) {
    			$attributes = $xml->admin->link->attributes ();
    			$module_caption = (isset ($attributes['captioni18n'])) ? $attributes['captioni18n'] : null;
    		}    		
    	}*/
    	    	
    	$messages_ordered = array ();
    	
		// tri du retour, pour l'affichage avec des sections
		foreach ($messages as $caption => $value) {
			// si ce message est une info liée avec le fichier module.xml
			if ($caption == $module_description || $caption == $module_longdescription || $caption == $module_caption) {
				$section = substr ($caption, 0, strrpos ($caption, '.'));
				$message = substr ($caption, strlen ($section) + 1);
				$messages_ordered['module.xml'][$message] = str_replace('"', '&quot;', $value);
			
			// si ce message est un message quelconque
			} else {
				// remplacement des '.' par '$', car '.' est remplace par '_' dans un submit de formulaire
				// et ça nous arrangera pas à la relecture des valeurs soumises d'avoir des '_' à la places des '.'
				$section = substr ($caption, 0, strrpos ($caption, '.'));
				$message = substr ($caption, strlen ($section) + 1);
				$messages_ordered[$section][$message] = str_replace('"', '&quot;', $value);
			}
		}		
		ksort($messages_ordered);
		foreach ($messages_ordered as $section => $message) {
			ksort ($messages_ordered[$section]);
		}		
    	$ppo->messagesOrdered = $messages_ordered;
    	
    	return _arPPO ($ppo, 'messages.form.tpl');
    }
    
    /**
     * Modifie les messages contenus dans un fichier .properties
     */
    public function processEdit () {
    	CopixRequest::assert ('section', 'new_section_name', 'filemtime');
    	$module = _request ('moduleName');
    	$file = _request ('file');
    	$fileInfos = _class ('functions')->getFileInfos ($file);
    	$dir = CopixModule::getPath ($module) . COPIX_RESOURCES_PATH;
    	$post = CopixRequest::asArray ();
    	$section = _request ('section');
    	$newSectionName = _request ('new_section_name');
    	$filemtime = _request ('filemtime');
    	$functions = _class ('functions');
    	
    	$functions->assertCanEditFile ($dir . $file);
    	
    	// recherche des messages du fichier à traduire
	    $messages = CopixI18n::getBundle ($module . '|' . $fileInfos->baseName, $fileInfos->lang)->getKeys ($fileInfos->country);

    	// suppression des anciens messages de cette section
    	foreach ($messages as $key => $value) {
    		if (substr ($key, 0, strlen ($section)) == $section) {
    			unset ($messages[$key]);
    		}
    	}
    		
    	// boucle sur les valeurs renvoyées par le formulaire
    	foreach ($post as $postKey => $postValue) {    			
    		// si c'est une clef de message
    		if (substr ($postKey, 0, 4) == 'key_') {
    			$msgKey = substr ($postKey, strpos ($postKey, '_') + 1);
    			$newMsgKey = $functions->getValidKey (_request ($postKey));
    			$messages[$newSectionName . '.' . $newMsgKey] = _request ('value_' . $msgKey);
    		}
    	}
    		
    	$functions->writeFile ($dir, $file, $messages);
    	
    	return _arRedirect (_url ('edit', array ('moduleName' => $module, 'file' => $file)));
    }
    
    /**
     * Supprime un message dans un fichier .properties
     */
    public function processDelete () {
    	CopixRequest::assert ('message', 'filemtime');
    	$module = _request ('moduleName');
    	$file = _request ('file');
    	$message = _request ('message');
    	$confirm = _request ('confirm');
    	$fileInfos = $this->_getFileInfos ($file);
    	$dir = CopixModule::getPath ($module) . COPIX_RESOURCES_PATH;
    	$filemtime = _request ('filemtime');
    	$functions = _class ('functions');
    	
    	$functions->assertCanEditFile ($dir . $file, $filemtime);    	

    	// si on doit afficher la confirmation
    	if ($confirm === null) {
    		return CopixActionGroup::process (
				'generictools|Messages::getConfirm',
				array (
					'message' => _i18n ('global.confirm.deleteMessage', array ($message, $file)),
					'confirm' => _url ('delete', array ('filemtime' => $filemtime, 'moduleName' => $module, 'file' => $file, 'message' => $message, 'confirm' => 1)),
					'cancel' => _url ('edit', array ('moduleName' => $module, 'file' => $file))
				)
			);
    	
    	// si on a confirmé
    	} else if ($confirm == 1) {
    	   	// recherche des messages du fichier à traduire
	    	$messages = CopixI18n::getBundle ($module . '|' . $fileInfos->baseName, $fileInfos->lang)->getKeys ($fileInfos->country);
    		
    		// si le message a supprimer n'existe pas
    		if (!isset ($messages[$message])) {
    			return _arRedirect (_url ('edit', array ('moduleName' => $module, 'file' => $file, 'error' => 'messageNotFound')));
    		}
    		
    		// modification du fichier
    		unset ($messages[$message]);    		
    		$this->_writeFile ($dir, $file, $messages);
    		
    		return _arRedirect (_url ('edit', array ('moduleName' => $module, 'file' => $file)));
    	} 
    	
    	// normalement on ne devrait pas arriver là
    	return _arRedirect (_url ('admin||'));
    }
    
    /**
     * Ajoute un message dans une section d'un fichier
     */
    public function processAdd () {
    	CopixRequest::assert ('key', 'value', 'filemtime');
    	$module = _request ('moduleName');
    	$file = _request ('file');
    	$fileInfos = $this->_getFileInfos ($file);
    	$dir = CopixModule::getPath ($module) . COPIX_RESOURCES_PATH;
    	$messages = CopixI18n::getBundle ($module . '|' . $fileInfos->baseName, $fileInfos->lang)->getKeys ($fileInfos->country);
    	$filemtime = _request ('filemtime');
    	$functions = _class ('functions');
    	$key = $functions->getValidKey (_request ('key'));
    	$value = trim (_request ('value'));
    	
    	$functions->assertCanEditFile ($dir . $file, $filemtime);
    	
    	// si on n'a pas indiqué de nom pour la clef du message à ajouter
    	if (substr ($key, strlen ($key) - 1) == '.') {
    		return _arRedirect (_url ('edit', array ('moduleName' => $module, 'file' => $file, 'error' => 'keyEmpty')));
    		
    	// si on n'a pas indiqué de valeur pour le message à ajouter
    	} else if (strlen ($value) == 0) {
    		return _arRedirect (_url ('edit', array ('moduleName' => $module, 'file' => $file, 'error' => 'valueEmpty')));
    	
    	// si la clef qu'on veut ajouter existe déja
    	} else if (isset ($messages[$key])) {
    		return _arRedirect (_url ('edit', array ('moduleName' => $module, 'file' => $file, 'error' => 'keyExists')));	
    	}
    	
    	// ajout du message dans le fichier
    	$messages[$key] = $value;
    	$functions->writeFile ($dir, $file, $messages);
    	
    	return _arRedirect (_url ('edit', array ('moduleName' => $module, 'file' => $file)));
    }
    
	/**
	 * Ajoute une section, et un message pour qu'on voit la section
	 */
    public function processAddSection () {
    	CopixRequest::assert ('section', 'filemtime');
    	$functions = _class ('functions');
    	$module = _request ('moduleName');
    	$file = _request ('file');
    	$fileInfos = $functions->getFileInfos ($file);
    	$messages = CopixI18n::getBundle ($module . '|' . $fileInfos->baseName, $fileInfos->lang)->getKeys ($fileInfos->country);
    	$dir = CopixModule::getPath ($module) . COPIX_RESOURCES_PATH;
    	$newSection = $functions->getValidSection (_request ('section'));
    	$filemtime = _request ('filemtime');
    	
    	$functions->assertCanEditFile ($dir . $file, $filemtime);
    	
    	// section à ajouter vide
    	if (strlen ($newSection) == 0) {
    		return _arRedirect (_url ('edit', array ('moduleName' => $module, 'file' => $file, 'error' => 'sectionEmpty')));    	
    	}
    	
    	$section = $fileInfos->baseName . '.' . $newSection;
    	
    	$messages[$section . '.deleteMe'] = 'I\'m just here to show the section. You should delete me when I won\'t be alone.';
    	$functions->writeFile ($dir, $file, $messages);
    	
    	return _arRedirect (_url ('edit', array ('moduleName' => $module, 'file' => $file)));
    }
}
?>