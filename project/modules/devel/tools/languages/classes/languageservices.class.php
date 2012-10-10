<?php
/**
 * @package		tools
 * @subpackage	languages
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Fonctions utiles pour tout le module
 *
 * @package		tools
 * @subpackage	languages
 */
class LanguageServices {
	/**
	 * Lien vers le drapeau d'un fichier non modifiable
	 *
	 * @var string
	 */
	private static $_flagLocked = 'locked.png';
	
	/**
	 * Nom du drapeau pour un pays inconnu
	 *
	 * @var string
	 */
	private static $_flagUnknow = 'unknow.png';
	
	/**
	 * Remet à jour la liste des fichiers lockés, en supprimant ceux qui ont dépassé le temps de lock
	 */
	public static function updateLockedFiles () {
		_ioDao ('languageslocks')->deleteBy (
			_daoSP ()->addCondition ('time_lock', '<=', (time () - (CopixConfig::get ('languages|lockWaitTimeOut') * 60)))
		);
	}
	
	/**
	 * Retourne le répertoire des icones des drapeaux
	 *
	 * @return string
	 */
	public static function getFlagsPath () {
		return 'img/flags/';
	}
	
	/**
	 * Retourne le chemin vers le répertoire de backup
	 *
	 * @return string
	 */
	public static function getBackupPath () {
		return COPIX_VAR_PATH . 'modules/languages/backups/';
	}
	
