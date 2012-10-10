<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Croës Gérald, Jouanneau Laurent
 * @copyright	CopixTeam
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

	const DIRMOD=0775;
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
		return @file_get_contents ($pFilename, false);
	}

	/**
	 * Ecriture d'un fichier sur le disque dur
	 *
	 * Cette fonction est basée sur le code trouvé dans Smarty (http://smarty.php.net)
	 *
	 * @param	string	$pFileName le nom du fichier (le fichier sera crée ou remplacé)
	 * @param	mixed	$pData les données à écrire dans le fichier
	 * @return	bool 	si la fonction a correctement écrit les données dans le fichier
	 */
	public static function write ($pFileName, $pData){
		//file_put_contents('/tmp/log_write_g.txt', $pFileName."\n\r", FILE_APPEND);
		$_dirname = dirname ($pFileName);

		//If the $pFileName finish with / just createDir
		if ((($lastChar = substr ($pFileName, -1)) == '/') || ($lastChar == '\\')){
			self::createDir ($pFileName);
			return true;
		} else {
			//asking to create the directory structure if needed.
			self::createDir ($_dirname);
		}

		if (file_put_contents ($pFileName, $pData, LOCK_EX) === false){
			throw new CopixException (_i18n ('copix:copixfile.error.errorWhileWritingFile', array ($pFileName, null)));
		}

		@chmod($pFileName, self::FILEMOD);
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
	public static function delete ($pFilename) {
		$_dirname = dirname ($pFilename);

		// On vérifie si on n'a pas un fichier
		if ((($lastChar = substr ($pFilename, -1)) == '/') || ($lastChar == '\\')){
			return false;
		}

		if(!@is_writable ($_dirname)) {
			// On ne dispose pas des droits d'écriture, vérifions si le répertoire existe
			if(!@is_dir ($_dirname)) {
				throw new CopixException (_i18n ('copix:copixfile.error.directoryNotExists', array ($_dirname)));
			}
			if (!file_exists ($pFilename)) {
				throw new CopixException (_i18n ('copix:copixfile.error.fileNotFound', array ($pFilename)));
			}
			throw new CopixException (_i18n ('copix:copixfile.error.notWritable', array ($pFilename, $_dirname)));
		}

		if (@unlink($pFilename)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Création d'une arborescence de répertoires si elle n'existe pas.
	 * 
	 * @param	string	$pDirectory	le nom du répertoire que l'on souhaites créer
	 * 
	 * <code>
	 *    if (CopixFile::createDir (COPIX_TEMP_PATH.'chemin/complet/des/repertoires/a/creer/')){
	 *       //ok, le répertoire à bien été cré
	 *    }
	 * </code>
	 * 
	 * @return bool toujours vrai, self::_createDir lance une exception en cas d'échec
	 */
	public static function createDir ($pDirectory){
		if (!file_exists ($pDirectory)){
			if (!@mkdir($pDirectory, self::DIRMOD, true)) {
				throw new CopixException (_i18n ("copix:copixfile.error.creatingDirectory", array ($pDirectory)));
			}
			return true;
		}
		return false;
	}

	/**
	 * Recherche d'un pattern dans une arborescence de répertoire
	 *
	 * @param string $pPattern le pattern à rechercher (patterns supportés: filename.*.ext, *.ext, filenamestart*.ext)
	 * @param string $pPath le chemin dans lequel on va rechercher les fichiers
	 * @param bool $pRecursiveSearch si l'on va également rechercher dans les sous dossier (défaut = true)
	 * @return array of string une liste de fichier (chemins) correspondant à la recherche
	 */
	public static function search ($pPattern, $pPath, $pRecursiveSearch = true){
		$pPath = self::getRealPath($pPath);
		$pPath = self::trailingSlash ($pPath);
		$files = self::glob ($pPath.$pPattern);
		if ($pRecursiveSearch){
			foreach (self::glob ($pPath.'*', GLOB_ONLYDIR) as $file) {
				$files = array_merge ($files, self::search ($pPattern, $file, true));
			}
		}
		return $files;
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
		if (!is_dir (self::getRealPath ($pPath))) {
			return false;
		}
		$entries = self::glob (self::trailingSlash (self::getRealPath ($pPath)) . '*');

		// Compte le nombre d'entrées (avant filtrage)
		$remaining = count($entries);

		// Applique le filter
		if($pFilterCallback) {
			$entries = array_filter($entries, $pFilterCallback);
		}

		// Traite les entrées
		foreach($entries as $entry) {
			// On ne traite pas les répertoires . et ..
			if ($entry == '.' || $entry == '..') {
				continue;
			}
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
		$entries = self::glob($basePath.$relativePath.'*');
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
	 * Liste des préfixes COPIX_*_PATH du plus spécifique au moins spécifique.
	 *
	 * @var array
	 */
	private static $_copixPathPrefixes = array(
		'COPIX_CACHE_PATH' => true,
		'COPIX_LOG_PATH' => true,
		'COPIX_TEMP_PATH' => true,
		'COPIX_VAR_PATH' => true,
		'COPIX_PROJECT_PATH' => true,
		'COPIX_SMARTY_PATH' => true,
		'COPIX_UTILS_PATH' => true,
		'COPIX_PATH' => true,
	);

	/**
	 * Détermine si un chemin peut-être défini relativement à l'une des constantes COPIX_*_PATH.
	 *
	 * @param string $pPath Chemin à analyser.
	 * @return array Un tableau array($prefixe, $cheminRelatif), si aucun préfixe ne correspond $prefixe == null
	 */
	public static function getCopixPathPrefix($pPath) {
		$pPath = self::getRealPath($pPath);
		foreach(self::$_copixPathPrefixes as $name=>$path) {
			if($path === true) {
				$path = self::$_copixPathPrefixes[$name] = self::getRealPath(constant($name));
			}
			$length = strlen ($path);
			// getRealPath va renvoyer false si le répertoire n'existe pas
			if ($path !== false && substr ($pPath, 0, $length) == $path) {
				return array($name, substr($pPath,$length));
			}
		}
		return array(null, $pPath);
	}

	/**
	 * Fonction glob surchargeant glob PHP et safe_glob
	 *
	 * @param string $pattern
	 * @param int $flags
	 * @return unknown
	 */
	public static function glob ($pattern, $flags=null){
		$result = glob ($pattern, $flags);
		if ($result === false){
			$result = self::_safe_glob ($pattern, $flags);
		}
		return $result;
	}
	/**
	 * Fonction safe_glob pour pallier les sécurités mise en place sur certains hébergeur
	 *
	 * @param string $pattern
	 * @param int $flags
	 * @return array ou boolean
	 */
	private static function _safe_glob ($pattern, $flags=null){
		$split = explode('/',$pattern);
		$match = array_pop ($split);
		$path = implode ('/',$split);
		if (($dir = opendir ($path)) !== false) {
			$glob = array();
			while(($file = readdir ($dir))!== false) {
				if (fnmatch ($match,$file)) {
					if ((is_dir ("$path/$file")) || (!($flags & GLOB_ONLYDIR))) {
						if ($flags & GLOB_MARK) {
							$file.='/';
						}
						$glob[]=$file;
					}
				}
			}
			closedir($dir);
			if (! ($flags & GLOB_NOSORT)) {
				sort($glob);
			}
			return $glob;
		} else {
			return false;
		}
	}
	
	/**
	 * Renvoi le répertoire réel (utilise realpath si activé sur le serveur, sinon, renvoie un équivalent)
	 *
	 * @param string $pPath Répertoire dont on veut le realpath
	 * @return string
	 */
	public static function getRealPath ($pPath) {
		$config = CopixConfig::instance ();
		
		// Supprime le slash terminal
		if ($trailingSlash = (strlen($pPath) > 1 && (substr($pPath,-1) == '/' || substr($pPath,-1) == '\\'))) {
			$pPath = substr($pPath, 0, -1);			
		}

		// Utilise la fonction realPath standard si possible
		if ($config->realPathDisabled === false) {
			if (($realPath = realpath ($pPath)) === false) {
				return false;
			}
		// Implémentation maison
		} else {
			static $realPathCache = array ();
			
			// Vérifie le cache
			if (isset ($realPathCache[$pPath])) {
				$realPath = $realPathCache[$pPath];
				
			} else {
	
				// Transforme le chemin en chemin absolu
				if ($config->osIsWindows ()) {
					if (!preg_match ('@^[a-z]:[/\x5c]@i', $pPath)) {
						$pPath = getcwd ().DIRECTORY_SEPARATOR.$pPath;
					}
				} elseif (substr ($pPath[0],0,1) != '/') {
					$pPath = getcwd ().DIRECTORY_SEPARATOR.$pPath;			
				}
				
				// Si le fichier n'existe pas : retourne FALSE
				if (!is_readable ($pPath)) {
					$realPathCache[$pPath] = false;
					return false;
				}
				
				// Découpe le chemin
				$parts = preg_split ('@([/\x5c])@', $pPath, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE);
				
				// On maintient à la fois la liste des morceaux et le chemin actuel
				$pathItems = array ();
				$realPath = '';
				
				// Essaie de trouver un préfixe en cache
				for ($i = count ($parts)-1; $i >= 0; $i--) {
					$prefix = substr ($pPath,0,$parts[$i][1]-1);
					if (isset ($realPathCache[$prefix])) {
						// On a trouvé un chemin en cache, on repart à partir de celui-ci
						$realPath = $realPathCache[$prefix];
						$pathItems = explode (DIRECTORY_SEPARATOR, $realPath);
						$parts = array_slice ($parts, $i);
						break;
					}
				}

				// Traite les parties après le préfixe que l'on a trouvé (ou non)		
				foreach ($parts as $partInfo) {
					list ($item, $offset) = $partInfo;
					
					if ($item == '.') {
						continue;
					} elseif ($item == '..') {
						array_pop ($pathItems);
						$realPath = implode (DIRECTORY_SEPARATOR, $pathItems);
					} else {
						$pathItems[] = $item;
						$realPath .= (empty ($realPath) ? '' : DIRECTORY_SEPARATOR).$item;
					}
		
					// Résolution des liens symboliques
					if( is_link ($realPath)) {
						$realPath = readlink ($realPath);
						$pathItems = explode (DIRECTORY_SEPARATOR, $path);
					}
					
					//On rajoute le slash de départ sous les systèmes linux
					if (!$config->osIsWindows ()){
						if (substr ($realPath, 0, 1) != '/'){
							$realPath = '/'.$realPath;
						}
					}

					// Met en cache
					$realPathCache[substr ($pPath,0,$offset).$item] = $realPath;
					$realPathCache[$realPath] = $realPath;
				}
				
			}
			
		}
		
		// Ajoute le slash terminal si nécessaire
		if ($trailingSlash && substr($realPath, -1) != DIRECTORY_SEPARATOR) {
			$realPath .= DIRECTORY_SEPARATOR;
		}

		// Retourne ce qu'on a trouvé
		return $realPath;		
	} 
}
