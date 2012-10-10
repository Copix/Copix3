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
 * Affichage des propriétés d'un fichier
 * @package		webtools
 * @subpackage	fileexplorer
 */
class ZoneFileProperties extends CopixZone {
	function _createContent (& $toReturn){
		_classInclude ('filesiterator');
		$file = new FileDescription ($this->getParam ('file'));
		
		$tpl = new CopixTpl ();
		$tpl->assign ('permissions', $file->getPermissions ());
		$tpl->assign ('owner', $file->getOwner ());
		$tpl->assign ('group', $file->getGroup ());
		$tpl->assign ('lastupdate', $file->getLastUpdateDate ());
		$tpl->assign ('lastaccess', $file->getLastAccessDate ());
		
		$toReturn = $tpl->fetch ('file.properties.tpl');
	}
}
?>