	/**
	 * Lock un fichier
	 * 
	 * @param string $pModule Nom du module
	 * @param LanguageFileInfos $pFileInfos Informations sur le fichier
	 */
	public static function lock ($pModule, $pFileInfos) {
		// recherche d'un éventuel lock sur ce fichier déja existant
		$lock = _ioDao ('languageslocks')->findBy (
			_daoSP ()
				->addCondition ('module_lock', '=', $pModule)
				->addCondition ('file_lock', '=', $pFileInfos->getName ())
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
	 * Ecrase ou créé un fichier
	 * 
	 * @param string $pModule Nom du module
	 * @param object $pFileInfos Informations sur le fichier (retour de getFileInfos)
	 * @param array $pMessages Messages à écrire dans le fichier (clef = nom du message, valeur = texte du message)
	 */
	public static function write ($pModule, $pFileInfos, $pMessages) {
		try {
			self::assertCanEditFile ($pModule, $pFileInfos);
		} catch (CopixException $e) {
			if ($e->getMessage () <> 'fileNotFound') {
				throw new CopixException ($e->getMessage ());
			}
		}
		self::backupFile ($pModule, $pFileInfos);
	
		$filePath = CopixModule::getPath ($pModule) . COPIX_RESOURCES_DIR . $pFileInfos->name;
		
		// réécriture du fichier		
		$fileHwnd = fopen ($filePath, 'w');
		foreach ($pMessages as $key => $value) {
			fwrite ($fileHwnd, $key . ' = ' . $value . "\n");
		}
		fclose ($fileHwnd);
	}
	
	/**
	 * Effectue une sauvegarde du fichier
	 * 
	 * @param string $pModule Nom du module
	 * @param object $pFileInfos Informations sur le fichier (retour de getFileInfos) 
	 */
	public static function backup ($pModule, $pFileInfos) {
		// si on a configuré un backup
		if (CopixConfig::get ('languages|nbrBackupFiles') > 0) {
			// création du répertoire des backups
			$backupDir = COPIX_VAR_PATH . 'modules/languages/backups/' . $pModule . '/';
			CopixFile::createDir ($backupDir);
			
			// recherche des backups de ce fichier .properties
			$search = CopixFile::search ($pFileInfos->baseName . '*', $backupDir, false);
			rsort ($search);
			
			// suppression des fichiers "en trop", selon la config "nbrBackupFiles"
			if (count ($search) >= CopixConfig::get ('languages|nbrBackupFiles')) {
				for ($boucle = CopixConfig::get ('languages|nbrBackupFiles') - 1; $boucle < count ($search); $boucle++) {
					unlink ($search[$boucle]);
				}
			}
			
			// création du backup
			$filePath = CopixModule::getPath ($pModule) . COPIX_RESOURCES_DIR;
			if (file_exists ($filePath . $pFileInfos->name)) {
				copy ($filePath . $pFileInfos->name, $backupDir . $pFileInfos->name . '.' . time ());
			}
		}
	}
	
	/**
	 * Supprime un fichier properties
	 * 
	 * @param string $pModule Nom du module
	 * @param object $pFileInfos Informations sur le fichier (retour de getFileInfos)
	 * @return bool
	 */
	public static function delete ($pModule, $pFileInfos) {
		self::assertCanEditFile ($pModule, $pFileInfos);
		self::backupFile ($pModule, $pFileInfos);
		
		$filePath = CopixModule::getPath ($pModule) . COPIX_RESOURCES_DIR . $pFileInfos->name;
		if (is_file ($filePath)) {
			return CopixFile::delete ($filePath);
		}
		
		return false;
	}
	
	/**
	 * Renvoie une clef de message valide
	 * 
	 * @param string $key Clef à vérifier, et modifier si nécessaire
	 * @return string Clef valide
	 */
	public static function getValidKey ($pKey) {
		return str_replace (' ', '', $pKey);
	}
	
	/**
	 * Renvoie un nom de section valide
	 * 
	 * @param string $section Section à vérifier, et modifier si nécessaire
	 * @return string Section valide
	 */
	public static function getValidSection ($pSection) {
		return str_replace (' ', '', $pSection);
	}
	
	/**
	 * Assure qu'un fichier peut être modifié, génère des exceptions si un blocage intervient
	 * 
	 * @param string $pModule Nom du module
	 * @param object $pFileInfos Informations sur le fichier (retour de getFileInfos) 
	 * @param int $pFilemtime Date de modification du fichier à comparer format timestamp, null si on ne veut pas comparer
	 */
	public static function assertEdit ($pModule, $pFileInfos, $pFilemtime = null) {
		$filePath = CopixModule::getPath ($pModule) . COPIX_RESOURCES_DIR . $pFileInfos->name;
		
		// si le fichier n'existe pas
		if (!file_exists ($filePath)) {
			throw new CopixException ('fileNotFound');
		
		// si le fichier existe
		} else {
		
			// recherche des fichiers lockés
			$locks = _ioDao ('languageslocks')->findBy (
				_daoSP ()
					->addCondition ('module_lock', '=', $pModule)
					->addCondition ('file_lock', '=', $pFileInfos->name)
			);
			foreach ($locks as $lockIndex => $lockInfos) {
				// fichier locké par un autre utilisateur
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
			}
		}
	}
	
	/**
	 * Recherche les fichiers .properties
	 * 
	 * @param bool $pGetInstalled Rechercher dans les modules installés
	 * @param bool $pGetUninstalled Rechercher dans les modules non installés
	 * @return array
	 */
	public static function getFiles ($pGetInstalled, $pGetUninstalled) {
		$installedModules = CopixModule::getFullList (true);
		ksort ($installedModules);
		
		$uninstalledModules = CopixModule::getFullList (false);
		$uninstalledModules = array_diff_key ($uninstalledModules, $installedModules);
		ksort ($uninstalledModules);
		
		$toReturn = array ();
		
		// recherche des fichiers .properties dans les modules installés
		if ($pGetInstalled && !$pGetUninstalled) {
			$toReturn = self::_getLngInfos ($installedModules);

		// recherche des fichiers .properties dans les modules installés et non installés
		} else if ($pGetUninstalled && $pGetInstalled) {
			$toReturn = array_merge (self::_getLngInfos ($installedModules), self::_getLngInfos ($uninstalledModules));
		
		// recherche des fichiers .properties dans les modules non installés uniquement
		} else if ($pGetUninstalled && !$pGetInstalled) {
			$toReturn = self::_getLngInfos ($uninstalledModules);
		}
		
		return $toReturn;
	}
	
	/**
	 * Récupère des infos sur les fichiers .properties de modules
	 * 
	 * @param array $pModules Retour d'un CopixModule::getFullList
	 * @return array
	 */
	private static function _getLngInfos ($pModules) {
		$functions = _class ('functions');
		$toReturn = array ();
		
		foreach ($pModules as $module_name => $module_dir) {
			$module_dir = $module_dir . $module_name . '/';
			$module_infos = CopixModule::getInformations ($module_name);
			$module_title = '[' . $module_name . '] ' . $module_infos->description;
			
			// recherche des langues de ce module
			$resourcesPath = $module_dir . COPIX_RESOURCES_DIR;
			if (is_dir ($resourcesPath)) {
				$dirHwnd = opendir ($resourcesPath);
				while (($file = readdir ($dirHwnd)) !== false) {
					if (strpos ($file, '.properties') !== false) {
						$fileInfos = $functions->getFileInfos ($file);	
						try {
							$langueIcon = self::getFlagsPath () . strtolower ($fileInfos->country) . '.png';
							$functions->assertCanEditFile ($module_name, $fileInfos);
						} catch (CopixException $e) {
							$langueIcon = self::getFlagsPath () . self::$_flagLocked;
						}
						
						if (file_exists (_resourcePath ($langueIcon))) {
							$iconPath = _resource ($langueIcon);
						} else {
							$iconPath = _resource (self::getFlagsPath () . self::$_flagUnknow);
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