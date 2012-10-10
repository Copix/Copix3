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
 * @subpackage	backups 
 */
class ActionGroupBackups extends CopixActionGroup {
	
	private $_pathFlagLocked = 'img/tools/flags/locked.png';
	private $_backupPath = 'modules/languages/backups/';
	
	public function beforeAction ($actionName) {
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
	
	public function __construct () {
		$this->_backupPath = COPIX_VAR_PATH . $this->_backupPath;
	}

    /**
     * Liste les fichiers sauvegardés
     */
    public function processDefault () {
    	$ppo = new CopixPPO ();
    	$ppo->TITLE_PAGE = _i18n ('global.title.backupedFiles');
    	$ppo->files = array ();
    	$ppo->savedFiles = array ();
    	
    	// recherche du module / fichier à restaurer
    	$backupModule = null;
    	$backupFile = null;
    	if (strpos (_request ('moduleFile'), '|') !== false) {
    		list ($backupModule, $backupFile) = explode ('|', _request ('moduleFile'));
    		$ppo->backupModule = $backupModule;
    		$ppo->backupFile = $backupFile;
    	}
    	
    	// recherche des modules qu'on a backupés
    	$savedFiles = array ();
    	$savedFilesTimestamp = array ();
    	if (is_dir ($this->_backupPath)) {
    		$dirBackup = opendir ($this->_backupPath);
	    	while (($module = readdir ($dirBackup)) !== false) {
	    		if (is_dir ($this->_backupPath . $module) && $module <> '.' && $module <> '..') {
	    			
	    			// recherche des fichiers backupés pour ce module
	    			$dirModule = opendir ($this->_backupPath . $module);
		 			while (($languageFile = readdir ($dirModule)) !== false) {
						if (strpos ($languageFile, '.properties.') !== false) {
							
							// recherche d'infos dans le nom du fichier
							$file = substr ($languageFile, 0, strrpos ($languageFile, '.'));
							$fileInfos = _class ('functions')->getFileInfos ($file, false);
							$saveTimeStamp = substr ($languageFile, strrpos ($languageFile, '.') + 1);
							$fileInfos->module = $module;
							$fileInfos->saveDate = date ('d/m/Y H\hi:s', $saveTimeStamp);
							$fileInfos->saveDateTimestamp = $saveTimeStamp;
							try {
								_class ('functions')->assertCanEditFile ($module, $fileInfos);
								$fileInfos->isWritable = true;
							} catch (CopixException $e) {
								$fileInfos->isWritable = false;
							}
							
							// sauvegarde des infos dans $files
							if (!isset ($files[$module]) || !in_array ($file, $files[$module])) {
								$files[$module][] = $file;
							}
							
							// si c'est ce module / fichier qu'on veut restaurer
							if ($backupModule == $module && $backupFile == $file) {
								$savedFiles[] = $fileInfos;
								$savedFilesTimestamp[] = $saveTimeStamp;
							}
						}
		    		}
	    		}
	    	}
    	}
    	
    	// tri du tableau avec tous les modules / fichiers
    	ksort ($files);
    	foreach ($files as $module => $file) {
    		sort ($files[$module]);
    	}
    	$ppo->files = $files;
    	
    	// tri du tableau avec les fichiers sauvegardés
    	rsort ($savedFilesTimestamp);
    	foreach ($savedFilesTimestamp as $timestamp) {
    		foreach ($savedFiles as $fileInfos) {
    			if ($fileInfos->saveDateTimestamp == $timestamp) {
    				$ppo->savedFiles[] = $fileInfos;
    			}
    		}
    	}
    	
    	return _arPPO ($ppo, 'backups.list.tpl');
    }
    
    public function processRestore () {
    	CopixRequest::assert ('moduleName', 'file', 'saveDateTimestamp');
    	$module = _request ('moduleName');
    	$file = _request ('file');
    	$saveDateTimestamp = _request ('saveDateTimestamp');
    	$saveDate = date ('d/m/Y H\hi:s');
    	$functions = _class ('functions');
    	
    	// si on doit afficher un message de confirmation
    	if (_request ('confirm') <> 1) {
    		return CopixActionGroup::process (
				'generictools|Messages::getConfirm',
				array (
					'message' => _i18n ('global.confirm.restoreFile', array ($file, $module, $saveDate)),
					'confirm' => _url ('backups|restore', array ('moduleName' => $module, 'file' => $file, 'saveDateTimestamp' => $saveDateTimestamp, 'confirm' => 1)),
					'cancel' => _url ('backups|', array ('moduleFile' => $module . '|' . $file))
				)
			);
    	} else {
    		$fileInfos = $functions->getFileInfos ($file);
    		$moduleInfos = CopixModule::getInformations ($module);
    		$restoreFile = COPIX_TEMP_PATH . $module . '_' . $file . '.' . $saveDateTimestamp . '.restore';
    		// sauvegarde du fichier à backuper, car backupFile delete le dernier fichier pour pouvoir créer la nouvelle sauvegarde
    		copy ($this->_backupPath . $module . '/' . $file . '.' . $saveDateTimestamp, $restoreFile);
			$functions->backupFile ($module, $fileInfos);
			copy ($restoreFile, $moduleInfos->path . $module . '/resources/' . $file);
			unlink ($restoreFile);
			
			return _arRedirect (_url ('backups|', array ('moduleFile' => $module . '|' . $file)));
    	}
    }
}
?>