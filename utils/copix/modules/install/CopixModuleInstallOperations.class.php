<?php
class CopixModuleInstallOperations {
    /**
     * Liste des dependance a installer si on install le module
     *
     * @param string $pModuleName Nom du module
     * @param mixed $arDependencies tableau de dépendances permettant de concatener les sous dépendance
     * @return mixed Tableau des dépendances
     */
    public static function getDependenciesForInstall ($moduleName, $arDependencies = array (), $pLevel = 0) {
    	$toCheck = CopixModule::getInformations ($moduleName);
        $moduleDependency = new stdClass ();
        $moduleDependency->level = $pLevel;
        $moduleDependency->name = $moduleName;
   	    $moduleDependency->kind = 'module';
   	    $arDependencies['module_'.$moduleName] = $moduleDependency;
   	    foreach($toCheck->getDependencies () as $dependency){
   	        if (!isset ($arDependencies[$dependency->kind.'_'.$dependency->name])) {
   	            $dependency->level = $pLevel+1;
   	            $arDependencies[$dependency->kind.'_'.$dependency->name] = $dependency;
   	            if ($dependency->kind === 'module')     {
   	                if (! in_array ($dependency->name, CopixModule::getList (true))) {
   	                    if (in_array ($dependency->name, CopixModule::getList (false))) {
   	                        $arDependencies = array_merge ($arDependencies, self::getDependenciesForInstall ($dependency->name,$arDependencies, $pLevel + 1));
   	                    }
   	                }else{
						unset ($arDependencies[$dependency->kind.'_'.$dependency->name]);
   	                }
   	            }
   	        }else{
   	        	if ($dependency->kind === 'module') {
   	        		if ($arDependencies[$dependency->kind.'_'.$dependency->name]->level < $pLevel){
   	        			$arDependencies[$dependency->kind.'_'.$dependency->name]->level = $pLevel;
   	        		}
   	        	}
   	        }
   	    }
   	    return $arDependencies;
    }

    /**
     * Liste des dependance a supprimer si on supprime le module
     *
     * @param string $pModuleName Nom du module
     * @param mixed $arDependencies tableau de dépendances permettant de concatener les sous dépendance
     * @return mixed Tableau des dépendances
     */
    public static function getDependenciesForDelete ($pModuleName, $arDependencies = array()) {
        if (!in_array ($pModuleName, $arDependencies)) {
            $arDependencies[] = $pModuleName;
        }
        foreach (CopixModule::getList(true) as $installedModule){
            $toCheck = CopixModule::getInformations ($installedModule);
            foreach((array) $toCheck->dependencies as $dependency){
                if ($dependency->kind === 'module') {
                    if ($dependency->name == $pModuleName && !in_array ($toCheck->name, $arDependencies)) {
                        $arDependencies[] = $toCheck->name;
                        $arDependencies = self::getDependenciesForDelete ($toCheck->name, $arDependencies);
                    }
                }
            }
        }
        return $arDependencies;
    }

    /**
     * Test si une dépendance est valide
     * Pour un module, regarde si il existe (installer ou pas)
     * Pour une extension, regarde si elle est dans la liste des extensions chargé
     *
     * @param mixed $pDependency La dependance (kind, name)
     * @return boolean true ou false
     */
    public static function testDependency($pDependency) {
        switch ($pDependency->kind) {
            case 'module':
                return in_array($pDependency->name, CopixModule::getList(false));
            case 'extension':
                return extension_loaded($pDependency->name);
            case 'function':
                return function_exists($pDependency->name);
            case 'class':
                $arDependency = explode('|',$pDependency->name);
                if (class_exists($arDependency[0])) {
                    return true;
                } elseif (isset($arDependency[1])) {
                    if (@include_once($arDependency[1])) {
                        return class_exists($arDependency[0]);
                    }
                }
                return false;
            case 'copix':
                $arDependency = explode('.',$pDependency->name);
                if (COPIX_VERSION_MAJOR < $arDependency[0]) {
                    return false;
                } else if (COPIX_VERSION_MAJOR > $arDependency[0]) {
                    return true;
                }
                if (!isset($arDependency[1])) {
                    return true;
                }
                if (COPIX_VERSION_MINOR < $arDependency[1]) {
                    return false;
                } else if (COPIX_VERSION_MINOR > $arDependency[1]) {
                    return true;
                }
                if (!isset($arDependency[2])) {
                    return true;
                }
                if (COPIX_VERSION_FIX < $arDependency[2]) {
                    return false;
                }
                return true;
        }
        return false;
    }

