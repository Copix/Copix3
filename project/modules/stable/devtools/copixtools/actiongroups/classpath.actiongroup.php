<?php
/**
 * @package standard
 * @subpackage admin
 * @author Gérald Croës, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Reconstruction du CopixClassPaths.inc.php
 *
 * @package standard
 * @subpackage admin
 */
class ActionGroupClassPath extends CopixActionGroup {
	/**
	 * Vérifie que l'on est bien administrateur
	 *
	 * @param string $pActionName Nom de l'action
	 */
	public function beforeAction ($pActionName) {
		CopixPage::add ()->setIsAdmin (true);
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
	
	/**
	 * Pour changer la chaine d'un répertoire et utiliser la constante COPIX_PATH
	 *
	 * @param string $pItem Elément
	 * @param string $pKey Clef
	 */
	private function _walkPaths (&$pItem, $pKey) {
		$pItem = substr (CopixFile::getRealPath ($pItem), strlen (CopixFile::getRealPath (COPIX_PATH)));
	}
	
	/**
	 * Réécriture du chemin des classes
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		_notify ('breadcrumb', array ('path' => array ('#' => _i18n ('classpath.breadcrumb.rebuild', CopixFile::extractFileName (COPIX_CLASSPATHS_FILE)))));

		require (COPIX_CLASSPATHS_FILE);
		$paths = $classes;
		
		array_walk ($paths, array ($this, '_walkPaths'));
		CopixAutoloader::rebuildClassPath ();

		require (COPIX_CLASSPATHS_FILE);
		$newPaths = $classes;

		array_walk ($newPaths, array ($this, '_walkPaths'));
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('classpath.result.title.rebuild', CopixFile::extractFileName (COPIX_CLASSPATHS_FILE));
		
		$ppo->dirs = array ();
		foreach ($newPaths as $path) {
			$dir = CopixFile::extractFilePath ($path);
			$ppo->dirs[$dir] = (isset ($ppo->dirs[$dir])) ? $ppo->dirs[$dir] + 1 : 1;
		}
		ksort ($ppo->dirs);
		
		$ppo->deleted = array ();
		$ppo->edited = array ();
		foreach ($paths as $name => $path) {
			// si cette classe a été supprimée
			if (!array_key_exists ($name, $newPaths)) {
				$ppo->deleted[$name] = $path;
			// si la classe existe toujours
			} else {
				// si le chemin a été modifié
				if ($path != $newPaths[$name]) {
					$ppo->edited[$name] = $newPaths[$name];
				}
				// on supprime la classe de $newPaths pour ne garder que les nouvelles classes à la fin de la boucle
				unset ($newPaths[$name]);
			}
		}
		// ici, $newPaths ne contient que les nouveau chemins entre l'ancienne génération et la nouvelle
		$ppo->added = $newPaths;
		
		return _arPPO ($ppo, 'classpath.result.tpl');
	}
}