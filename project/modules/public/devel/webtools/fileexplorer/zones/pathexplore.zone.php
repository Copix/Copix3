<?php
/**
 * @package		webtools
 * @subpackage	fileexplorer
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Zone qui afiche un chemin cliquable pour exploration
 * @package		webtools
 * @subpackage	fileexplorer
 */
class ZonePathExplore extends CopixZone {
	protected function _createContent (& $toReturn){
		$path = $this->getParam ('path');
		$pathParts = explode ('/', $path);
		$toReturn = '';
		
		$currentPath = '';

		$toReturn .= '<a href="'._url ('default', array ('path'=>'/')).'">'._i18n ('fileexplorer.rootDirectory').'/</a>'; 		
		for ($i=0; $i<(count ($pathParts)-1); $i++){
			$part = $pathParts[$i];
			$currentPath .= '/'.$part;
			$toReturn .= '<a href="'._url ('default', array ('path'=>$currentPath)).'">';
			$toReturn .= $part;
			$toReturn .= '/</a>';
		}
		$toReturn .= $pathParts[$i];
		return true;
	}
}
?>