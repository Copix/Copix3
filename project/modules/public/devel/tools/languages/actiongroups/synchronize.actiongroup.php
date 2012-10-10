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
 * @subpackage	synchronize 
 */
class ActionGroupSynchronize extends CopixActionGroup {
	/**
	 * Nombre de fichier properties "différents" (les fichiers de langue différente mais de même nom ne sont pas différents)
	 */
	private $_nbrPropertiesFiles = 0;
	
	/**
	 * Initialisée avant array_walk, pour pour voir executer _addFrom
	 */
	private $_arWalkAddFromLang = '';
	
	/**
	 * Exécutée avant toute action
	 */
	public function beforeAction ($actionName){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
	
	/**
	 * Affiche les options de synchronisation
	 */
    public function processDefault () {
    	$ppo = new CopixPpo ();
    	$ppo->TITLE_PAGE = _i18n ('synchronize.title.synchronize');
    	
    	// recherche des langues pour la synchronisation par langue
		$this->_nbrPropertiesFiles = 0;
		$ppo->arLanguages = $this->_getLanguages (CopixModule::getFullList (false));
		$ppo->nbrProperiesFiles = $this->_nbrPropertiesFiles;
    	
    	return _arPpo ($ppo, 'synchronize.form.tpl');
    }
    
    /**
     * Retourne les langues disponibles pour ces modules
     * 
     * @param array $pModules Résultat d'un CopixModule::getFullList
     * @return array Tableau associatif 
     */
	private function _getLanguages ($pModules) {
		$toReturn = array ();
		$arPropertiesFiles = array ();
		
		foreach ($pModules as $moduleName => $moduleDir) {
			// recherche des langues de ce module
    		$resourcesPath = $moduleDir . $moduleName . '/resources/';
    		if (is_dir ($resourcesPath)) {
	    		$dirHwnd = opendir ($resourcesPath);
	    		while (($file = readdir ($dirHwnd)) !== false) {
	    			if (strpos ($file, '.properties') !== false) {	    				
	    				$fileInfos = _class ('languages|functions')->getFileInfos ($file, false);
	    				$key = (!is_null ($fileInfos->country)) ? $fileInfos->lang . '_' . $fileInfos->country : $fileInfos->lang;
	    				$arPropertiesFiles[$moduleName . '|' . $fileInfos->baseName] = true;
	    				
	    				$iso3166 = (!is_null ($fileInfos->country)) ? strtolower ($fileInfos->country) : $fileInfos->lang;
	    				$toReturn[$key]['langName'] = $fileInfos->langName; 
	    				$toReturn[$key]['nbr'] = (!isset ($toReturn[$key]['nbr'])) ? 1 : $toReturn[$key]['nbr'] + 1;
	    			}
	    		}
    		}    		
		}
		
		$this->_nbrPropertiesFiles += count ($arPropertiesFiles);
		ksort ($toReturn);
		return $toReturn;
	}

	/**
	 * Affiche les différences entre les fichiers .properties
	 */
	public function processShowDifferences () {
		CopixRequest::assert ('baseLang', 'syncLang');
		
		$baseLang = _request ('baseLang');
		$syncLang = explode (',', _request ('syncLang'));
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('synchronize.title.showDifferences');
		
		$ppo->arDifferences = $this->_getDifferences ($syncLang, $baseLang);
		if (!is_null ($baseLang)) {
			$ppo->urlSync = _url ('synchronize|synchronizeLanguages', array ('baseLang' => $baseLang, 'syncLang' => implode (',', $syncLang)));
		} else {
			$ppo->urlSync = _url ('synchronize|synchronizeLanguages', array ('syncLang' => $syncLang));
		}
		
		return _arPpo ($ppo, 'synchronize.differences.tpl');
	}
	
	/**
	 * Ajoute FROM devant une valeur, pour pouvoir retrouver les clefs à traduire
	 */
	private function _addFrom ($value, $lang) {
		return '[FROM:' . $lang . '] ' . $value;
	}
	
	/**
	 * A appeler depusi un array_walk. Ajoute FROM devant toutes les valeurs
	 */
	private function _arWalkAddFrom (&$item, $key) {
		$item = $this->_addFrom ($item, $this->_arWalkFromAddLang);
	}
	
	/**
	 * Synchronize les langues
	 */
	public function processSynchronizeLanguages () {
		CopixRequest::assert ('baseLang', 'syncLang');
		
		$rBaseLang = _request ('baseLang');
		$arLang = explode ('_', $rBaseLang);
		$lang = $arLang[0];
		$country = (count ($arLang) == 1) ? strtoupper ($arLang[0]) : $arLang[1];
		$langCountry  = $lang;
		$langCountry .= (count ($arLang) > 1) ? '_' . $arLang[1] : '';
		$rSyncLang = explode (',', _request ('syncLang'));
		$functions = _class ('languages|functions');
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('synchronize.title.synchronize');
		
		$arDifferences = $this->_getDifferences ($rSyncLang, $rBaseLang);
		
		foreach ($arDifferences as $moduleName => $moduleInfos) {
			foreach ($moduleInfos as $fileBaseName => $fileInfos) {
				foreach ($fileInfos as $fileLang => $langInfos) {
					
					// si on ne veut pas faire les modifs sur ce fichier
					if (is_null (_request ($moduleName . '|' . $fileBaseName . '|' . $fileLang))) {
						continue;
					}
				
					$messagesBase = null;
					$changeLang = $langInfos['fileInfos']->lang;
					$changeCountry = (is_null ($langInfos['fileInfos']->country)) ? strtoupper ($langInfos['fileInfos']->lang) : $langInfos['fileInfos']->country;
					$messagesChange = CopixI18n::getBundle ($moduleName . '|' . $fileBaseName, $changeLang)->getKeys ($changeCountry);
					$writeFile = false;
										
					foreach ($langInfos['actions'] as $actionIndex => $action) {
						$arAction = explode ('=>', $action);
						$actionName = $arAction[0];
						$actionParameter = (isset ($arAction[1])) ? $arAction[1] : null;
 
						// action : supprimer le fichier
						if ($actionName == 'deleteFile') {
							$functions->deleteFile ($moduleName, $langInfos['fileInfos']);
						
						// action : tout sauf supprimer le fichier
						} else {
							$messagesBase = CopixI18n::getBundle ($moduleName . '|' . $fileBaseName, $lang)->getKeys ($country);
							
							// action : créer le fichier
							if ($actionName == 'createFile') {
								$writeFile = true;
								
								// ajout de FROM devant les valeurs, pour retrouver les clefs à traduire
								$messagesChange = $messagesBase;
								if (count ($messagesChange) > 0) {									
									$this->_arWalkFromAddLang = $langCountry;
									array_walk ($messagesChange, array ($this, '_arWalkAddFrom'));
								}
								
							// action : ajouter une clef
							} else if ($actionName == 'addKey') {
								$writeFile = true;
								$messagesChange[$actionParameter] = $this->_addFrom ($messagesBase[$actionParameter], $langCountry);
								
							
							// action : supprimer une clef
							} else if ($actionName == 'deleteKey') {
								$writeFile = true;
								unset ($messagesChange[$actionParameter]);
								
							}
						}
						
						// si on doit écrire le fichier (autres actions que deleteFile en gros)
						if ($writeFile) {
							$functions->writeFile ($moduleName, $langInfos['fileInfos'], $messagesChange);
						}
					}
				}
			}
		}
		
		$i18nKey = (count ($rSyncLang) <= 1) ? 'synchronize.confirm.syncLanguageDone' : 'synchronize.confirm.syncLanguagesDone';
		$ppo->resultMsg = _i18n ($i18nKey, array (_request ('syncLang'), $rBaseLang));
		
		return _arPpo ($ppo, 'synchronize.result.tpl');
	}
	
	/**
	 * Retourne un tableau des différences entre les langues
	 * 
	 * @param string $pLang Si null, différences globales entre tous les fichiers. Sinon, différence avec la langue $pLang
	 * @return array
	 */
	private function _getDifferences ($pSyncLang, $pBaseLang = null) {
		$modules = CopixModule::getFullList (false);
		$toReturn = array ();
		$functions = _class ('languages|functions');
		
		// si on veut synchroniser la langue mère (impossible)
		if (array_search ($pBaseLang, $pSyncLang) !== false) {
			unset ($pSyncLang[array_search ($pBaseLang, $pSyncLang)]);
		}
		
		foreach ($modules as $moduleName => $moduleDir) {
			$resourcesDir = $moduleDir . $moduleName . '/resources/';
			$properties = array ();
			
			// si on a un répertoire resources dans ce module
			if (is_dir ($resourcesDir)) {

				// recherche de tous les fichiers .properties de ce module
				$dirHwnd = opendir ($resourcesDir);
				while (($file = readdir ($dirHwnd)) !== false) {
					if (strpos ($file, '.properties') !== false) {
						$fileInfos = $functions->getFileInfos ($file, false);
						$lang = (!is_null ($fileInfos->country)) ? $fileInfos->lang . '_' . $fileInfos->country : $fileInfos->lang;
						$properties[$fileInfos->baseName . '|' . $lang] = $fileInfos;
					}
				}
				closedir ($dirHwnd);
					
				// **************************
				// si on est en mode language
				// **************************

				if (!is_null ($pBaseLang)) {
					$deleteFiles = false;
					
					// recherche de tous les messages de ce module, qui sont de la langue "mère"
					$messagesBase = array ();
					foreach ($properties as $propKey => $propInfos) {
						list ($fileBaseName, $fileLang) = explode ('|', $propKey);
						$langInfos = explode ('_', $fileLang);
						$country = (count ($langInfos) == 1) ? strtoupper ($langInfos[0]) : $langInfos[1];
						if ($fileLang == $pBaseLang) {
							$messagesBase[$fileBaseName] = CopixI18n::getBundle ($moduleName . '|' . $fileBaseName, $langInfos[0])->getKeys ($country);
						}
					}
					
					// boucle sur tous les fichiers de ce module
					foreach ($properties as $propKey => $fileInfos) {
						$actions = array ();
						list ($fileBaseName, $fileLang) = explode ('|', $propKey);
 						
						// si c'est le fichier "mère", on ne le touche pas
						if ($fileLang == $pBaseLang) {
							continue;
						
						// si cette langue n'est pas dans celle(s) que l'on veut synchroniser
						} else if (!in_array ($fileLang, $pSyncLang)) {
							continue;
						}
						
						// si on doit supprimer ce fichier
						if (!isset ($messagesBase[$fileBaseName])) {
							$actions[0] = 'deleteFile';
						
						// verification des clefs du fichier, pour les comparer avec le fichier "mère"
						} else {
							$langueCountry = (is_null ($fileInfos->country)) ? strtoupper ($fileInfos->lang) : $fileInfos->country;
							$messagesLangue = CopixI18n::getBundle ($moduleName . '|' . $fileInfos->baseName, $fileInfos->lang)->getKeys ($langueCountry);
														
							// clefs présentes dans la langue mère, mais pas dans cette langue
							$addKeys = array_diff_key ($messagesBase[$fileBaseName], $messagesLangue);
							ksort ($addKeys);
							foreach ($addKeys as $keyName => $keyValue) {
								$actions[] = 'addKey=>' . $keyName;
							}
							
							// clefs présentes dans cette langue, mais pas dans le mère
							$deleteKeys = array_diff_key ($messagesLangue, $messagesBase[$fileBaseName]);
							ksort ($deleteKeys);
							foreach ($deleteKeys as $keyName => $keyValue) {
								$actions[] = 'deleteKey=>' . $keyName;
							}
						}
						
						// si on doit faire des modifs sur ce fichier
						if (count ($actions) > 0) {
							$toReturn[$moduleName][$fileBaseName][$fileLang]['fileInfos'] = $fileInfos;
							$toReturn[$moduleName][$fileBaseName][$fileLang]['module'] = $moduleName;
							foreach ($actions as $actionIndex => $actionName) {
								$toReturn[$moduleName][$fileBaseName][$fileLang]['actions'][$actionIndex] = $actionName;
							}
						}
					}

					// boucle sur tous les fichiers de langues "mère"
					foreach ($messagesBase as $fileBaseName => $fileMessages) {
						
						// boucle sur tous les fichiers de langue qu'on "doit" avoir
						foreach ($pSyncLang as $syncLang) {
							
							// si on n'a pas cette langue dans ce module
							if (!isset ($properties[$fileBaseName . '|' . $syncLang])) {
								$fileName = ($syncLang == 'default_default') ? $fileBaseName . '.properties' : $fileBaseName . '_' . $syncLang . '.properties';
								$toReturn[$moduleName][$fileBaseName][$syncLang]['fileInfos'] = $functions->getFileInfos ($fileName, false);
								$toReturn[$moduleName][$fileBaseName][$syncLang]['module'] = $moduleName;
								$toReturn[$moduleName][$fileBaseName][$syncLang]['actions'][0] = 'createFile';
							}
						}
					}

				// ************************
				// si on est en mode global
				// ************************

				} else if ($rMode == 'global') {
				
				}
			}
		}
		
		ksort ($toReturn);
		return $toReturn;
	}
	
	/**
	 * Supprime tous les fichiers d'une langue
	 */
	public function processDelete () {
		CopixRequest::assert ('lang');
		$lang = _request ('lang');
		$confirm = _request ('confirm');
		$ppo = new CopixPpo ();
		$ppo->lang = $lang;
		$functions = _class ('languages|functions');
		
		// si la langue n'est pas au format xx ou xx_XX
		if (strlen ($lang) <> 2 && strlen ($lang) <> 5) {
			return _arRedirect (_url ('synchronize|', array ('error' => 'invalidLang')));
		}
		
		$arFiles = $this->_getFiles ($lang);
		
		// --------------------------------------------------
		// si on doit juste afficher les fichiers à supprimer
		// --------------------------------------------------

		if (is_null ($confirm)) {
			$ppo->arFiles = $arFiles;
			
			// recherche du nombre de fichiers qu'on peut supprimer, et du nombre lockés
			$toDeleteFiles = 0;
			$lockedFiles = 0;
			foreach ($arFiles as $moduleName => $moduleFiles) {
				foreach ($moduleFiles as $fileInfos) {
					try {
						$functions->assertCanEditFile ($moduleName, $fileInfos);
						$toDeleteFiles++;
					} catch (CopixException $e) {
						$lockedFiles++;
						$fileInfos->locked = true;
					}
				}
			}
			
			// création du titre de la page
			$i18nKey = ($toDeleteFiles <= 1) ? 'synchronize.title.confirmDeleteFile' : 'synchronize.title.confirmDeleteFiles';
			$titleI18n = _i18n ($i18nKey, $toDeleteFiles);
			if ($lockedFiles > 0) {
				$i18nKey = ($lockedFiles == 1) ? 'synchronize.title.fileOpened' : 'synchronize.title.filesOpened';
				$titleI18n .= ' (' . _i18n ($i18nKey, $lockedFiles) . ')';
			}
			$ppo->TITLE_PAGE = $titleI18n;
			
			return _arPpo ($ppo, 'deletelang.confirm.tpl');
		
		// ---------------------------------
		// si on doit supprimer les fichiers
		// ---------------------------------

		} else {
			$deletedFiles = 0;
			foreach ($arFiles as $moduleName => $moduleFiles) {
				foreach ($moduleFiles as $fileInfos) {
					// si on veut bien supprimer ce fichier (case cochée)
					if (!is_null (_request ('deleteFile|' . $moduleName . '|' . $fileInfos->baseName))) {
						try {
							if ($functions->deleteFile ($moduleName, $fileInfos)) {
								$deletedFiles++;
							}
						} catch (CopixException $e) {
							$arErrors[] = $e->getMessage ();
						}
					}
				}
			}
			
			// recherche des clefs de langue pour le template
			if ($deletedFiles <= 1) {
				$titleKey = 'synchronize.title.doneDeleteFile';
				$resultKey = 'synchronize.confirm.deleteFileDone';
			} else {
				$resultKey = 'synchronize.confirm.deleteFilesDone';
			 	$titleKey = 'synchronize.title.doneDeleteFiles';
			}
			
			$ppo->TITLE_PAGE = _i18n ($titleKey, array ($deletedFiles));
			$ppo->resultMsg = _i18n ($resultKey, array ($deletedFiles, $lang));
			
			return _arPpo ($ppo, 'deletelang.result.tpl');
		}
		
		return _arNone ();
	}
	
	/**
	 * Retourne tous les fichiers .properties de tous les modules de la langue $lang
	 * 
	 * @return array
	 */
	private function _getFiles ($lang) {
		$toReturn = array ();
		
		// recherche des modules
		$modules = CopixModule::getFullList (false);
		$functions = _class ('languages|functions');
		
		foreach ($modules as $moduleName => $moduleDir) {
			$resourcesDir = $moduleDir . $moduleName . '/resources/';
			
			// recherche des fichiers .properties
			$arFiles = CopixFile::search ('*' . $lang . '.properties', $resourcesDir);
			foreach ($arFiles as $fileIndex => $filePath) {
				$toReturn[$moduleName][] = $functions->getFileInfos (basename ($filePath), false);
			}
		}
		
		return $toReturn;
	}
}
?>