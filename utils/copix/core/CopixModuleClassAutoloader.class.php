<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		GÃ©rald CroÃ«s
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet l'autoload des classes de module dans Copix
 * 
 * Cette classe ne parcours que les classes des modules dÃ©jÃ  installÃ©s, situÃ©es dans des fichiers sous "/classes"
 * @package copix
 * @subpackage core 
 */
class CopixModuleClassAutoloader {
	/**
	 * La liste des classes trouvÃ©es avec le tokenizer
	 *
	 * @var array
	 */
	private static $_classIncludes = array ();

	/**
	 * La liste des classes dÃ©jÃ  chargÃ©es
	 *
	 * @var array
	 */
	private static $_classes = array ();
	
	/**
	 * Chargement de la classe demandÃ©e
	 *
	 * @param string $pClassName le nom de la classe recherchÃ©e
	 * @return boolean
	 */
	public static function autoload ($pClassName){
		$pClassName = strtolower ($pClassName);
		$toReturn = self::_autoload ($pClassName);
		return $toReturn;

	}
	
	/**
	 * Indique si l'on peut charger automatiquement la classe donnÃ©e en paramÃ¨tre
	 *
	 * @param string $pClassName le nom de la classe
	 * @return boolean
	 */
	public static function canAutoload ($pClassName){
		return self::autoload (strtolower ($pClassName));
	}
	
	/**
	 * RÃ©alisation concrÃ¨te de l'autoload (pouvant se rappeler avec re-gÃ©nÃ©ration des fichiers). 
	 *
	 * @param string $pClassName le nom de la classe recherchÃ©e
	 * @param boolean $pCanRegenerate si l'on autorise ou non la regÃ©nÃ©ration des caches d'autoload
	 * @return boolean
	 */
	private static function _autoload ($pClassName, $pCanRegenerate = true){
		//on va tenter de charger les classes dans l'ordre de la pile de contexte 
		// (on a plus de chance de trouver de suite la classe concernÃ©e)
		foreach ($stack = array_unique (CopixContext::getStack ()) as $context){
			if (self::_loadClass ($pClassName, $context)){
				return true;
			}
		}
		
		//Finalement on tente tous les modules installÃ©s
		foreach ($diff = array_diff (CopixModule::getList (false), $stack) as $moduleName){
			if (self::_loadClass ($pClassName, $moduleName)){
				return true;
			}
		}

		//Si on a le droit de tener la regÃ©nÃ©ration du fichier d'autoload, on retente l'histoire
		if ($pCanRegenerate && (CopixConfig::instance ()->getMode () === CopixConfig::DEVEL)){
			self::_includesAll ();
			self::_generateCacheFiles ();
			return self::_autoload ($pClassName, false);
		}

		//on a vraiment rien trouvÃ©.
		return false;
	} 
	
	/**
	 * Inclusion de toute les classes php des modules installÃ©s
	 *  
	 * Attention, seules les fichiers ayant l'extension .class.php et situÃ©s dans les rÃ©pertoires /classes
	 * seront inclus.
	 */
	private static function _includesAll (){
		//Inclusion de toute les classes connues
		foreach (CopixModule::getList (false) as $module){
			$directories = new AppendIterator ();

			//on regarde s'il existe un rÃ©pertoire "classes".
			if (is_readable ($classPath = CopixModule::getPath ($module).'classes')){
				$directories->append (new RecursiveIteratorIterator (new RecursiveDirectoryIterator ($classPath)));				
			}
			
			//on regarde s'il existe un rÃ©pertoire "taglib"
			if (is_readable ($classPath = CopixModule::getPath ($module).'taglib')){
				$directories->append (new RecursiveIteratorIterator (new RecursiveDirectoryIterator ($classPath)));				
			}

			//on regarde s'il existe un rÃ©pertoire "plugins"
			if (is_readable ($classPath = CopixModule::getPath ($module).'plugins')){
				$directories->append (new RecursiveIteratorIterator (new RecursiveDirectoryIterator ($classPath)));				
			}
			
			//On va filtrer les fichiers php depuis les rÃ©pertoires trouvÃ©s.
			$files = new CopixExtensionFilterIteratorDecorator ($directories);
			$files->setExtension ('.php');

			foreach ($files as $fileName){
				$classes = self::_extractClasses ((string) $fileName);
				self::_pushClassIncludes ($classes, $module);
			}
		}
	}

	/**
	 * Extraction des classes dÃ©clarÃ©es dans le fichier
	 *
	 * @param string $pFileName le fichier d'ou extraire les classes
	 * @return array [classes] = filename
	 */
	private static function _extractClasses ($pFileName){
		$toReturn = array ();
		
		$tokens = token_get_all (CopixFile::read ($pFileName));

		$classHunt = false;
		foreach ($tokens as $token){
			if (is_array ($token)){
				if ($token[0] === T_INTERFACE || $token[0] === T_CLASS){
					$classHunt = true;
					continue;
				}

				if ($classHunt && $token[0] === T_STRING){
					$toReturn[$token[1]] = $pFileName;
					$classHunt = false;
				}
			}
		}

		return $toReturn;
	}

	/**
	 * Ajout des classes trouvÃ©es dans le processus de recherche 
	 * @param array $pClasses les classes trouvÃ©es
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
	 * GÃ©nÃ©ration d'un fichier par module avec la liste des classes qu'il embarque.
	 */
	private static function _generateCacheFiles (){
		//recherche dans toutes les classes connues d'ou elles proviennent.
		//on ne sÃ©lectionnera que celles qui proviennent d'un module de chez nous pour les sauvegarder
		foreach (CopixModule::getList (false) as $moduleName){
			//on parcours par liste de module pour crÃ©er des fichiers vides pour les modules
			//qui n'embarquent pas de classe
			$classes = isset (self::$_classIncludes[$moduleName]) ? self::$_classIncludes[$moduleName] : array ();
			$generator = new CopixPHPGenerator ();
			$classesDeclaration = $generator->getVariableDeclaration ('$classes', $classes);
			$classesDeclaration = $generator->getPHPTags ($classesDeclaration);

			CopixFile::write (self::_makeFileName ($moduleName), $classesDeclaration);
		}

		self::$_classIncludes = array ();
	}
	
	/**
	 * Tente de charger la classe dans le module donnÃ©  
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
		if (! is_readable (self::_makeFileName ($pModuleName))){
			self::_includesAll ();
			self::_generateCacheFiles ();
		}
		
		include (self::_makeFileName ($pModuleName));
		self::$_classes[$pModuleName] = $classes;
	}
	
	/**
	 * CrÃ©ation du nom de fichier de cache pour les classes du module donnÃ©
	 *
	 * @param string $pModuleName le nom du module dont on souhaite connaitre le nom de fichier pour l'autoload 
	 */
	private static function _makeFileName ($pModuleName){
		return COPIX_TEMP_PATH.'cache/php/autoload/'.$pModuleName.'.php';
	}
}

spl_autoload_register (array ('CopixModuleClassAutoloader', 'autoload'));