    /**
     * Installation d'un module (sans prendre en compte les dépendances)
     *
     * @param string $pModuleName Nom du module
     * @return true si success et message de l'exception sinon
     */
    public static function installModule ($pModuleName) {
        try {
        	// evenement avant l'installation du module. si un listener retourne false, on annule l'installation
        	$response = CopixEventNotifier::notify (new CopixEvent ('beforeInstallModule', array ('moduleName' => $pModuleName)));
        	foreach ($response->getResponse () as $listener) {
        		if (isset ($listener['install']) && $listener['install'] === false) {
        			$message = (isset ($listener['message'])) ? $listener['message'] : _i18n ('copix:copixmodule.error.listenerNoMessage');
        			throw new CopixException (_i18n ('copix:copixmodule.error.listenerCancelInstall', array ($pModuleName, $message)));
        		}
        	}

        	// execution des scripts de base de données
            $scriptFile = self::_getInstallFile ($pModuleName);
            if ($scriptFile) {
                $ct = CopixDB::getConnection () ;
                $ct->doSQLScript($scriptFile);
            }

			// execution d'un script après l'install de la base de données
            $moduleInstaller = self::_getModuleInstaller ($pModuleName);
            if ($moduleInstaller !== null) {
                $moduleInstaller->processPreInstall ();
            }

			// recréé le cache des modules
            self::_addModuleInDatabase ($pModuleName);
            CopixModule::clearCache ();

            if ($moduleInstaller !== null) {
                $moduleInstaller->processPostInstall ();
            }

            // evenement après l'installation du module. si un listener retourne false, on annule l'installation
        	$response = CopixEventNotifier::notify (new CopixEvent ('afterInstallModule', array ('moduleName' => $pModuleName)));
        	foreach ($response->getResponse () as $listener) {
        		if (isset ($listener['install']) && $listener['install'] === false) {
        			$message = (isset ($listener['message'])) ? $listener['message'] : _i18n ('copix:copixmodule.error.listenerNoMessage');
        			throw new CopixException (_i18n ('copix:copixmodule.error.listenerCancelInstall', array ($pModuleName, $message)));
        		}
        	}

			$infos = CopixModule::getInformations ($pModuleName);
			if ($infos->getUpdateFrom () != $infos->getVersion ()) {
				self::updateModule ($pModuleName);
			}

        } catch (Exception $e) {
            return $e->getMessage();
        }
        return true;
    }

