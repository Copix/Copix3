<?php
/**
 * @package     tools
 * @subpackage  antispam
 * @author      Duboeuf Damien, Brice Favre
 * @copyright   CopixTeam
 * @link        2002-2008, http://copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Zone d'affichage des éléments antispam (à intégrer dans un formulaire)
 * @package tools
 * @subpackage antispam
 */
class ZoneAntiSpam extends CopixZone {
	
	function _createContent(&$toReturn) {
		$tpl = new CopixTpl ();
		
		$tpl->assign ('namespace_id',  uniqid ());
		$toReturn = $tpl->fetch('antispam|captcha.form.php');
		
		return true;
	}
}
?>