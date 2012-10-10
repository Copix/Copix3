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
	 * Chemins des classes 
	 * Tableau de la forme 'nom_de_classe_en_minuscule' => 'chemin du fichier à charger'.
	 * 
	 * @var array
	 */
	private static $_classPaths = false;

	/**
	 * Charge une classe.
	 * 
	 * @param string $pClassname Nom de la classe
	 * @return boolean True : la classe a été chargée, false : la classe était déja chargée
	 * @see CopixAutoloader::load ()
	 */
	public static function autoload ($pClassname) {
		return self::load ($pClassname);
	}
	
	/**
	 * Vérifie si l'autoloader peut charger une classe.
	 * 
	 * @param string $pClassname Nom de la classe
	 * @return boolean True : la classe est connue, false : la classe n'est pas connue
	 * @see CopixAutoloader::isKnown ()
	 */
	public static function canAutoload ($pClassname) {
		return self::isKnown ($pClassname);
	}

	/**
	 * Tente de charger une classe Copix par son nom
	 *
	 * @param string $pClassname Le nom de la classe à charger.
	 * @return boolean True si la classe à été chargée (ou l'était déjà), false si la classe est inconnue. 
	 */
	public static function load ($pClassname) {
                if (self::$_classPaths === false){
                    include (COPIX_CLASSPATHS_FILE);
                    self::$_classPaths = $classes;
                }

		$lowerClassName = strtolower ($pClassname);

		// Chargement classique
		if (isset (self::$_classPaths[$lowerClassName])) {
			Copix::RequireOnce (self::$_classPaths[$lowerClassName]);
			return true;
		}

		// Rien trouvé
		return false;
	}

	/**
	 * Tente de charger une classe Copix par son nom
	 *
	 * @param string $pClassname Le nom de la classe à charger.
	 * @return boolean True si la classe est connue de cet autoloader. 
	 */
	public static function isKnown ($pClassname) {
                if (self::$_classPaths === false){
                    include (COPIX_CLASSPATHS_FILE);
                    self::$_classPaths = $classes;
                }
                return isset (self::$_classPaths[strtolower ($pClassname)]);
	}	
	/**
	 * Force une reconstruction du fichier COPIX_CLASSPATHS_FILE
	 * 
	 * @see CopixClassPathBuilder
	 */
	public static function rebuildClassPath () {
		Copix::RequireOnce (COPIX_PATH . 'coding/CopixClassPathBuilder.class.php');
		$builder = new CopixClassPathBuilder ();
		$builder->build ();
	}
	
}

/*
 * Met en place CopixAutoloader 
 * On référence la méthode statique. Ainsi le singleton ne sera pas créé avant la première tentative de chargement
 */
spl_autoload_register (array ('CopixAutoloader', 'autoload'));