<?php
/**
* @package	 cms
* @subpackage cms_portlet_picture
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package cms
 * @subpackage cms_portlet_picture
 */
class ZoneEditPicture extends CopixZone {
	/**
	* Attends un objet de type textpage en paramètre.
	*/
	function _createContent (&$ToReturn){
		$tpl = & new CopixTpl ();

		//essaye de récupérer l'image
		$daoPicture = CopixDAOFactory::getInstanceOf ('pictures|pictures');
		$picture    = $daoPicture->get ($this->_params['toEdit']->id_pict);		
		if ($picture) $this->_params['toEdit']->url_pict = $picture->url_pict;
		$tpl->assign ('toEdit', $this->_params['toEdit']);

		//appel du template.
		$ToReturn = $tpl->fetch ('cms_portlet_picture|picture.edit.tpl');
		return true;
	}

}
?>