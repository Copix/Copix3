<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Guillaume Perréal
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe responsable du chargement automatique de classes.
 * 
 * @package		copix
 * @subpackage	core
 */
class CopixAutoloader {
	
	/**
	 * Instance du singleton
	 * 
	 * @var CopixAutoloader
	 */
	private static $_instance = null;
	
	/**
	 * Chemins des classes 
	 * Tableau de la forme 'nom_de_classe_en_minuscule' => 'chemin du fichier à charger'.
	 * 
	 * @var array
	 */
	private $_classPaths = array ();

	/**
	 * Renvoie le singleton
	 * 	 
	 * @return CopixAutoloader Singleton
	 */
	public static function getInstance () {
		if (!self::$_instance) {
			self::$_instance = new CopixAutoloader ();
		}
		return self::$_instance;
	}

	/**
	 * Charge une classe.
	 * 
	 * @param string $pClassname Nom de la classe
	 * @return boolean True : la classe a été chargée, false : la classe était déja chargée
	 * @see CopixAutoloader::load ()
	 */
	public static function autoload ($pClassname) {
		return self::getInstance ()->load ($pClassname);
	}
	
	/**
	 * Vérifie si l'autoloader peut charger une classe.
	 * 
	 * @param string $pClassname Nom de la classe
	 * @return boolean True : la classe est connue, false : la classe n'est pas connue
	 * @see CopixAutoloader::isKnown ()
	 */
	public static function canAutoload ($pClassname) {
		return self::getInstance ()->isKnown ($pClassname);
	}

	/**
	 * Créer l'unique instance de la classe
	 * Charge le contenu du fichier COPIX_CLASSPATHS_FILE et initialise $this->_classPaths avec la valeur de retour.
	 */
	private function __construct () {
		$this->_classPaths = include (COPIX_CLASSPATHS_FILE);
	}
	
	/**
	 * Tente de charger une classe Copix par son nom
	 *
	 * @param string $pClassname Le nom de la classe à charger.
	 * @return boolean True si la classe à été chargée (ou l'était déjà), false si la classe est inconnue. 
	 */
	public function load ($pClassname) {
		$lowerClassName = strtolower ($pClassname);
		if (isset ($this->_classPaths[$lowerClassName])) {
			return Copix::RequireOnce ($this->_classPaths[$lowerClassName]);
		} else {
			return false;
		}
	}

	/**
	 * Tente de charger une classe Copix par son nom
	 *
	 * @param string $pClassname Le nom de la classe à charger.
	 * @return boolean True si la classe est connue de cet autoloader. 
	 */
	public function isKnown ($pClassname) {
		return isset ($this->_classPaths[strtolower ($pClassname)]);
	}	
	/**
	 * Force une reconstruction du fichier COPIX_CLASSPATHS_FILE
	 * 
	 * @see CopixClassPathBuilder
	 */
	public function rebuildClassPath () {
		Copix::RequireOnce (COPIX_UTILS_PATH . 'CopixClassPathBuilder.class.php');
		$builder = new CopixClassPathBuilder ();
		$builder->build ();
	}
	
}

/*
 * Met en place CopixAutoloader 
 * On référence la méthode statique. Ainsi le singleton ne sera pas créé avant la première tentative de chargement
 */
spl_autoload_register (array ('CopixAutoloader', 'autoload'));
?>