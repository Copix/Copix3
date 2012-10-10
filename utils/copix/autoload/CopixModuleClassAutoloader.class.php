<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

if (!defined ('T_NAMESPACE')) {
    define ('T_NAMESPACE', 'T_NAMESPACE');
}
/**
 * Permet l'autoload des classes de module dans Copix
 *
 * Cette classe ne parcours que les classes des modules déjà installés, situées dans des fichiers sous "/classes"
 * @package copix
 * @subpackage core
 */
class CopixModuleClassAutoloader {
	/**
	 * La liste des classes trouvées avec le tokenizer
	 *
	 * @var array
	 */
	private static $_classIncludes = array ();

	/**
	 * La liste des classes déjà chargées
	 *
	 * @var array
	 */
	private static $_classes = array ();

	/**
	 * Indique s'il est possible de regenérer.
	 *
	 * @var boolean
	 */
	private static $_canRegenerate = true;

	/**
	 * Chargement de la classe demandée
	 *
	 * @param string $pClassName le nom de la classe recherchée
	 * @return boolean
	 */
	public static function autoload ($pClassName){
        	return self::_autoload (strtolower ($pClassName));
	}

	/**
	 * Indique si l'on peut charger automatiquement la classe donnée en paramètre
	 *
	 * @param string $pClassName le nom de la classe
	 * @return boolean
	 */
	public static function canAutoload ($pClassName){
		return self::autoload ($pClassName);
	}

	/**
	 * Réalisation concrète de l'autoload (pouvant se rappeler avec re-génération des fichiers).
	 *
	 * @param string $pClassName le nom de la classe recherchée
	 * @return boolean
	 */
	private static function _autoload ($pClassName){
		//on va tenter de charger les classes dans l'ordre de la pile de contexte
		// (on a plus de chance de trouver de suite la classe concernée)
		foreach ($stack = array_unique (CopixContext::getStack ()) as $context){
			if (self::_loadClass ($pClassName, $context)){
				return true;
			}
		}

		//Finalement on tente tous les modules installés
		foreach ($diff = array_diff (CopixModule::getList (Copix::installed ()), $stack) as $moduleName){
			if (self::_loadClass ($pClassName, $moduleName)){
				return true;
			}
		}

		//Si on a le droit de tenter la regénération du fichier d'autoload, on retente l'histoire
		if (self::$_canRegenerate && (CopixConfig::instance ()->getMode () === CopixConfig::DEVEL)){
			self::$_canRegenerate = false;
			self::_includesAll ();
			self::_generateCacheFiles ();
			return self::_autoload ($pClassName);
		}

		//on a vraiment rien trouvé.
		return false;
	}

	/**
	 * Inclusion de toute les classes php des modules installés
	 *
	 * Attention, seules les fichiers ayant l'extension .class.php et situés dans les répertoires /classes
	 * seront inclus.
	 */
	private static function _includesAll (){
		//Inclusion de toute les classes connues
		foreach (CopixModule::getList (Copix::installed ()) as $module){
			$directories = new AppendIterator ();

			//on regarde s'il existe un répertoire "classes".
			if (is_readable ($classPath = CopixModule::getPath ($module).'classes')){
				$directories->append (new RecursiveIteratorIterator (new RecursiveDirectoryIterator ($classPath)));
			}

			//on regarde s'il existe un répertoire "taglib"
			if (is_readable ($classPath = CopixModule::getPath ($module).'taglib')){
				$directories->append (new RecursiveIteratorIterator (new RecursiveDirectoryIterator ($classPath)));
			}

			//on regarde s'il existe un répertoire "plugins"
			if (is_readable ($classPath = CopixModule::getPath ($module).'plugins')){
				$directories->append (new RecursiveIteratorIterator (new RecursiveDirectoryIterator ($classPath)));
			}

			//on regarde s'il existe un répertoire "actiongroups"
			if (is_readable ($classPath = CopixModule::getPath ($module).'actiongroups')){
				$directories->append (new RecursiveIteratorIterator (new RecursiveDirectoryIterator ($classPath)));
			}

			//on regarde s'il existe un répertoire "zones"
			if (is_readable ($classPath = CopixModule::getPath ($module).'zones')){
				$directories->append (new RecursiveIteratorIterator (new RecursiveDirectoryIterator ($classPath)));
			}

			//On va filtrer les fichiers php depuis les répertoires trouvés.
			$files = new CopixExtensionFilterIteratorDecorator ($directories);
			$files->setExtension ('.php');

			foreach ($files as $fileName){
				$classes = self::_extractClasses ((string) $fileName);
				self::_pushClassIncludes ($classes, $module);
			}
		}
	}

