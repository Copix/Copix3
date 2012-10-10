<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Guillaume Perréal
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe chargée de recontruire le fichier COPIX_CLASSPATHS_FILE
 * La méthode est la suivante :
 * - on charge tous les fichiers de classe trouvés dans les répertoires indiqués par $_basePaths,
 *   en stockant au passage le chemin relatif à l'une des constantes COPIX_*_PATH,
 * - on récupère la liste de toutes les classes et interfaces Copix,
 * - pour chacune, on utilise la réflection pour savoir dans quelle fichier elles sont déclarées,
 * - on génère le contenu du fichier COPIX_CLASSPATHS_FILE en utilisant les constantes
 *
 * @package		copix
 * @subpackage	utils
 */
class CopixClassPathBuilder {
	/**
	 * Les classes trouvées lors du processus de recherche
	 *
	 * @var array
	 */
	private $_classIncludes;

	/**
	 * Chemins de copix, pour remplacer par des constantes
	 *
	 * @var array
	 */
	private $_copixPaths = array ();

	/**
	 * Constructeur
	 */
	public function __construct () {
		$this->_copixPaths = array (
			CopixFile::getRealPath (COPIX_UTILS_PATH) => 'COPIX_UTILS_PATH',
			CopixFile::getRealPath (COPIX_PATH) => 'COPIX_PATH'
		);
	}
	
	/**
	 * Extraction des classes déclarées dans le fichier
	 *
	 * @param string $pFileName le fichier d'ou extraire les classes
	 * @return array [classes] = filename
	 */
	private function _extractClasses ($pFileName){
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
					$toReturn[$token[1]] = (string) $pFileName;
					$classHunt = false;
				}
			}
		}

		return $toReturn;
	}

	/**
	 * Ajout des classes trouvées dans le processus de recherche 
	 * @param array $pClasses les classes trouvées
	 */
	private function _pushClassIncludes ($pClasses) {
		foreach ($pClasses as $className => $fileName) {
			$filePath = '\'' . CopixFile::getRealPath ($fileName) . '\'';
			foreach ($this->_copixPaths as $path => $const) {
				if (substr ($filePath, 0, strlen ($path) + 1) == '\'' . $path) {
					$filePath = $const . ' . \'' . substr ($filePath, strlen ($path) + 1);
					break;
				}
			}
			$this->_classIncludes[strtolower ($className)] = str_replace ('\\', '/', $filePath);
		}
	}
	/**
	 * Reconstruit le fichier COPIX_CLASSPATHS_FILE
	 * Nécessite les droits d'écriture dans COPIX_CLASSPATHS_FILE, cette opération est réservée aux développeurs de Copix
	 */
	public function build () {
		// Vérifie qu'on ait ce qu'il faut
		if (!is_writable (COPIX_CLASSPATHS_FILE)) {
			throw new CopixException (_i18n ('copix:copixclasspath.error.fileNotWritable', COPIX_CLASSPATHS_FILE));
		}

		//Recherche toute les classes connues
		$files = new CopixExtensionFilterIteratorDecorator (new RecursiveIteratorIterator (new RecursiveDirectoryIterator (COPIX_PATH)));
		$files->setExtension ('.php');
		foreach ($files as $fileName){
			$classes = $this->_extractClasses ($fileName);
			$this->_pushClassIncludes ($classes);
		}
		
		// Genère le fichier classpath
		$generator = new CopixPHPGenerator ();
		$content = '$classes = array (';
		foreach ($this->_classIncludes as $class => $path) {
			$content .= $generator->getEndLine () . $generator->getTabs () . '\'' . $class . '\' => ' . $path . ',';
		}
		$content .= $generator->getEndLine () . ');';
		$content = $generator->getPHPTags ($content);

		// Ecrit le fichier
		CopixFile::write (COPIX_CLASSPATHS_FILE, $content);
	}
}