<?php
/**
 * @package		languages
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * @package		languages 
 * @subpackage	keys 
 */
class ActionGroupKeys extends CopixActionGroup {
	
	private $_pathFlags = 'img/tools/flags/';
	private $_unkonwFlag = 'unknow.png';
	private $_canEditFile = false;
	private $_moduleName = null;	
	private $_fileName = null;
	private $_filePath = null;
	private $_fileDir = null;
	private $_fileInfos = null;
	
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
		$this->_fileDir = CopixModule::getPath ($this->_moduleName) . 'resources/';
		$this->_filePath = $this->_fileDir . $this->_fileName;
		$this->_fileInfos = $functions->getFileInfos ($this->_fileName);
		
		// verification si on peut modifier ce fichier
		try {
			$this->_canEditFile = true;
			$functions->assertCanEditFile ($this->_moduleName, $this->_fileInfos, _request ('filemtime'));
		} catch (CopixException $e) {
			$this->_canEditFile = $e->getMessage ();
		}
		
		// création ou mise à jour d'un lock sur ce fichier, si on peut le modifier
		if ($this->_canEditFile) {
			$functions->lockFile ($this->_moduleName, $this->_fileInfos);
		}
	}
	
	/**
	 * Affiche les messages contenus dans le fichier .properties
	 */
    public function processDefault () {
		$functions = _class ('functions');
		$modulePath = CopixModule::getPath ($this->_moduleName);
     	    	
    	// recherche des messages du fichier à traduire
	    $messages = CopixI18n::getBundle ($this->_moduleName . '|' . $this->_fileInfos->baseName, $this->_fileInfos->lang)->getKeys ($this->_fileInfos->country);
	    
    	$ppo = new CopixPPO ();
    	$ppo->TITLE_PAGE = '[' . $this->_moduleName . '] ' . $this->_fileName;
    	$ppo->moduleName = $this->_moduleName;
    	$ppo->file = $this->_fileName;
    	$fileInfos = $functions->getFileInfos ($this->_fileName);
    	$ppo->mainSection = $this->_fileInfos->baseName;
    	$ppo->filemtime = filemtime ($this->_filePath);
    	$ppo->canEditFile = ($this->_canEditFile === true);
    	
    	// verifications des erreurs passées dans l'url
    	if (_request ('error') !== null) {
    		$ppo->arErrors = array (_i18n ('global.error.' . _request ('error')));
    	} else {
    		$ppo->arErrors = array ();
    	}

    	// verification si un fichier module.xml est present dans ce repertoire, ou le precedent
    	$moduleXml = (file_exists ($modulePath . 'module.xml')) ? $modulePath . 'module.xml' : null;
    	
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
    	
    	return _arPPO ($ppo, 'keys.form.tpl');
    }
    
    /**
     * Modifie les messages contenus dans un fichier .properties
     */
    public function processEdit () {
    	// si on ne peut pas modifier ce fichier
    	if ($this->_canEditFile !== true) {
			return _arRedirect (_url ('keys|',  array ('moduleName' => $this->_moduleName, 'file' => $this->_fileName, 'error' => $this->_canEditFile)));
    	}
    	
    	CopixRequest::assert ('section', 'new_section_name', 'filemtime');
    	$functions = _class ('functions');
    	
    	$post = CopixRequest::asArray ();
    	$section = _request ('section');
    	$newSectionName = $functions->getValidSection (_request ('new_section_name'));
    	$filemtime = _request ('filemtime');
    	    	
    	// recherche des messages du fichier à traduire
	    $messages = CopixI18n::getBundle ($this->_moduleName. '|' . $this->_fileInfos->baseName, $this->_fileInfos->lang)->getKeys ($this->_fileInfos->country);

    	// suppression des anciens messages de cette section
    	foreach ($messages as $key => $value) {
    		$posEnd = (strrpos ($key, '.') !== false) ? strrpos ($key, '.') : strlen ($key);
    		$thisSection = substr ($key, 0, $posEnd);
    		if ($thisSection == $section) {
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
    		
    	$functions->writeFile ($this->_moduleName, $this->_fileInfos, $messages);
    	
    	return _arRedirect (_url ('keys|', array ('moduleName' => $this->_moduleName, 'file' => $this->_fileName)));
    }
    
    /**
     * Supprime un message dans un fichier .properties
     */
    public function processDelete () {
    	// si on ne peut pas modifier ce fichier
    	if ($this->_canEditFile !== true) {
			return _arRedirect (_url ('keys|',  array ('moduleName' => $this->_moduleName, 'file' => $this->_fileName, 'error' => $this->_canEditFile)));
    	}
    	
    	CopixRequest::assert ('message', 'filemtime');
    	$functions = _class ('functions');
    	$message = _request ('message');
    	$confirm = _request ('confirm');
    	$filemtime = _request ('filemtime');

    	// si on doit afficher la confirmation
    	if ($confirm === null) {
    		return CopixActionGroup::process (
				'generictools|Messages::getConfirm',
				array (
					'message' => _i18n ('global.confirm.deleteMessage', array ($message, $this->_fileName)),
					'confirm' => _url ('keys|delete', array ('filemtime' => $filemtime, 'moduleName' => $this->_moduleName, 'file' => $this->_fileName, 'message' => $message, 'confirm' => 1)),
					'cancel' => _url ('keys|', array ('moduleName' => $this->_moduleName, 'file' => $this->_fileName))
				)
			);
    	
    	// si on a confirmé
    	} else if ($confirm == 1) {
    	   	// recherche des messages du fichier à traduire
	    	$messages = CopixI18n::getBundle ($this->_moduleName. '|' . $this->_fileInfos->baseName, $this->_fileInfos->lang)->getKeys ($this->_fileInfos->country);
    		
    		// si le message à supprimer n'existe pas
    		if (!isset ($messages[$message])) {
    			return _arRedirect (_url ('keys|', array ('moduleName' => $this->_moduleName, 'file' => $this->_fileName, 'error' => 'messageNotFound')));
    		}
    		
    		// modification du fichier
    		unset ($messages[$message]);    		
    		$functions->writeFile ($this->_moduleName, $this->_fileInfos, $messages);
    		
    		return _arRedirect (_url ('keys|', array ('moduleName' => $this->_moduleName, 'file' => $this->_fileName)));
    	} 
    	
    	// normalement on ne devrait pas arriver là
    	return _arRedirect (_url ('admin||'));
    }
    
    /**
     * Ajoute un message dans une section d'un fichier
     */
    public function processAdd () {
    	// si on ne peut pas modifier ce fichier
    	if ($this->_canEditFile !== true) {
			return _arRedirect (_url ('keys|',  array ('moduleName' => $this->_moduleName, 'file' => $this->_fileName, 'error' => $this->_canEditFile)));
    	}
    	
    	CopixRequest::assert ('key', 'value', 'filemtime');
    	$functions = _class ('functions');
    	$messages = CopixI18n::getBundle ($this->_moduleName. '|' . $this->_fileInfos->baseName, $this->_fileInfos->lang)->getKeys ($this->_fileInfos->country);
    	$filemtime = _request ('filemtime');
    	
    	$key = $functions->getValidKey (_request ('key'));
    	$value = trim (_request ('value'));
    	    	
    	// si on n'a pas indiqué de nom pour la clef du message à ajouter
    	if (substr ($key, strlen ($key) - 1) == '.') {
    		return _arRedirect (_url ('keys|', array ('moduleName' => $this->_moduleName, 'file' => $this->_fileName, 'error' => 'keyEmpty')));
    		
    	// si on n'a pas indiqué de valeur pour le message à ajouter
    	} else if (strlen ($value) == 0) {
    		return _arRedirect (_url ('keys|', array ('moduleName' => $this->_moduleName, 'file' => $this->_fileName, 'error' => 'valueEmpty')));
    	
    	// si la clef qu'on veut ajouter existe déja
    	} else if (isset ($messages[$key])) {
    		return _arRedirect (_url ('keys|', array ('moduleName' => $this->_moduleName, 'file' => $this->_fileName, 'error' => 'keyExists')));	
    	}
    	
    	// ajout du message dans le fichier
    	$messages[$key] = $value;
    	$functions->writeFile ($this->_moduleName, $this->_fileInfos, $messages);
    	
    	return _arRedirect (_url ('keys|', array ('moduleName' => $this->_moduleName, 'file' => $this->_fileName)));
    }
    
	/**
	 * Ajoute une section, et un message pour qu'on voit la section
	 */
    public function processAddSection () {
    	// si on ne peut pas modifier ce fichier
    	if ($this->_canEditFile !== true) {
			return _arRedirect (_url ('keys|',  array ('moduleName' => $this->_moduleName, 'file' => $this->_fileName, 'error' => $this->_canEditFile)));
    	}
		
		CopixRequest::assert ('section', 'filemtime');
    	$functions = _class ('functions');
    	$messages = CopixI18n::getBundle ($this->_moduleName. '|' . $this->_fileInfos->baseName, $this->_fileInfos->lang)->getKeys ($this->_fileInfos->country);
    	$newSection = $functions->getValidSection (_request ('section'));
    	$newSectionFull = $this->_fileInfos->baseName . '.' . $newSection;
    	$filemtime = _request ('filemtime');

    	// section à ajouter vide
    	if (strlen ($newSection) == 0) {
    		return _arRedirect (_url ('keys|', array ('moduleName' => $this->_moduleName, 'file' => $this->_fileName, 'error' => 'sectionEmpty')));    	
    	}
    	
    	$section = $this->_fileInfos->baseName . '.' . $newSection;
    	
    	$messages[$section . '.deleteMe'] = 'I\'m just here to show the section. You should delete me when I won\'t be alone.';
    	$functions->writeFile ($this->_moduleName, $this->_fileInfos, $messages);
    	
    	return _arRedirect (_url ('keys|default', array ('moduleName' => $this->_moduleName, 'file' => $this->_fileName)));
    }
}
?>