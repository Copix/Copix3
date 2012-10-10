<?php
/**
* @package		cms
* @subpackage	cms_portlet_wysiwyg
* @author		Croes Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* show the list of the known pages.
* @package cms
* @subpackage cms_portlet_wysiwyg
*/
class ZoneEditWYSIWYGContent extends CopixZone {
	/**
    * Attends un objet de type textpage en paramètre.
    */
	function _createContent (&$toReturn){
		$tpl = & new CopixTpl ();
		$tpl->assign ('edited', $this->_params['toEdit']->getCopy ());
		switch ($this->_params['kind']){
			case 0:
			$kind = "general";
			break;

			case 1:
			$kind = "preview";
			break;

			default:
			$kind = "general";
			break;
		}

		$tpl->assign ('possibleKinds', CopixTpl::find ('cms_portlet_wysiwygcontent', '.portlet.?tpl'));
		$tpl->assign ('show', $this->_params['toEdit']->getParsed ("content"));
		$tpl->assign ('kind', $kind);

		//appel du template.
		$toReturn = $tpl->fetch ('cms_portlet_wysiwygcontent|wysiwyg.edit.tpl');
		return true;
	}
}
?>