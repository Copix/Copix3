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
				throw new Exception (_i18n ('copix:copix.error.cache.directoryNotExists', array ($_dirname)));
			}
			throw new Exception (_i18n ('copix:copix.error.cache.notWritable', array ($file, $_dirname)));
		}

		// write to tmp file, then rename it to avoid
		// file locking race condition
		$_tmp_file = tempnam ($_dirname, 'wrt');

		if (!($fd = @fopen ($_tmp_file, 'wb'))) {
			$_tmp_file = $_dirname . '/' . uniqid('wrt');
			if (!($fd = @fopen ($_tmp_file, 'wb'))) {
				throw new Exception (_i18n ('copix:copix.error.cache.errorWhileWritingFile', array ($file, $_tmp_file)));
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
	 * Effacer un fichier
	 *
	 * @param	string	$pFilename	Le chemin du fichier à effacer
	 * @return 	boolean 	si le fichier est effacé
	 * <code>
	 *    $isDeleted = CopixFile::delete (COPIX_VAR_PATH.'fichier_de_donnees.dat');
	 * </code>
	 */
	public function delete ($pFilename) {
		$_dirname = dirname ($pFilename);

		// On vérifie si on n'a pas un fichier
		if ((($lastChar = substr ($pFilename, -1)) == '/') || ($lastChar == '\\')){
			return false;
		}

		if(!@is_writable ($_dirname)) {
			// On ne dispose pas des droits d'écriture, vérifions si le répertoire existe
			if(!@is_dir ($_dirname)) {
				throw new Exception (_i18n ('copix:copix.error.cache.directoryNotExists', array ($_dirname)));
			}
			throw new Exception (_i18n ('copix:copix.error.cache.notWritable', array ($file, $_dirname)));
		}

		if (@unlink($pFilename)) {
			return true;
		} else {
			return false;
		}
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
							$_make_new_dir = true;
							break;
						}
					}
				} else {
					$_make_new_dir = true;
				}

				if ($_make_new_dir && !file_exists($_new_dir) && !@mkdir($_new_dir, self::DIRMOD) && !is_dir($_new_dir)) {
					throw new Exception (_i18n ("copix:copix.error.cache.creatingDirectory", array ($_new_dir)));
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
	 * Permet de supprimer ou nettoyer une arborescence de fichiers.
	 *
	 * @param string $pDirectory Répertoire cible.
	 * @param array $pFailedList Liste des fichiers/répertoires en erreur.
	 * @param boolean $pRemoveDirectory true: supprimer le répertoire à la fin de l'opération, false: ne faire que supprimer les fichiers.
	 * @param boolean $pStopOnFailure true: s'arrêter à la première erreur, false: continuer en cas d'erreur,
	 * @param ICopixFileFilter $pFileFilter Filtre à appliquer sur le contenu du répertoire.
	 * @return boolean true si l'opération s'est déroulée normalement, false s'il y a eu une erreur.
	 */
	private static function _deleteDirectory($pPath, &$pFailed, $pRemoveDirectory, $pStopOnFailure, $pFilterCallback) {
		// Initialidation de $toReturn
		$toReturn = true;
	  
		// Récupère le contenu du répertoire
		$entries = glob(self::trailingSlash($pPath).'*');

		// Compte le nombre d'entrées (avant filtrage)
		$remaining = count($entries);

		// Applique le filter
		if($pFilterCallback) {
			$entries = array_filter($entries, $pFilterCallback);
		}

		// Traite les entrées
		foreach($entries as $entry) {
				
			if(is_dir($entry)) {
				// Répertoire : suppresion récursive
				$toReturn = self::_deleteDirectory($entry, $pFailed, $pRemoveDirectory, $pStopOnFailure, $pFilterCallback);

			} elseif(!($toReturn = (@unlink($entry) ? true : false))) {
				// Fichier : simple suppression
				$pFailed[] = $entry;
			}
			// Gère le code de retour
			if($toReturn) {
				// Entrée supprimée : on réduit le nombre restant
				$remaining--;
			} elseif($pStopOnFailure) {
				// Erreur avec demande d'arrêt : on stop
				break;
			}
		}

		// Supprime le répertoire lui-même si demandé et s'il est vide
		if($toReturn && $pRemoveDirectory) {
			if($remaining > 0) {
				// S'il reste des entrées, on ne pourra pas supprimer de toute façon
				$toReturn = false;
			} else {
				// Tente la suppression
				$toReturn = @rmdir($pPath) ? true : false;
			}
		}
			
		// Retourne le résultat de l'opération
		if(!$toReturn) {
			$pFailed[] = $pPath;
		}
		return $toReturn;

	}

	/**
	 * Supression d'une arborescence à partir d'un répertoire donné
	 * (récursivement permet donc de supprimer tout les sous repertoire)
	 * @param string $pDirectory le nom du répertoire que l'on souhaites supprimer.
	 * @param boolean $pStopOnFailure indique si l'on doit s'arrêter en cas d'échec de suppression d'un élément
	 *        (par défaut false)
	 * @param callback $pFilterCallback Callback utilisé pour savoir si on doit supprimer un fichier ou un répertoire;
	 *                                  paramètres du callback : chemin du fichier.
	 * 	 * @return true si suppression correcte, array of string si échec de supression.
	 *    Le tableau contient l'ensemble des fichiers qui ne sont pas supprimés.
	 */
	public static function removeDir ($pDirectory, $pStopOnFailure = false, $pFilterCallback = null){
		$failed = array();
		$success = self::_deleteDirectory($pDirectory, $failed, true, $pStopOnFailure, $pFilterCallback);
		return $success ? true : $failed;
	}

	/**
	 * Supression de tout les fichier d'une arborescence à partir d'un répertoire donné
	 * @param string $pDirectory le nom du répertoire que l'on parser pour la suppression.
	 * @param boolean $pStopOnFailure indique si l'on doit s'arrêter en cas d'échec de suppression d'un élément
	 *        (par défaut false)
	 * @param callback $pFilterCallback Callback utilisé pour savoir si on doit supprimer un fichier ou un répertoire;
	 *                                  paramètres du callback : chemin du fichier.
	 * @return true si suppression correcte, array of string si échec de supression.
	 *    Le tableau contient l'ensemble des fichiers qui ne sont pas supprimés.
	 */
	public static function removeFileFromPath ($pDirectory, $pStopOnFailure = false, $pFilterCallback = null){
		$failed = array();
		$success = self::_deleteDirectory($pDirectory, $failed, false, $pStopOnFailure, $pFilterCallback);
		return $success ? true : $failed;
	}

	/**
	 * Implémentation de la recherche de fichiers.
	 *
	 * @param array $result Liste en cours de construction
	 * @param string $basePath Chemin de base parcouru.
	 * @param string $relativePath Chemin relatif par rapport au chemin de base.
	 * @param integer $depth Profondeur par rapport au chemin de base.
	 * @param callback $entryFilter Callback utilisé pour déterminer si un fichier ou un répertoire doit être listé.
	 * @param callback $recurseFilter Callback utilisé pour déterminer si on doit descendre dans un répertoire
	 */
	private static function _findFiles(&$result, $basePath, $relativePath, $depth, $entryFilter, $recurseFilter) {

		$entries = glob($basePath.$relativePath.'*');
		foreach($entries as $entry) {
			$entryRelativePath = $relativePath.basename($entry);
			$entryFullPath = $basePath.$entryRelativePath;
			$entryDepth = $depth+1;
			// Si c'est un répertoire, détermine si on doit entrer dedans
			$doRecurse = is_dir($entry) && (!$recurseFilter || call_user_func($recurseFilter, $entryFullPath, $entryRelativePath, $basePath, $entryDepth));
			// Dans tous les cas, demande si on le liste
			if(!$entryFilter || call_user_func($entryFilter, $entryFullPath, $entryRelativePath, $basePath, $entryDepth)) {
				$result[] = $entry;
			}
			// Appel récursif quand nécessaire.
			if($doRecurse) {
				self::_findFiles($result, $basePath, $entryRelativePath.'/', $entryDepth, $entryFilter, $recurseFilter);
			}
		}
	}

	/**
	 * Recherche des fichiers et répertoires répondant à des critères spécifiés.
	 *
	 * Les deux callbacks que l'on peut passer en paramètres doit avoir la forme :
	 * function($fullPath, $relativePath, $basePath, $depth).
	 *
	 * @param array $basePaths Une liste des répertoires dans lesquels chercher.
	 * @param callback $entryFilter Callback utilisé pour déterminer si un fichier ou un répertoire doit être listé.
	 * @param callback $recurseFilter Callback utilisé pour déterminer si on doit descendre dans un répertoire
	 * @return array Liste des fichiers et répertoires trouvés.
	 */
	public static function findFiles($basePaths, $entryFilter = null, $recurseFilter = null) {

		if(!is_array($basePaths)) {
			$basePaths = array($basePaths);
		}
		$result = array();
		foreach($basePaths as $basePath) {
			$basePaths = self::trailingSlash($basePath);
			self::_findFiles($result, $basePath, '', 0, $entryFilter, $recurseFilter);
		}
		return $result;
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
		if (($pos = strrpos ($pFileName, '.')) !== false){
			return substr ($pFileName, $pos);
		}
		return null;
	}

	/**
	 * Retourne l'icone associée au fichier
	 * @param	string	$pFileName	le nom du fichier
	 * @return string	le chemin de l'icone
	 */
	public static function getIcon ($pFileName){
		switch (self::extractFileExt ($pFileName)){
			case '.gif':
			case '.png':
			case '.jpg':
			case '.jpeg':
			case '.bmp':
				return 'img/mimetypes/image.png';
			case '.doc':
			case '.odt':
				return 'img/mimetypes/office-document.png';
			case '.txt':
				return 'img/mimetypes/text.png';
			case '.sh':
				return 'img/mimetypes/script.png';
			case '.xls':
				return 'img/mimetypes/office-speadshit.png';
			case '.ppt':
			case '.pps':
			case '.odp':
			case '.sxi':
			case '.fodp':
				return 'img/mimetypes/office-presentation.png';
			case '.odg':
			case '.sxd':
				return 'img/mimetypes/office-drawing.png';
			case '.zip':
			case '.gz':
			case '.bz2':
			case '.rar':
				return 'img/mimetypes/archive.png';
			case '.php':
			case '.php5':
			case '.php4':
			case '.php3':
			case '.ptpl':
				return 'img/mimetypes/php.png';
			case '.html':
			case '.xhtml':
			case '.htm':
				return 'img/mimetypes/html.png';
			case '.tpl':
				return 'img/mimetypes/text-template.png';
			case '.avi':
			case '.mpg':
			case '.wmv':
			case '.mp4':
				return 'img/mimetypes/video.png';
			case '.wav':
			case '.mp3':
			case '.ogg':
			case '.wma':
				return 'img/mimetypes/audio.png';
			case '.exe':
				return 'img/mimetypes/executable.png';
			default :
				return 'img/mimetypes/unknown.png';
		}
	}

	/**
	 * Liste des préfixes COPIX_*_PATH du plus spécifique au moins spécifique.
	 *
	 * @var array
	 */
	private static $_copixPathPrefixes = array(
		'COPIX_CACHE_PATH',
		'COPIX_LOG_PATH',
		'COPIX_TEMP_PATH',
		'COPIX_VAR_PATH',
		'COPIX_PROJECT_PATH',
		'COPIX_SMARTY_PATH',
		'COPIX_UTILS_PATH',
		'COPIX_CORE_PATH',
		'COPIX_PATH',	
	);

	/**
	 * Détermine si un chemin peut-être défini relativement à l'une des constantes COPIX_*_PATH.
	 *
	 * @param string $pPath Chemin à analyser.
	 * @return array Un tableau array($prefixe, $cheminRelatif), si aucun préfixe ne correspond $prefixe == null
	 */
	public static function getCopixPathPrefix($pPath) {
		foreach(self::$_copixPathPrefixes as $key) {
			@list(,$relativePath) = explode(constant($key), $pPath);
			if($relativePath) {
				return array($key, $relativePath);
			}
		}
		return array(null, $pPath);
	}

}

?>