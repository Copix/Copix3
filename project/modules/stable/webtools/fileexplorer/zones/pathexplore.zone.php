<?php
/**
 * @package webtools
 * @subpackage fileexplorer
 * @author Croës Gérald, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Zone affichant un chemin cliquable pour l'exploration
 * 
 * @package webtools
 * @subpackage fileexplorer
 */
class ZonePathExplore extends CopixZone {
	/**
	 * Modifie $toReturn pour y indiquer le contenu de la zone
	 *
	 * @param string $toReturn Contenu HTML de la zone
	 * @return boolean
	 */
	protected function _createContent (& $toReturn) {
		$path = str_replace ('\\', '/', $this->getParam ('path'));
		$pathParts = array_filter (explode ('/', $path), array ($this, '_filterTrim'));
		$toReturn = '';
		$currentPath = (CopixConfig::instance ()->osIsWindows ()) ? '' : '/';
		$separator = '/';

		$toReturn .= '<a href="' . _url ('default', array ('path'=>'/')) . '">' . _i18n ('fileexplorer.rootDirectory') . '</a>';
		$index = 0;
		$lastDir = null;
		foreach ($pathParts as $part) {
			if ($index >= count ($pathParts) - 1) {
				$lastDir = $part;
				break;
			}
			$index++;
			$currentPath .= $part . '/';
			$toReturn .= $separator;
			$toReturn .= '<a href="'._url ('default', array ('path'=>$currentPath)).'">';
			$toReturn .= $part;
			$toReturn .= '</a>';
		}
		if ($lastDir !== null) {
			$toReturn .= $separator . $lastDir;
		}
		return true;
	}
	
	/**
	 * Supprime les entrées du tableau qui ont une valeur vide, ou null
	 *
	 * @param string $pValue Valeur
	 * @return boolean
	 */
	private function _filterTrim ($pValue) {
		return ($pValue != '');
	}
}