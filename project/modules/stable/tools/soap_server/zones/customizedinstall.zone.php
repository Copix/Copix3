<?php
/**
 * @package		tools
 * @subpackage	soap_server
 * @author		Favre Brice
 * @copyright	2001-2008 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		tools
 * @subpackage	soap_server
 */
class ZoneCustomizedInstall extends CopixZone {
	/**
	 * Création du contenu de la page
	 */
	function _createContent (&$toReturn) {
		$tpl = new CopixTpl ();
		$tpl->assign ('arModulesPath', CopixConfig::instance ()->arModulesPath);
		$arModules = $this->_getModuleOrderByDescription ();
		$tpl->assign ('arModules', $arModules );
		$toReturn = $tpl->fetch ('classes.list.tpl');
	}
}