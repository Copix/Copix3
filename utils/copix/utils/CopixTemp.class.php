<?php
/**
 * @package copix
 * @subpackage utils
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion du répertoire temp
 *
 * @package copix
 * @subpackage utils
 */
class CopixTemp {
	/**
	 * Détermine si un fichier doit être enlevé du répertorie temp.
	 *
	 * @param string $Path
	 * @return boolean
	 */
	public static function _tempFileFilter ($Path) {
		$basename = basename ($Path);
		if (is_dir ($Path) && ($basename == '.svn' || $basename == 'CVS' || $basename == '.htaccess')) {
			return false;
		}
		return true;
	}

	/**
	 * Retourne le chemin vers le fichier de lock du répertoire temp
	 *
	 * @return boolean
	 */
	private static function _getLockPath () {
		return COPIX_VAR_PATH . DIRECTORY_SEPARATOR . 'temp.lock';
	}

	/**
	 * Vide le répertoire temporaire
	 *
	 * @param string $pPath Chemin du répertoire à vider
	 * @return boolean
	 */
	public static function clear ($pPath = null) {
		self::assertNotLocked ();

    	self::lock ();
		if ($pPath != null) {
			// vérification existance $pPath
			if (!is_dir (COPIX_TEMP_PATH . $pPath)) {
				return false;
			}
			// vérification sur $pPath, pour être sur de ne pas supprimer un répertoire "avant" le répertoire temporaire
			$realPathTemp = CopixFile::getRealPath (COPIX_TEMP_PATH);
			if (substr (CopixFile::getRealPath (COPIX_TEMP_PATH . $pPath), 0, strlen ($realPathTemp)) != $realPathTemp) {
				$extras = array ('COPIX_TEMP_PATH' => $realPathTemp, '$pPath' => $pPath, 'fullPath' => CopixFile::getRealPath (COPIX_TEMP_PATH . $pPath));
				throw new CopixException ('Le répertoire à supprimer n\'est pas dans le répertoire temporaire.', 0, $extras);
			}
		}
		CopixFile::removeFileFromPath (COPIX_TEMP_PATH . $pPath, false, array ('CopixTemp', '_tempFileFilter'));
		self::unlock ();
		return true;
	}

	/**
	 * Suppression des fichiers temporaires qui concernent un module en particulier
	 *
	 * @param string $pName Nom du module
	 */
	public static function clearModule ($pName) {
		if (!in_array ($pName, CopixModule::getList (false))) {
			throw new CopixException ('Le module "' . $pName . '" n\'existe pas.');
		}

		self::assertNotLocked ();
		self::lock ();

		try {
			// autoload
			CopixFile::delete (CopixModuleClassAutoloader::getCacheFileName ($pName));

			// module.xml
			foreach (CopixModule::getCacheFilesName ($pName) as $file) {
				CopixFile::delete ($file);
			}

			// toutes les daos, puisqu'on ne sait pas quelles DAO utilisent le module demandé
			CopixFile::removeDir (CopixDAOFactory::getCacheBasePath ());

			// liste des modules
			CopixFile::delete (CopixModule::getListCacheFileName ());
			CopixFile::delete (CopixModule::getListCacheFileName (false));

			// traductions
			foreach (CopixI18NBundle::getCacheFilesName ($pName) as $file) {
				CopixFile::delete ($file);
			}

			// configuration
			CopixFile::delete (CopixModuleConfig::getCacheFileName ($pName));

			// ressources
			foreach (CopixResourceFetcher::getCacheFilesName ($pName) as $file) {
				CopixFile::delete ($file);
			}

			// A FAIRE
			// templates
			// listeners.instance.php
		
		} catch (Exception $e) {
			self::unlock ();
			throw $e;
		}
		self::unlock ();
	}

	/**
	 * Bloque toute action dans le répertoire temporaire
	 */
	public static function lock () {
		CopixFile::write (self::_getLockPath (), 'temp dir is locked');
	}

	/**
	 * Supprime le blocage du répertoire temporaire
	 */
	public static function unlock () {
		try {
			CopixFile::delete (self::_getLockPath ());
		} catch (Exception $e) {}
	}

	/**
	 * Indique si le répertoire temporaire est bloqué
	 *
	 * @return boolean
	 */
	public static function isLocked () {
		return file_exists (self::_getLockPath ());
	}

	/**
	 * Certifie que le répertoire des fichiers temporaire est accessible
	 */
	public static function assertNotLocked () {
		if (self::isLocked ()) {
			throw new CopixException ('L\'accès en écriture au répertoire des fichiers temporaires est temporairement bloqué.');
		}
	}

	/**
	 * Ecrit un fichier dans le répertoire temporaire
	 *
	 * @param string $pFileName Chemin + nom du fichier
	 * @param string $pData Contenu du fichier
	 * @return boolean
	 */
	public static function write ($pFileName, $pData) {
		if (self::isLocked ()) {
			return false;
		}
		self::lock ();
		try {
			CopixFile::write ($pFileName, $pData);
		} catch (Exception $e) {
			self::unlock ();
			throw $e;
		}
		self::unlock ();
		return true;
	}

	/**
	 * Retourne des informations sur le répertoire temporaire
	 *
	 * @return array
	 */
	public static function getInformations () {
		$toReturn = array (
			'path' => CopixFile::getRealPath (COPIX_TEMP_PATH),
			'locked' => self::isLocked (),
			'locked_since' => (file_exists (self::_getLockPath ())) ? filemtime (self::_getLockPath ()) : null,
		);
		return $toReturn;
	}
}