    /**
     * Désinstallation d'un module (sans prendre en compte les dépendances)
     *
     * @param string $pModuleName Nom du module
     * @return true si success et message de l'exception sinon
     */
    public static function deleteModule ($pModuleName) {
        try {
        	// evenement avant la désinstallation du module. si un listener retourne false, on annule la désinstallation
        	$response = CopixEventNotifier::notify (new CopixEvent ('beforeUninstallModule', array ('moduleName' => $pModuleName)));
        	foreach ($response->getResponse () as $listener) {
        		if (isset ($listener['uninstall']) && $listener['uninstall'] === false) {
        			$message = (isset ($listener['message'])) ? $listener['message'] : _i18n ('copix:copixmodule.error.listenerNoMessage');
        			throw new CopixException (_i18n ('copix:copixmodule.error.listenerCancelUninstall', array ($pModuleName, $message)));
        		}
        	}

            $moduleInstaller = self::_getModuleInstaller ($pModuleName);
            if ($moduleInstaller !== null) {
                $moduleInstaller->processPreDelete ();
            }

            $scriptFile = self::_getDeleteFile ($pModuleName);
            if ($scriptFile) {
                CopixDB::getConnection ()->doSQLScript ($scriptFile);
            }
            CopixModule::clearCache ();
            self::_deleteModuleInDatabase ($pModuleName);
            CopixModule::clearCache ();

            if ($moduleInstaller !== null) {
                $moduleInstaller->processPostDelete ();
            }

            // evenement après la désinstallation du module
        	$response = CopixEventNotifier::notify (new CopixEvent ('afterUninstallModule', array ('moduleName' => $pModuleName)));
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return true;
    }
    /**
     * Mets a jour un module
     * @param $pModuleName string Nom du module
     * @return mixed true si tout va bien, le message en cas d'exception et false si impossible de maj
     */
    public static function updateModule ($pModuleName) {
		CopixTemp::clearModule ($pModuleName);
        $dao = _ioDAO ('CopixModule');
        $infos = CopixModule::getInformations($pModuleName);
        $currentVersion = $dao->get($pModuleName);
        $moduleVersion  = $infos->version;
        if ($currentVersion->version_cpm == $moduleVersion) {
            return true;
        }

        $error = false;
        $moduleInstaller = self::_getModuleInstaller ($pModuleName);

		// des scripts de mise à jour sont présents
		if (count ($infos->update) > 0) {
			while ($currentVersion->version_cpm != $moduleVersion && !$error) {
				$error = true;

				foreach ($infos->update as $version) {
					if ($version->from == $currentVersion->version_cpm) {
						try {
							$scriptFile = self::_getScriptFile($pModuleName, $version->script);
							if ($scriptFile) {
								$ct = CopixDB::getConnection () ;
								$ct->doSQLScript ($scriptFile);
							}

							$method = 'process'.$version->script;
							if (method_exists($moduleInstaller, $method)) {
								$moduleInstaller->$method();
							}

							$error = false;
							$currentVersion->version_cpm = $version->to;
							_ioDAO ('CopixModule')->update($currentVersion);
							CopixTemp::clearModule ($pModuleName);
							break;
						} catch (Exception $e) {
							return $e->getMessage();
						}
					}
				}
			}
		} else {
			$currentVersion->version_cpm = $moduleVersion;
			_ioDAO ('CopixModule')->update ($currentVersion);
			CopixTemp::clearModule ($pModuleName);
		}

        if (!$error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Ajoute un module comme install dans la base de donnes
     * @param string $moduleName le nom du module  ajouter
     * @return void
     */
    private static function _addModuleInDatabase ($moduleName){
        //insert in database if we can
        $dao	= _ioDAO ('CopixModule');
        if (! $dao->get ($moduleName)){
            $record = _record ('CopixModule');
            $record->name_cpm = $moduleName;
            $record->path_cpm = CopixModule::getBasePath ($moduleName);
            $record->version_cpm = CopixModule::getInformations ($moduleName)->getUpdateFrom ();
            $dao->insert ($record);
        }
    }

    /**
     * Enléve le module de la base de donnes
     * @param string $moduleName le nom du module
     */
    private static function _deleteModuleInDatabase ($moduleName){
        _ioDAO ('CopixModule')->delete ($moduleName);
    }


    /**
     * _getInstallFile
     *
     * Return  install.DBType.sql file for the modulePath
     * @param string $modulePath
     * @return scriptFile
     * @access private
     */
    private static function _getInstallFile ($pModuleName) {
        if (self::_dbConfigured ()){
            // find the current connection type (defined in /plugins/copixDB/profils.definition.xml)
            $config = CopixConfig::instance ();
            $driver = $config->copixdb_getProfile ();
            $typeDB = CopixDB::driverToDatabase ($driver->getDriverName ());


            // Search each module install file
            $scriptName ='install.'.$typeDB.'.sql';

            $SQLScriptFile = CopixModule::getPath ($pModuleName) . COPIX_INSTALL_DIR . 'scripts/' . $scriptName; // chemin et nom du fichier de script d'install
            if (is_readable($SQLScriptFile)) {
                return $SQLScriptFile;
            } else {
                return null;
            }
        }
        return null;
    }

    /**
     * Retourne une classe qui contient des méthodes d'installation, ou null si la classe n'existe pas
     *
     * @param string $pModuleName Nom du module
     * @return stdclass
     */
    private static function _getModuleInstaller($pModuleName) {
        // chemin et nom du fichier de script d'install
        $moduleInstallerFile = CopixModule::getPath ($pModuleName) . COPIX_INSTALL_DIR . 'scripts/'.strtolower($pModuleName).'.class.php';
        if (is_readable($moduleInstallerFile)) {
            Copix::RequireOnce ($moduleInstallerFile);
            $className = 'CopixModuleInstaller'.$pModuleName;
            $class = new $className ();
            if (! $class instanceof ICopixModuleInstaller){
            	throw new CopixException ('The installer class '.$className.' must implements ICopixModuleInstaller');
            }
			return $class;
        } else {
            return null;
        }

    }

    /**
     * _getInstallFile
     *
     * Return  install.DBType.sql file for the modulePath
     * @param string $modulePath
     * @param string script name
     * @return scriptFile
     * @access private
     */
    private static function _getScriptFile ($pModuleName, $pScript) {
        if (self::_dbConfigured ()){
            // find the current connection type (defined in /plugins/copixDB/profils.definition.xml)
            $config = CopixConfig::instance ();
            $driver = $config->copixdb_getProfile ();
            $typeDB = CopixDB::driverToDatabase ($driver->getDriverName ());

            // Search each module install file
            $scriptName =$pScript.'.'.$typeDB.'.sql';

            $SQLScriptFile = CopixModule::getPath ($pModuleName) . COPIX_INSTALL_DIR . 'scripts/' . $scriptName; // chemin et nom du fichier de script d'install
            if (is_readable($SQLScriptFile)) {
                return $SQLScriptFile;
            } else {
                return null;
            }
        }
        return null;
    }

    /**
     * _getDeleteFile
     *
     * Return  delete.DBType.sql file for the modulePath
     * @param string $pModuleName le nom du module
     * @return le chemin du fichier sql
     * @access private
     */
    private static function _getDeleteFile ($pModuleName) {
        if (self::_dbConfigured()){
            // find the current connection type (defined in /plugins/copixDB/profils.definition.xml)
            $config = CopixConfig::instance ();
            $driver = $config->copixdb_getProfile ();
            $typeDB = CopixDB::driverToDatabase ($driver->getDriverName());

            // Search each module install file
            $scriptName = 'delete.'.$typeDB.'.sql';
            $SQLScriptFile = CopixModule::getPath ($pModuleName) . COPIX_INSTALL_DIR . 'scripts/' . $scriptName; // chemin et nom du fichier de script d'install
            return is_readable($SQLScriptFile) ? $SQLScriptFile : null;
        }
        return null;
    }
    /**
     * Indique si une base à été configurée
     * @return boolean true si une base est configurée, faux sinon
     */
    private static function _dbConfigured (){
        $defaultProfileName = CopixConfig::instance ()->copixdb_getDefaultProfileName ();
        return isset($defaultProfileName);
    }
}