<?php

class ActionGroupPackager extends CopixActionGroup{
	
	private $_dirNbrFiles = 0;
	private $_dirSize = 0;
	private $_themes = array ();
	
	/**
	 * Options de package
	 */
	public function processDefault () {		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = 'Packager la version ' . COPIX_VERSION;
		$ppo->packageName = 'copix_' . strtolower (str_replace (' ', '_', str_replace ('.', '_', COPIX_VERSION)));
		
		// recherche des modules
		$modules = CopixModule::getFullList (false);
		ksort ($modules);
		
		$ppo->modules = array ();
		$modulesPath = CopixConfig::instance ()->arModulesPath;
		sort ($modulesPath);
		foreach ($modulesPath as $path) {
			foreach ($modules as $moduleName => $modulePath) {
				if ($path == $modulePath) {
					
					// recherche des fichiers et de la taille du module
					$dirHwnd = opendir ($modulePath . $moduleName);
					$stat = stat ($modulePath . $moduleName);
					$this->_dirSize = 0;
					$this->_dirNbrFiles = 0;
					$this->_get_dir_infos ($modulePath . $moduleName);
					$filesStr =  ($this->_dirNbrFiles <= 1) ? 'fichier' : 'fichiers';
					
					$moduleInfos = CopixModule::getInformations ($moduleName);
					$ppo->modules[$path][$moduleName]['description'] = $moduleInfos->description;
					$ppo->modules[$path][$moduleName]['files'] = $this->_dirNbrFiles . ' ' . $filesStr;
					$ppo->modules[$path][$moduleName]['size'] = round ($this->_dirSize / 1024) . ' Ko';
				}
			}
		}
		
		// recherche des themes
		$themes = CopixTpl::getThemesList ();
		$ppo->themes = array ();
		foreach ($themes as $themeId) {
			//$infos = CopixTpl::getThemeInformations ($themeId);
			$ppo->themes[] = CopixTpl::getThemeInformations ($themeId);			
		}
		
		return _arPPO ($ppo, 'packager.tpl');		
	}
	
	/**
	 * Retourne des infos sur un répertoire, dans les propriétés _dirNbrFiles et _dirSize
	 */
	private function _get_dir_infos ($pDir) {
    	$handle = opendir ($pDir);
   
    	while ($file = readdir ($handle)) {
        	if ($file != '..' && $file != '.') {
        		$dir = $pDir . '/' . $file;
        		if (!is_dir($dir)) {
        			//echo '[' . $dir . '] [' . $file . ']<br />';
        			$this->_dirNbrFiles++;
            		$this->_dirSize += filesize ($dir);
            	} else if (strpos ($dir, '.svn') === false) {
            		$this->_get_dir_infos ($dir);
        		}
        	}
        }
	}
	
