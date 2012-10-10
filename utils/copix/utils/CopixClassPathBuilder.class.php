<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Guillaume Perréal
 * @copyright	2001-2008 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe utilitaire chargée de recontruire le fichier COPIX_CLASSPATHS_FILE.
 *
 * La méthode est la suivante :
 * - on charge tous les fichiers de classe trouves dans les répertoires indiqués par $basePaths,
 *   en stockant au passage le chemin relatif à l'un des constantes COPIX_*_PATH,
 * - on récupère la liste de toutes les classes et interfaces Copix,
 * - pour chacune, on utilise la réflection pour savoir dans quelle fichier elles sont déclarées,
 * - on génère le contenu du fichier COPIX_CLASSPATHS_FILE en utilisant les constantes.
 *
 * @author Guillaume Perréal
 */
class CopixClassPathBuilder {

	/**
	 * Chemins contenant les classes à lister.
	 *
	 * Tableau de la forme "chemin" => nom de la constante.
	 * L'ordre dans lequelle le chemins sont indiqués est important.
	 *
	 * Exemple : array(COPIX_UTILS_PATH => 'COPIX_UTILS_PATH')
	 *
	 * @var array
	 */
	private $basePaths = array();

	/**
	 * Table de correspondance entre chemins complets et chemins relatif aux constants COPIX_*_PATH.
	 *
	 * Tableau de la forme "chemin complet" => "chaine d'incluude"
	 *
	 * @var array
	 */
	private $fileIncludes = array();

	/**
	 * Table des chemins de classes potentiels.
	 * 
	 * Tableau de la forme 'nom_de_classe' => tableu des 'fichiers_pouvant_contenir la déclaration'.
	 * 
	 * Il est rempli lors du parcours des dossiers puis lors de la recherche 
	 * de déclarations de classes et d'interfaces. Il peut éventuellement 
	 * contenir des informations erronnées, notamment lorsque les déclarations 
	 * de classes et d'interfaces sont en commentaires.  
	 *
	 * @var array
	 */
	private $potentialClassPaths = array();

	/**
	 * Liste des sous-répertoires de COPIX_PATH à prendre en compte.
	 */
	static private $copixDirs = array(
		'auth', 'cache', 'dao', 'core', 'forms', 'lists', 'datasource', 
		'db', 'events', 'ldap', 'log', 'taglib', 'modules', 'validator',
		'field', 'coding', 'plugin', 'filter', 'lock'
	);

	/**
	 * Construit le builder.
	 *
	 * Initialise $this->basePaths.
	 */
	public function __construct() {
		$this->basePaths = array(
			COPIX_UTILS_PATH => 'COPIX_UTILS_PATH',
			COPIX_CORE_PATH => 'COPIX_CORE_PATH',
			COPIX_PATH => 'COPIX_PATH',
		);
	}

	/**
	 * Filtre les dossiers à explorer à la recherche de fichiers de classes.
	 *
	 * Accepte tous les répertoires listés dans self::$copixDirs.
	 *
	 * @see CopixFile::findFiles()
	 */
	public function _classDirFilter($fullPath, $relativePath, $basePath, $depth) {
		return ($depth > 1) || in_array(basename($fullPath), self::$copixDirs);
	}
	
	/**
	 * Ajoute une entrée à $this->potentialClassPaths.
	 *
	 * @param string $pClassName Nom de la classe.
	 * @param string $pFilePath Chemin du fichier pouvant le contenir.
	 */
	protected function _addPotentialClassPath($pClassName, $pFilePath) {
		$className = strtolower($pClassName);
		if(isset($this->potentialClassPaths[$className])) {
			$this->potentialClassPaths[$className][] = $pFilePath;
		} else {
			$this->potentialClassPaths[$className] = array($pFilePath);
		}
	}

	/**
	 * Filtre les fichiers de classe.
	 *
	 * Accepte le fichiers nommés Copix*.class.php et ICopix*.class.php.
	 * Remplit $this->fileIncludes au fur et à mesure.
	 *
	 * @see CopixFile::findFiles()
	 */
	public function _classFileFilter($fullPath, $relativePath, $basePath, $depth) {
		if(!is_dir($fullPath) && preg_match('/^(I?Copix\w+)\.class\.php$/i', basename($fullPath), $matches)) {
				
			// Ajout le lien classe=>fichier dans potentialClassPaths
			$this->_addPotentialClassPath($matches[1], $fullPath);
				
			$realPath = CopixConfig::getRealPath($fullPath);
			if(isset($this->fileIncludes[$realPath])) {
				return false;
			}
			$this->fileIncludes[$realPath] = sprintf("%s.'%s'", $this->basePaths[$basePath], $relativePath);
			return true;
		}
		return false;
	}

