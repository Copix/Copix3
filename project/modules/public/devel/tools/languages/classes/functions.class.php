<?php
/**
 * @package		languages
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

class Functions {
	
	private $_pathFlags = 'img/tools/flags/';
	private $_pathFlagLocked = 'img/tools/flags/locked.png';
	private $_unkonwFlag = 'unknow.png';
	
	/**
	 * Remet à jour la liste des fichiers lockés
	 */
	public function updateLockedFiles () {
		_ioDao ('languageslocks')->deleteBy (
			_daoSP ()->addCondition ('time_lock', '<=', (mktime () - (CopixConfig::get ('languages|lockWaitTimeOut') * 60)))
		);
	}
	
	/**
	 * Lock un fichier en modification
	 */
	public function lockFile ($pModule, $pFileInfos) {
		// recherche d'un éventuel lock sur ce fichier déja existant
		$lock = _ioDao ('languageslocks')->findBy (
			_daoSP ()
				->addCondition ('module_lock', '=', $pModule)
				->addCondition ('file_lock', '=', $pFileInfos->name)
		);
		
		// si on n'a pas de lock sur ce fichier
		if (count ($lock) == 0) {
			$lockedFile = _record ('languageslocks');
			$lockedFile->id_dbuser = CopixAuth::getCurrentUser ()->getId ();
			$lockedFile->id_session = session_id ();
			$lockedFile->module_lock = $pModule;
			$lockedFile->file_lock = $pFileInfos->name;
			$lockedFile->time_lock = mktime ();
			_ioDao ('languageslocks')->insert ($lockedFile);
		}
	}
    
    /**
     * Ecrase ou cré un fichier
     * 
     * @param string $dir lien relatif depuis /www/ vers le repertoire du fichier
     * @param string $file fichier à modifier / créer
     * @param array $messages messages à écrire dans le fichier (clef = nom du message, valeur = texte du message)
     */
    public function writeFile ($pModuleName, $pFileInfos, $pMessages) {
    	try {
    		$this->assertCanEditFile ($pModuleName, $pFileInfos);
    	} catch (CopixException $e) {
    		if ($e->getMessage () <> 'fileNotFound') {
    			throw new CopixException ($e->getMessage ());
    		}
    	}
    	$this->backupFile ($pModuleName, $pFileInfos);
    
    	$filePath = CopixModule::getPath ($pModuleName) . 'resources/' . $pFileInfos->name;
    	
    	// réécriture du fichier    	
    	$fileHwnd = fopen ($filePath, 'w');
    	foreach ($pMessages as $key => $value) {
    		fwrite ($fileHwnd, $key . ' = ' . $value . "\n");
    	}
    	fclose ($fileHwnd);
    }
    
    /**
     * Effectue une sauvegarde du fichier selon la configuration
     */
    public function backupFile ($pModuleName, $pFileInfos) {
    	// si on a configuré un backup
    	if (CopixConfig::get ('languages|nbrBackupFiles') > 0) {
    		// création du répertoire des backups
    		$backupDir = COPIX_VAR_PATH . 'modules/languages/backups/' . $pModuleName . '/';
    		$file = new CopixFile;
    		$file->createDir ($backupDir);
    		
    		// recherche des backups de ce fichier .properties
    		$search = $file->search ($pFileInfos->baseName . '*', $backupDir, false);
    		rsort ($search);
    		
    		// suppression des fichiers "en trop", selon la config "nbrBackupFiles"
    		if (count ($search) >= CopixConfig::get ('languages|nbrBackupFiles')) {
    			for ($boucle = CopixConfig::get ('languages|nbrBackupFiles') - 1; $boucle < count ($search); $boucle++) {
    				unlink ($search[$boucle]);
    			}
    		}
    		
    		// création du backup
			$filePath = CopixModule::getPath ($pModuleName) . 'resources/';
			if (file_exists ($filePath . $pFileInfos->name)) {
    			copy ($filePath . $pFileInfos->name, $backupDir . $pFileInfos->name . '.' . mktime ());
			}
    	}
    }
    
    /**
     * Supprime un fichier properties
     */
	public function deleteFile ($pModuleName, $pFileInfos) {
		$this->assertCanEditFile ($pModuleName, $pFileInfos);
		$this->backupFile ($pModuleName, $pFileInfos);
		
		$filePath = CopixModule::getPath ($pModuleName) . 'resources/' . $pFileInfos->name;
		if (is_file ($filePath)) {
			unlink ($filePath);
			return true;
		}
		
		return false;
	}
    
    /**
     * Renvoie une clef de message valide
     * 
     * @param string $key Clef à vérifier, et modifier si nécessaire
     * @return string Clef valide
     */
    public function getValidKey ($pKey) {
    	return str_replace (' ', '', $pKey);
    }
    
    /**
     * Renvoie un nom de section valide
     * 
     * @param string $section Section à vérifier, et modifier si nécessaire
     * @return string Section valide
     */
    public function getValidSection ($pSection) {
    	return str_replace (' ', '', $pSection);
    }
    
    /**
     * Assure qu'un fichier peut être modifié
     * 
     * @param string $file Fichier à vérifier
     * @param int $filemtime TimeStamp de la modif que l'on veut comparer
     * @return bool True, ou Exception si fichier non trouvé, lock sur le fichier, ou date de modif différente de $filemtime
     */
    public function assertCanEditFile ($pModuleName, $pFileInfos, $pFilemtime = null) {
    	$filePath = CopixModule::getPath ($pModuleName) . 'resources/' . $pFileInfos->name;
    	
    	// si le fichier n'existe pas
    	if (!file_exists ($filePath)) {
    		throw new CopixException ('fileNotFound');
    	
    	// si le fichier existe
    	} else {
    	
    		// recherche des fichiers lockés
    		$locks = _ioDao ('languageslocks')->findBy (
    			_daoSP ()
    				->addCondition ('module_lock', '=', $pModuleName)
    				->addCondition ('file_lock', '=', $pFileInfos->name)
    		);
    		foreach ($locks as $lockIndex => $lockInfos) {
    			if ($lockInfos->id_session != session_id ()) {
    				throw new CopixException ('fileLocked');
    			}
    		}
    		
    		// date de dernière modification différente de celle à comparer
    		if ($pFilemtime !== null && $pFilemtime <> filemtime ($filePath)) {
    			throw new CopixException ('notAssertFileMTime');
    		
    		// droit d'écriture insuffisant
    		} else if (!is_writable ($filePath)) {
    			throw new CopixException ('fileWriteRight');
    			    			
    		// fichier dispo à la modification
    		} else {
    			return true;
    		}
    	}
    }
    
    /**
	 * Retourne une class avec des infos prises dans le nom du fichier
	 * 
	 * @param string $pFile Nom du fichier uniquement
	 * @param bool $pLangToCountry Remplit automatiquement le country par lang si country n'est pas indiqué
	 * @return stdclass
	 */
	public function getFileInfos ($pFile, $pLangToCountry = true) {
		$fileBase = substr($pFile, 0, (strlen($pFile) - strlen('.properties')));
		$array = explode('_', $fileBase);
		
		$toReturn->name = $pFile;
		
		// fichier de la forme "monNom.properties" (langue en)
		if (count($array) == 1 || strlen ($array[count ($array) - 1]) <> 2) {
			$toReturn->baseName = implode ('_', $array);
			$toReturn->lang = 'default';
			$toReturn->country = 'default';
			$toReturn->flag = _resource ('img/tools/flags/default.png');
			$toReturn->langCountry = 'default_default';

		// fichier de la forme "monNom_xx.properties"
		} else if (count ($array) >= 2 && strlen ($array[count ($array) - 1]) == 2 && strlen ($array[count ($array) - 2]) <> 2) {
			$arrayName = $array;
			unset ($arrayName[count ($arrayName) - 1]);
			$toReturn->baseName = implode ('_', $arrayName);
			$toReturn->lang = $array[count ($array) - 1];
			$toReturn->country = ($pLangToCountry) ? strtoupper ($toReturn->lang) : null;
			$toReturn->flag = _resource ('img/tools/flags/' . $toReturn->lang . '.png');
			$toReturn->langCountry = $toReturn->lang;
			if (!is_null ($toReturn->country)) {
				$toReturn->langCountry .= '_' . $toReturn->country;
			}
		
		// fichier de la forme "monNom_xx_XX.properties"
		} else if (count ($array) >= 3 && strlen ($array[count ($array) - 1]) == 2 && strlen ($array[count ($array) - 2]) == 2) {
			$arrayName = $array;
			unset ($arrayName[count ($arrayName) - 1]);
			unset ($arrayName[count ($arrayName) - 1]);
			$toReturn->baseName = implode ('_', $arrayName);
			$toReturn->lang = $array[count ($array) - 2];
			$toReturn->country = $array[count ($array) - 1];
			$toReturn->flag = _resource ('img/tools/flags/' . strtolower ($toReturn->country) . '.png');
			$toReturn->langCountry = $toReturn->lang . '_' . $toReturn->country;
			
		// format de fichier incorrect
		} else {
			throw new CopixException ('File name "' . $pFile . '" is not a valid properties file name.');
		}
		
		try {
			$i18nKey = (is_null ($toReturn->country)) ? $toReturn->lang : strtolower ($toReturn->country);
			$toReturn->langName = _i18n ('iso3166.' . $i18nKey);
		} catch (CopixException $e) {
			$toReturn->langName = _i18n ('iso3166.unknow');
		}

		return $toReturn;
	}
	
	/**
     * Recherche les fichiers .properties
     * 
     * @param bool $pGetInstalled Rechercher dans les modules installés
     * @param bool $pGetUninstalled Rechercher dans les modules non installés
     * @return array
     */
    public function getFiles ($pGetInstalled, $pGetUninstalled) {
    	$installedModules = CopixModule::getFullList (true);
    	ksort ($installedModules);
    	
    	$uninstalledModules = CopixModule::getFullList (false);
	    $uninstalledModules = array_diff_key ($uninstalledModules, $installedModules);
	    ksort ($uninstalledModules);
	    
	    $toReturn = array ();
    	
    	// recherche des fichiers .properties dans les modules installés
    	if ($pGetInstalled && !$pGetUninstalled) {
	    	$toReturn = $this->_getLngInfos ($installedModules);

    	// recherche des fichiers .properties dans les modules installés et non installés
    	} else if ($pGetUninstalled && $pGetInstalled) {
	    	$toReturn = array_merge ($this->_getLngInfos ($installedModules), $this->_getLngInfos ($uninstalledModules));
    	
    	// recherche des fichiers .properties dans les modules non installés uniquement
    	} else if ($pGetUninstalled && !$pGetInstalled) {
    		$toReturn = $this->_getLngInfos ($uninstalledModules);
    	}
    	
    	return $toReturn;
    }
    
    /**
     * Récupère des infos sur les fichiers .properties de modules
     * 
     * @param array $modules Retour d'un CopixModule::getFullList
     * @return array
     */
    private function _getLngInfos ($modules) {
    	$functions = _class ('functions');
    	$toReturn = array ();
    	
    	foreach ($modules as $module_name => $module_dir) {
    		$module_dir = $module_dir . $module_name . '/';
    		$module_infos = CopixModule::getInformations ($module_name);
    		$module_title = '[' . $module_name . '] ' . $module_infos->description;
    		
    		// recherche des langues de ce module
    		$resourcesPath = $module_dir . 'resources/';
    		if (is_dir ($resourcesPath)) {
	    		$dirHwnd = opendir ($resourcesPath);
	    		while (($file = readdir ($dirHwnd)) !== false) {
	    			if (strpos ($file, '.properties') !== false) {
	    				$fileInfos = $functions->getFileInfos ($file);	
	    				try {
	    					$langueIcon = $this->_pathFlags . strtolower ($fileInfos->country) . '.png';
	    					$functions->assertCanEditFile ($module_name, $fileInfos);
	    				} catch (CopixException $e) {
	    					$langueIcon = $this->_pathFlagLocked;
	    				}
	    				
						if (file_exists (_resourcePath ($langueIcon))) {
							$iconPath = _resource ($langueIcon);
						} else {
							$iconPath = _resource ($this->_pathFlags . $this->_unkonwFlag);
						}
	    				
	    				$toReturn[$module_name]['title'] = $module_title;
	    				$toReturn[$module_name]['icon'] = $module_infos->icon;
	    				$toReturn[$module_name]['file_' . $fileInfos->baseName][$fileInfos->lang . '_' . $fileInfos->country]['fileName'] = $file;
	    				$toReturn[$module_name]['file_' . $fileInfos->baseName][$fileInfos->lang . '_' . $fileInfos->country]['icon'] = $iconPath;
	    				$toReturn[$module_name]['file_' . $fileInfos->baseName][$fileInfos->lang . '_' . $fileInfos->country]['isWritable'] = true; 
	    			}
	    		}
    		}
    		
    		// tri des langues, pour les avoir toujours dans le même ordre
    		// sinon sous linux, on a un ordre un peu "aléatoire"
    		foreach ($toReturn as $moduleName => $moduleInfos) {
    			foreach ($moduleInfos as $moduleFile => $moduleLangues) {
    				if (substr ($moduleFile, 0, 5) == 'file_') {
    					ksort ($moduleLangues);
    					unset ($toReturn[$moduleName][$moduleFile]);
    					$toReturn[$moduleName][$moduleFile] = $moduleLangues;
    				}
    			}
    		}
    	}
    	
    	return $toReturn;
    }
}
?>