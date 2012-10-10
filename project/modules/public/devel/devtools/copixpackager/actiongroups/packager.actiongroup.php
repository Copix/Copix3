<?php

class ActionGroupPackager extends CopixActionGroup{
	
	private $_dirNbrFiles = 0;
	private $_dirSize = 0;
	
	public function processDefault () {		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = 'Packager la version ' . COPIX_VERSION;
		
		// recherche des modules
		$modules = CopixModule::getFullList (false);
		ksort ($modules);
		$modulesPath = CopixConfig::instance ()->arModulesPath;
		sort ($modulesPath);
		
		$ppo->modules = array ();
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
					
					$shortPath = substr ($path, strlen (COPIX_PROJECT_PATH));
					$ppo->modules[$shortPath][$moduleName]['description'] = 'Test';
					$ppo->modules[$shortPath][$moduleName]['files'] = $this->_dirNbrFiles . ' ' . $filesStr;
					$ppo->modules[$shortPath][$moduleName]['size'] = round ($this->_dirSize / 1024) . ' Ko';
				}
			}
		}
		
		// recherche des themes
		$themes = CopixTpl::getThemesList ();
		$ppo->themes = array ();
		foreach ($themes as $themeId) {
			$infos = CopixTpl::getThemeInformations ($themeId);
			$ppo->themes[] = CopixTpl::getThemeInfos ($themeId);			
		}
		
		return _arPPO ($ppo, 'packager.tpl');		
	}
	
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
}
?>