	/**
	 * Extraction des classes déclarées dans le fichier
	 *
	 * @param string $pFileName le fichier d'ou extraire les classes
	 * @return array [classes] = filename
	 */
	private static function _extractClasses ($pFileName){
        $toReturn = array();
        $tokens = token_get_all(file_get_contents($pFileName, false));

        $currentNamespace = '';
        $namespaceHunt = false;
        $validatedNamespaceHunt = false;
        $classHunt = false;
        foreach ($tokens as $token) {
            if (is_array($token)) {
                if ($token[0] === T_INTERFACE || $token[0] === T_CLASS) {
                    $classHunt = true;
                    continue;
                } elseif ($token[0] === T_NAMESPACE) {
                    $namespaceHunt = true;
                    continue;
                }
                if ($classHunt && $token[0] === T_STRING) {
                    $toReturn[(strlen($currentNamespace) > 0 ? $currentNamespace.'\\' : '').$token[1]] = $pFileName;
                    $classHunt = false;
                } elseif ($namespaceHunt && $validatedNamespaceHunt
                          && ($token[0] === T_STRING || $token[0] === T_NS_SEPARATOR)) {
                    $currentNamespace .= $token[1];
                } elseif ($namespaceHunt && !$validatedNamespaceHunt && $token[0] === T_WHITESPACE) {
                    $currentNamespace = '';
                    $validatedNamespaceHunt = true;
                } elseif ($namespaceHunt && !$validatedNamespaceHunt && $token[0] !== T_WHITESPACE) {
                    $namespaceHunt = false;
                }
            } else {
                if ($token === ';' || $token === '{') {
                    //ends the "default" namespace only
                    if ($namespaceHunt && !$validatedNamespaceHunt && $token === '{') {
                        $currentNamespace = '';
                    }
                    $classHunt = false;
                    $namespaceHunt = false;
                    $validatedNamespaceHunt = false;
                }
            }
        }
        return $toReturn;
	}

	/**
	 * Ajout des classes trouvées dans le processus de recherche
	 * @param array $pClasses les classes trouvées
	 * @param string $pModuleName le nom du module d'ou proviennent les classes
	 */
	private static function _pushClassIncludes ($pClasses, $pModuleName){
		foreach ($pClasses as $className=>$fileName){
			if (!isset (self::$_classIncludes[$pModuleName])){
				self::$_classIncludes[$pModuleName] = array ();
			}
			self::$_classIncludes[$pModuleName][strtolower ($className)] = $fileName;
		}
	}

	/**
	 * Génération d'un fichier par module avec la liste des classes qu'il embarque.
	 */
	private static function _generateCacheFiles (){
                //recherche dans toutes les classes connues d'ou elles proviennent.
		//on ne sélectionnera que celles qui proviennent d'un module de chez nous pour les sauvegarder
		foreach (CopixModule::getList (Copix::installed ()) as $moduleName){
			//on parcours par liste de module pour créer des fichiers vides pour les modules
			//qui n'embarquent pas de classe
			$classes = isset (self::$_classIncludes[$moduleName]) ? self::$_classIncludes[$moduleName] : array ();
			$generator = new CopixPHPGenerator ();
			$classesDeclaration = $generator->getVariableDeclaration ('$classes', $classes);
			$classesDeclaration = $generator->getPHPTags ($classesDeclaration);

			CopixFile::write (self::getCacheFileName ($moduleName), $classesDeclaration);
		}

		self::$_classIncludes = array ();
	}

	/**
	 * Tente de charger la classe dans le module donné
	 *
	 * @param string $pClassName le nom de classe que l'on recherche
	 * @param string $pModule    dans quel module on va tenter de charger la classe
	 */
	private static function _loadClass ($pClassName, $pModule){
		if (! array_key_exists ($pModule, self::$_classes)){
			self::_loadModuleClasses ($pModule);
		}

		if (isset (self::$_classes[$pModule][$pClassName])){
			Copix::RequireOnce (self::$_classes[$pModule][$pClassName]);
			return true;
		}

		return false;
	}

	/**
	 * Demande de chargement du fichier en cache des classes du module
	 *
	 * @param string $pModule le nom du module
	 */
	private static function _loadModuleClasses ($pModuleName){
		if (! is_readable ($fileName = self::getCacheFileName ($pModuleName))){
            CopixLock::lock('autoload');
            if (! is_readable ($fileName = self::getCacheFileName ($pModuleName))){
			    self::_includesAll ();
			    self::_generateCacheFiles ();
            }
            CopixLock::unlock('autoload');
		}

		include ($fileName);
		self::$_classes[$pModuleName] = $classes;
	}

	/**
	 * Création du nom de fichier de cache pour les classes du module donné
	 *
	 * @param string $pModuleName le nom du module dont on souhaite connaitre le nom de fichier pour l'autoload
	 */
	public static function getCacheFileName ($pModuleName) {
		return COPIX_CACHE_PATH . 'autoload/' . $pModuleName . '.php';
	}
}

spl_autoload_register (array ('CopixModuleClassAutoloader', 'autoload'));