	/**
	 * Filtre les noms de classes et d'interface.
	 *
	 * Accepte tous les classes et interfaces nommées Copix* et ICopix*.
	 *
	 * @param string $className
	 * @return boolean True si la classe est acceptée.
	 */
	public function _classNameFilter($className) {
		return preg_match('/^I?Copix\w+$/i', $className);
	}

	/**
	 * Lit le contenu des fichiers PHP sans les compiler, à la recherche de déclarations de classes et d'interfaces.
	 *
	 * L'objectif est de remplir le tableau $this->potentialClassPaths sans pour autant compiler le PHP.
	 * Il est possible que l'on trouve des déclarations en commentaires, c'est pour cela que l'on parle de chemin "potentiel".
	 *
	 * @param array $pFiles Liste des fichiers à analyser.
	 *
	 * @see $this->potentialClassPaths
	 */
	public function preParse($pFiles) {
		foreach($pFiles as $file) {
			if(preg_match_all('/\s*(?:interface|class)\s+(I?Copix\w+)\s+.*\{/', CopixFile::read($file), $matches, PREG_PATTERN_ORDER)) {
				foreach($matches[1] as $className) {
					$this->_addPotentialClassPath($className, $file);
				}
			}
		}
	}

	/** Méthode d'autoloading spécifique.
	 *
	 * Essaie de charger une classe selon les informations de $this->potentialClassPaths.
	 * Cela permet de résoudre les dépendances lors de la phase de chargement des fichiers.
	 *
	 * @param string $pClassName Nom de la classe à charger.
	 *
	 * @see $this->potentialClassPaths
	 */
	public function autoload($pClassName) {
		$className = strtolower($pClassName);
		if(isset($this->potentialClassPaths[$className])) {
			$files = $this->potentialClassPaths[$className];
			unset($this->potentialClassPaths[$className]);
			foreach($files as $file) {
				Copix::RequireOnce($file);
				if(class_exists($pClassName, false)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Reconstruit le fichier COPIX_CLASSPATHS_FILE.
	 *
	 * Nécessite les droits d'écriture dans COPIX_CLASSPATHS_FILE. Cette opération est réservée aux développeurs de Copix.
	 */
	public function build() {
		// Vérifie qu'on ait ce qu'il faut
		if(!is_writable(COPIX_CLASSPATHS_FILE)) {
			throw new Exception("Impossible d'écrire dans ".COPIX_CLASSPATHS_FILE);
		}

		// Récupère la liste des fichiers
		$files = CopixFile::findFiles(array_keys($this->basePaths), array($this, '_classFileFilter'),  array($this, '_classDirFilter'));

		// Effectue une "évaluation" des fichiers pour y trouver des classes/interfaces potentielles
		$this->preParse($files);

		// Met en place notre autoloader maison
		$autoloader = array($this, 'autoload');
		spl_autoload_register($autoloader);

		// Charge tout
		foreach($files as $file) {
			include_once($file);
		}

		// Supprime l'autoloader maison
		spl_autoload_unregister($autoloader);

		// Récupère toutes les classes et interfaces
		$classes = array_merge(
		get_declared_classes(),
		get_declared_interfaces()
		);

		// Filtre celles de copix
		$classes = array_filter($classes, array($this, '_classNameFilter'));
			
		// Genère le fichier classpath
		$classPathCode  = "<?php\n";
		$classPathCode .= "/** Chemins des classes.\n";
		$classPathCode .= "  * Ce fichier est généré par CopixClassPathBuilder, ne pas le modifier !\n";
		$classPathCode .= "  * NE JAMAIS LE SUPPRIMER NON PLUS !\n";
		$classPathCode .= "  * Dernière génération le ".date("Y-m-d H:i:s")."\n";
		$classPathCode .= "*/\n";
		$classPathCode .=" return array(\n";
		sort($classes);
		foreach($classes as $class) {
			$reflection = new ReflectionClass($class);
			$fileName = $reflection->getFileName();
			if(isset($this->fileIncludes[$fileName])) {
				$classPathCode .= sprintf("\t'%s' => %s,\n", strtolower($class), $this->fileIncludes[$fileName]);
			}
		}
		$classPathCode .= ");\n";
		$classPathCode .= "?>";

		// Ecrit le fichier
		CopixFile::write(COPIX_CLASSPATHS_FILE, $classPathCode);
	}

}

?>