	/**
	 * Créé le package avec les options demandées
	 */
	public function processMake () {
		$tempDir = COPIX_TEMP_PATH . 'copixpackager/';
		CopixFile::removeDir ($tempDir);
		CopixFile::createDir ($tempDir);
		$this->_themes = array ('default');
		$fileName = COPIX_TEMP_PATH . _request ('packageName');
		
		$post = CopixRequest::asArray ();
		foreach ($post as $key => $value) {
			// si c'est un template à intégrer au package
			if (substr ($key, 0, 9) == 'checktpl_') {
				$this->_themes[] = substr ($key, 9);
			}
		}
		
		// copie des fichiers indispensables + themes
		echo 'Copie des fichiers ';
		flush ();
		
		$this->_copyDir (COPIX_PATH, $tempDir . 'utils/copix/');
		$this->_copyDir (COPIX_CORE_PATH, $tempDir . 'utils/copix/core/');
		$this->_copyDir (COPIX_UTILS_PATH, $tempDir . 'utils/copix/utils/');
		$this->_copyDir (COPIX_SMARTY_PATH, $tempDir . 'utils/smarty/');
		$this->_copyDir (COPIX_PROJECT_PATH . 'config/', $tempDir . 'project/config/');
		$this->_copyDir (COPIX_PROJECT_PATH . 'themes/', $tempDir . 'project/themes/');
		copy (COPIX_PROJECT_PATH . 'project.inc.php', $tempDir . 'project/project.inc.php');
		// on a certains répertoires qui n'ont pas de constantes, mais dont les fichiers sont inclus comme ici
		$this->_copyDir (realpath (COPIX_PATH . '..') . '/', $tempDir . 'utils/');
		$this->_copyDir (getcwd () . '/', $tempDir . 'www/');
		
		// copie des modules sélectionnés
		$modules = CopixModule::getFullList (false);
		foreach ($modules as $moduleName => $modulePath) {
			if (!is_null (_request ('checkModule_' . $moduleName))) {
				$dirs = explode ('/', $modulePath);
				$copyPath = $tempDir . 'project/modules/' . $dirs[count ($dirs) - 3] . '/' . $dirs[count ($dirs) - 2] . '/' . $dirs[count ($dirs) - 1] . '/' . $moduleName . '/';
				$this->_copyDir ($modulePath . $moduleName . '/', $copyPath);
			}
		}
		
		echo '<font color="green">OK</font>';
		flush ();
		
		// création des répertoires que l'on ne veut pas copier, mais qui doivent exister
		CopixFile::createDir ($tempDir . 'temp/');
		CopixFile::createDir ($tempDir . 'var/');
		
		// création des archives
		if (!is_null (_request ('compressZip'))) {
			echo '<br />Création de l\'archive .zip ';
			flush ();
			//exec ($this->_getCmd (CopixConfig::get ('copixpackager|cmdZip'), $fileName . '.zip', $tempDir));
			echo '<font color="green">OK</font>';
			flush ();
		}
		if (!is_null (_request ('compressTarGz'))) {
			echo '<br />Création de l\'archive .tar.gz ';
			flush ();
			//exec ($this->_getCmd (CopixConfig::get ('copixpackager|cmdTarGz'), $fileName . '.tar.gz', $tempDir));
			echo '<font color="green">OK</font>';
			flush ();
		}
		
		echo '<br />Suppression des fichiers copiés ';
		flush ();
		//CopixFile::removeDir ($tempDir);
		echo '<font color="green">OK</font>';
		flush ();
						
		return _arNone ();
	}
	
	private function _getCmd ($pCmd, $pFile, $pDir) {
		$toReturn = str_replace ('#FILE#', $pFile, $pCmd);
		$toReturn = str_replace ('#DIR#', $pDir, $toReturn);
		
		return $toReturn;
	}
	
	/**
	 * Copie un répertoire et ses enfants
	 * 
	 * @param string $pBaseDir Répertoire à copier
	 * @param string $pDestDir Répertoire de destination
	 */
	private function _copyDir ($pBaseDir, $pDestDir) {
		// création du répertoire
		CopixFile::createDir ($pDestDir);
		
		// scan des fichiers / répertoires
		$dir = opendir ($pBaseDir);
		while (($file = readdir ($dir)) !== false) {
			if ($file <> '.' && $file <> '..' && strpos ($file, '.svn') === false) {
				$fullDir = $pBaseDir . $file;
				
				// si on a trouvé un répertoire
				if (is_dir ($fullDir)) {
					$copyDir = true;
					
					// si on est dans un répertoire de theme, on vérifie qu'on doit le copier
					$posThemes = strpos ($fullDir, '/themes/');
					if ($posThemes !== false) {
						$themeName = substr ($fullDir, $posThemes + 8);
						
						// si c'est la racine d'un thème en particulier
						if (strpos ($themeName, '/') === false) {
							$copyDir = (in_array ($file, $this->_themes));
						}
					}
					
					// si on doit bien copier ce répertoire
					if ($copyDir) {
						$this->_copyDir ($fullDir . '/', $pDestDir . $file . '/');
					}
				
				// si on a trouvé un fichier
				} else if (is_file ($fullDir)) {
					copy ($fullDir, $pDestDir . $file);
				}
			}
		}
	}
}
?>
