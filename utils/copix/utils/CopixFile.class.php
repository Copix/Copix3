<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Croës Gérald, Jouanneau Laurent
 * @copyright	2001-2007 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * CopixFileLocker heir. Is heavily based on what you can see on Smarty (http://smarty.php.net)
 *
 * @package		copix
 * @subpackage	utils
 */
class CopixFile {
	
	const DIRMOD=0755;
	const FILEMOD=0770;

	/**
	 * Lecture du contenu d'un fichier et retourne ce dernier dans une chaine de caractère
	 *
	 * @param	string	$pFilename	Le chemin du fichier à lire
	 * @return 	string 	le contenu du fichier
	 * <code>
	 *    $fileContent = CopixFile::read (COPIX_VAR_PATH.'fichier_de_donnees.dat');
	 * </code>
	 */
	public static function read ($pFilename){
		return (file_exists($pFilename)) ? file_get_contents ($pFilename, false) : false;
	}

	/**
	 * Ecriture d'un fichier sur le disque dur
	 *
	 * Cette fonction est basée sur le code trouvé dans Smarty (http://smarty.php.net)
	 *
	 * @param	string	$file le nom du fichier (le fichier sera crée ou remplacé)
	 * @param	mixed	$data les données à écrire dans le fichier
	 * @return	bool 	si la fonction a correctement écrit les données dans le fichier
	 */
	public static function write ($file, $data){
		$_dirname = dirname ($file);

		//If the $file finish with / just createDir
		if ((($lastChar = substr ($file, -1)) == '/') || ($lastChar == '\\')){
			self::_createDir ($file);
			return true;
		} else {
			//asking to create the directory structure if needed.
			self::_createDir ($_dirname);
		}

		if(!@is_writable ($_dirname)) {
			// cache_dir not writable, see if it exists
			if(!@is_dir ($_dirname)) {
				trigger_error (CopixI18N::get ('copix:copix.error.cache.directoryNotExists', array ($_dirname)));
				return false;
			}
			trigger_error (CopixI18N::get ('copix:copix.error.cache.notWritable', array ($file, $_dirname)));
			return false;
		}

		// write to tmp file, then rename it to avoid
		// file locking race condition
		$_tmp_file = tempnam ($_dirname, 'wrt');

		if (!($fd = @fopen ($_tmp_file, 'wb'))) {
			$_tmp_file = $_dirname . '/' . uniqid('wrt');
			if (!($fd = @fopen ($_tmp_file, 'wb'))) {
				trigger_error(CopixI18N::get ('copix:copix.error.cache.errorWhileWritingFile', array ($file, $_tmp_file)));
				return false;
			}
		}

		fwrite ($fd, $data);
		fclose ($fd);

		// Delete the file if it allready exists (this is needed on Win,
		// because it cannot overwrite files with rename())
		if (CopixConfig::osIsWindows ()) {
			// DDT : ajout du test pour la vérification de l'existence du fichier
			if (file_exists ($file)) {
				@unlink ($file);
			}
			@copy($_tmp_file, $file);//Sur certaines configuration bien particulières, il arrive que 
			//windows echoue sur le rename... ?
			@unlink($_tmp_file);
		}else{
			@rename($_tmp_file, $file);
		}
		@chmod($file, self::FILEMOD );
		return true;
	}

	/**
	 * Création d'une arborescence de répertoires
	 *
	 * @param	string	$dir	La structure à créer
	 * @return	bool 	si le repertoire est créé ou existe on retourne vrai, faux sinon 
	 * @access private
	 */
	private static function _createDir ($dir){
		if (!file_exists ($dir)) {
			$_open_basedir_ini = ini_get('open_basedir');

			if (DIRECTORY_SEPARATOR=='/') {
				/* unix-style paths */
				$_dir = $dir;
				$_dir_parts = preg_split('!/+!', $_dir, -1, PREG_SPLIT_NO_EMPTY);
				$_new_dir = ($_dir{0}=='/') ? '/' : getcwd().'/';
				if($_use_open_basedir = !empty($_open_basedir_ini)) {
					$_open_basedirs = explode(':', $_open_basedir_ini);
				}

			} else {
				/* other-style paths */
				$_dir = str_replace('\\','/', $dir);
				$_dir_parts = preg_split('!/+!', $_dir, -1, PREG_SPLIT_NO_EMPTY);
				if (preg_match('!^((//)|([a-zA-Z]:/))!', $_dir, $_root_dir)) {
					/* leading "//" for network volume, or "[letter]:/" for full path */
					$_new_dir = $_root_dir[1];
					/* remove drive-letter from _dir_parts */
					if (isset($_root_dir[3])) array_shift($_dir_parts);

				} else {
					$_new_dir = str_replace('\\', '/', getcwd()).'/';
				}

				if($_use_open_basedir = !empty($_open_basedir_ini)) {
					$_open_basedirs = explode(';', str_replace('\\', '/', $_open_basedir_ini));
				}

			}

			/* all paths use "/" only from here */
			foreach ($_dir_parts as $_dir_part) {
				$_new_dir .= $_dir_part;

				if ($_use_open_basedir) {
					// do not attempt to test or make directories outside of open_basedir
					$_make_new_dir = false;
					foreach ($_open_basedirs as $_open_basedir) {
						if (substr($_new_dir, 0, strlen($_open_basedir)) == $_open_basedir) {
							return$_make_new_dir = true;
							break;
						}
					}
				} else {
					$_make_new_dir = true;
				}

				if ($_make_new_dir && !file_exists($_new_dir) && !@mkdir($_new_dir, self::DIRMOD) && !is_dir($_new_dir)) {
					trigger_error(CopixI18N::get ("copix:copix.error.cache.creatingDirectory", array ($_new_dir)));
					return false;
				}
				$_new_dir .= '/';
			}
		}
		return true;
	}

	/**
	 * Création d'une arborescence de répertoires si elle n'existe pas.
	 * @param	string	$pDirectory	le nom du répertoire que l'on souhaites créer
	 * <code>
	 *    if (CopixFile::createDir (COPIX_TEMP_PATH.'chemin/complet/des/repertoires/a/creer/')){
	 *       //ok, le répertoire à bien été cré
	 *    }
	 * </code>
	 */
	public static function createDir ($pDirectory){
		return self::_createDir ($pDirectory);
	}

	/**
	 * Recherche d'un pattern dans une arborescence de répertoire
	 * @param string $pPattern le pattern à rechercher (patterns supportés: filename.*.ext, *.ext, filenamestart*.ext)
	 * @param string $pPath le chemin dans lequel on va rechercher les fichiers
	 * @param bool $pRecursiveSearch si l'on va également rechercher dans les sous dossier (défaut = true)
	 * @return array of string une liste de fichier (chemins) correspondant à la recherche
	 */
	public static function search ($pPattern, $pPath, $pRecursiveSearch = true){
		$pPath = self::trailingSlash($pPath);
		$files = glob ($pPath.$pPattern);
		if ($pRecursiveSearch){
			$tab = array();
			foreach ($files as $file) {
				if (is_dir ($file)){
					$tab = array_merge($tab, self::search($pPattern, $file, true));
				} else {
					$tab[]=$file;
				}
			}
			return $tab;
		}else{
			return $files;
		}
	}

	/**
	 * S'assure qu'il existe bien un slash de fin dans le nom $pPath
	 *
	 * @param string $pPath la chaine à traiter
	 * @return string la chaine $pPath avec le slash de fin
	 * @access public
	 */
	public static function trailingSlash ($pPath){
		$pPath = trim ($pPath);
		if (substr ($pPath, -1) === '/'){
			return $pPath;
		}
		return $pPath.'/';
	}

	/**
	 * Supression d'une arborescence à partir d'un répertoire donné 
	 * (récursivement permet donc de supprimer tout les sous repertoire) 
	 * @param string $pDirectory le nom du répertoire que l'on souhaites supprimer.
	 * @param boolean $pStopOnFailure indique si l'on doit s'arrêter en cas d'échec de suppression d'un élément
	 *        (par défaut false) 
	 * @return true si suppression correcte, array of string si échec de supression.
	 *    Le tableau contient l'ensemble des fichiers qui ne sont pas supprimés.
	 */
	public static function removeDir ($pDirectory, $pStopOnFailure = false){
	    $pRep = false;
	    if (substr($pDirectory,strlen($pDirectory)-1)!='/') {
	        $pDirectory .= '/';
	        $pRep = true;
	    }
		$pDirectoryMask = $pDirectory."*";
		$failed  = array ();
		foreach (glob ($pDirectoryMask) as $file) {
			if (is_dir ($file)) {
				$response = self::removeDir ($file,$pStopOnFailure);
				if (!$response && $pStopOnFailure) return $response;
				if (!$response) $failed = array_merge ($failed, $response);
			} else {
				if (! @unlink ($file)){
					$failed[] = $file;
					if ($pStopOnFailure){
						return array ($failed);
					}
				}
			}
		}
		if ($pRep) 	rmdir(substr($pDirectory,0,strlen($pDirectory)-1));
		return count ($failed) ? $failed : true;
	}

	/**
	 * Supression de tout les fichier d'une arborescence à partir d'un répertoire donné 	
	 * @param string $pDirectory le nom du répertoire que l'on parser pour la suppression.
	 * @param boolean $pStopOnFailure indique si l'on doit s'arrêter en cas d'échec de suppression d'un élément
	 *        (par défaut false) 
	 * @return true si suppression correcte, array of string si échec de supression.
	 *    Le tableau contient l'ensemble des fichiers qui ne sont pas supprimés.
	 */
	public static function removeFileFromPath ($pDirectory, $pStopOnFailure = false){
		$pDirectoryMask =$pDirectory."*";
		$failed  = array ();
		$files = glob ($pDirectoryMask);
		if (! $files) {
			$files = array ();
		}

		foreach ($files as $file) {
			if (is_dir($file)) {
				$response=self::removeFileFromPath ($file.'/');
				if (!$response && $pStopOnFailure) return $failed;
				if (!$response) $failed = array_merge ($failed, $response);
				//rmdir($file);
			} else {
				if (! @unlink ($file)){
					$failed[] = $file;
					if ($pStopOnFailure){
						return $failed;
					}
				}
			}
		}
			
		return count ($failed) ? $failed : true;
	}

	/**
	 * Extraction du nom de fichier seul (sans le chemin)
	 *
	 * @param string $pPath le chemin dans lequel extraire le nom de fichier
	 * @return string le nom du fichier
	 */
	public static function extractFileName ($pPath){
		return basename (str_replace ('\\', '/', $pPath));
	}

	/**
	 * Extraction du chemin seul (sans le nom du fichier)
	 *
	 * @param	string	$pPath le chemin depuis lequel extraire le chemin
	 * @return	string 	le chemin du fichier
	 */
	public static function extractFilePath ($pPath){
		return self::trailingSlash (dirname (str_replace ('\\', '/', $pPath)));
	}

	/**
	 * Extraction de l'extension d'un fichier
	 *
	 * @param	string	$pFilePath
	 * @return 	string	l'extension du fichier (avec le .)
	 */
	public static function extractFileExt ($pFileName){
		$pFileName = self::extractFileName ($pFileName);
		return substr ($pFileName, strrpos ($pFileName, '.'));
	}
}
?>