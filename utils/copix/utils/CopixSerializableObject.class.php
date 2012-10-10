<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croes Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe qui gère la possibilité de placer des objets en session sans se préocuper de
 * la sérialisation / désérialisation.
 * @package copix
 * @subpackage core
 */
class CopixSerializableObject extends CopixClassProxy {
	/**
	 * Le type de l'élément sérialisé
	 * 
	 * @var string
	 */
	private $_type = null;
	
	/**
	 * verrou qui indique si l'objet en propriété est utilisable (sous entendu pas sérialisé)
	 */
	private $_ready = false;
	
	/**
	 * Constante pour indiquer que cet objet fait partit de l'autoload
	 * Avec la nouvelle méthode, il devient inutile. Conservé à des fins de compatibilité.
	 * @deprecated
	 */
	const AUTOLOADED = 0;

	/**
	 * Constante indiquant qu'une classe est chargée vai _daoInclude
	 */	
	const DAO = 1;
	
	/**
	 * Constante indiquant qu'une classe est chargée via _classInclude
	 *
	 */
	const MODULE_CLASS = 2;
	
	/**
	 * Constante indiquant qu'une classe est chargée via Copix::RequireOnce
	 *
	 */
	const FILE = 3;
	
	/**
	 * Expression régulière permettant de reconnaître un chemin qui correpond à une classe d'un module.
	 *
	 * @var string
	 */
	private static $_moduleClassRegex;

	/**
	 * Informations sur toutes les classes référencées.
	 *
	 * @var array
	 */
	protected static $_globalReferences = array();

	/**
	 * Informations sur les classes référencées par l'instance.
	 *
	 * @var array
	 */
	protected $_references = array();

	/**
	 * Constructeur, l'objet et sa définition s'il y a lieu
	 * @param	object	$pObject	l'objet à placer dans la session
	 * @param	string	$pFileName	le chemin de la définition du fichier
	 */
	public function __construct ($pObject, $pFileName = null){
		parent::__construct ($pObject);
		if (is_object($pObject)) {
			$this->_addReference(get_class($pObject), $pFileName, $pObject);
		}
		$this->_ready = true;
	}
	
	/**
	 * Ajoute les références nécessaires pour recharger une classe ou un objet indiqué plus tard.
	 *
	 * @param string $pClassName Classe de l'objet.
	 * @param string $pFileName Nom du fichier.
	 * @param object $pObject Instance de l'objet.
	 */
	protected function _addReference($pClassName, $pFileName = null, $pObject = null) {
		if (isset ($this->_references[$pClassName])) {
			return;
		}
		// On vérifie ici si la classe existe pour éviter les erreurs d'interprétations
		if (class_exists ($pClassName, false)) {
			$this->_references[$pClassName] = self::_resolveReference($pClassName, $pFileName, $pObject);
		}
	}

	/**
	 * Avant la sérialisation (qui va se produire pour la session)
	 * @return array	liste des propriétés à sérialiser.
	 */
	public function __sleep (){
		if ($this->_ready){
			$this->_object = serialize ($this->_object);
			// "Hack": récupère la liste des noms de classe à partir de la chaîne sérialisée.
			if (preg_match_all ('/O:\d+:"([^"]+)"/', $this->_object, $parts)) {
				foreach($parts[1] as $className) {
					$this->_addReference($className);
				}
			}
			// Ne stocke pas les références aux classes autoloadées
			$this->_references = array_filter ($this->_references);
		}
		return array ('_object', '_references');
	}

	/**
   	 * Retourne l'objet qui fait l'objet du proxy
   	 * @return object
   	 */
   	public function & getRemoteObject (){
   		if ($this->_ready){
   			return parent::getRemoteObject ();
   		}
   		$this->_prepareObject ();
   		return parent::getRemoteObject ();
   	}
	
   	/**
   	 * fonction prepare l'objet, inclusion et déserialisation
   	 */
   	private function _prepareObject (){
   		if(isset ($this->_type)) {
			switch ($this->_type) {
				case 'file': 
					Copix::RequireOnce ($this->_fileName);
					break;
					
				case 'class':
					_classInclude ($this->_fileName);
					break;
					
				case 'dao':
					_daoInclude ($this->_fileName);
					break;
			}
			
			// Supprime les anciennes infos
			unset ($this->_type);
			unset ($this->_fileName);
			
			// Désérialise
			$this->_object = unserialize ($this->_object);
	   		$this->_ready = true;
			return $this->getRemoteObject ();
		}
		
		// Enregistre les références de cet objet
		self::$_globalReferences = array_merge (self::$_globalReferences, $this->_references);
   	
		// Désérialise
		$this->_object = unserialize ($this->_object);
   		$this->_ready = true;
   	}
		
	/**
	 * Résouds la référence à une classe ou une instance.
	 *
	 * @param string $pClassName Classe de l'objet.
	 * @param string $pFileName Nom du fichier.
	 * @param object $pObject Instance de l'objet.
	 * @return array L'information de référence. 
	 */
	protected static function _resolveReference ($pClassName, $pFileName = null, $pObject = null) {
		if (isset (self::$_globalReferences[$pClassName])) {
			return self::$_globalReferences[$pClassName];
		}

		// Classe "anonyme" ou autoloadée
		if (strtolower ($pClassName) == 'stdclass' || CopixAutoloader::canAutoload ($pClassName))  {
			return self::$_globalReferences[$pClassName] = false;
		}
		$class = new ReflectionClass ($pClassName);

		// DAO ou enregistrement DAO
		if ((// Les class_exists sont nécessaires pour prévenir des segmentation fault (!)
				   (class_exists('ICopixDAORecord', false) && $class->implementsInterface ('ICopixDAORecord'))
				|| (class_exists('ICopixDAO', false) && $class->implementsInterface ('ICopixDAO'))
			)
			&& ($pFileName !== null || $pObject !== null)
		) {
			return self::$_globalReferences[$pClassName] = array(
				self::DAO, 
				$pFileName === null ? $pObject->getDAOId () : $pFileName,
				null
			);			
		}

		// On a un nom de fichier passé par l'utilisateur
		if ($pFileName !== null) {
			// Le fichier est lisible, on ne cherche pas plus loin
			if (is_readable ($pFileName)) {
				return self::$_globalReferences[$pClassName] = array(self::FILE, $pFileName);
			}

			// Si ça ressemble à un sélecteur Copix, on vérifie.
			if (preg_match ('/^\w+\|\w+$/', $pFileName)) {
				$selector = CopixSelectorFactory::create ($pFileName);
				$filePath = $selector->getPath (COPIX_CLASSES_DIR.strtolower($selector->fileName).'.class.php');				
				if (is_readable ($filePath)) {
					return self::$_globalReferences[$pClassName] = array(self::MODULE_CLASS, $pFileName);
				}
			}
			
			// Ca n'a pas marché 
		}

		// On a rien... essayons de deviner
		$type = self::FILE;
		$fileName = $class->getFileName ();
		
		// Vérifie si ça correpond à une classe de module
		if (preg_match (self::$_moduleClassRegex, $fileName, $parts)) {
			list (/**/, $basePath, $moduleName, $className) = $parts;
			if (CopixModule::isValid($moduleName, $basePath)) {
				// Bingo
				return self::$_globalReferences[$pClassName] = array(self::MODULE_CLASS, $moduleName.'|'.$className);
			}
		}
			
		// En dernier recours, stocke un fichier
		return self::$_globalReferences[$pClassName] = array ($type, $fileName);
	}

	/**
	 * Chargeur de classe.
	 *
	 * @param string $pClassName Nom de la classe à charger.
	 * @return boolean
	 */
	public static function autoload ($pClassName) {
		if (!isset (self::$_globalReferences[$pClassName]) || !self::$_globalReferences[$pClassName]) {
			return false;
		}
		list ($type, $fileName) = self::$_globalReferences[$pClassName];
		switch ($type) {
			case self::DAO:
				return _daoInclude ($fileName) ? true : false;
				
			case self::MODULE_CLASS: 
				return _classInclude ($fileName) ? true : false;
				
			case self::FILE:
				return Copix::RequireOnce ($fileName) ? true : false;
		}
		return false;
	}
	
	/**
	 * Simulation d'un constructeur "statique" 
	 */
	public static function _initialize () {
                if (!isset (self::$_globalReferences)){
                    self::$_globalReferences = array ();
                }
		// Enregistre l'autoloader
		spl_autoload_register (array ('CopixSerializableObject', 'autoload'));

		// Génère l'expression régulière pour analyser les chemins de modules
		$dirSep = preg_quote (DIRECTORY_SEPARATOR, '@');
		$classDir = preg_quote (preg_replace('@[/\x5C]@', DIRECTORY_SEPARATOR, COPIX_CLASSES_DIR), '@');
		self::$_moduleClassRegex = '@^(.+)'.$dirSep.'(.+?)'.$dirSep.$classDir.'(.+?)\.class\.php$@i';
	}
}

// Initialise la partie statique
CopixSerializableObject::_initialize ();