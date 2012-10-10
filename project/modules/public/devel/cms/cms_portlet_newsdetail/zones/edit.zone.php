<?php
/**
* @package	 cms
* @subpackage cms_portlet_newsdetail
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package	 cms
 * @subpackage cms_portlet_newsdetail
 * ZoneEdit
 */
class ZoneEdit extends CopixZone {
	function _createContent (&$ToReturn){
		$tpl   = & new CopixTpl ();
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

		$tpl->assign ('toEdit', $this->_params['toEdit']);
		$tpl->assign ('show', $this->_params['toEdit']->getParsed ("content"));
		$tpl->assign ('kind', $kind);
		$tpl->assign ('pageName', $this->_getNamePage($this->_params['toEdit']->detail_urlback));
		$tpl->assign ('possibleKinds', CopixTpl::find ('cms_portlet_newsdetail', '.portlet.?tpl'));

		//appel du template.
		$ToReturn = $tpl->fetch ('cms_portlet_newsdetail|edit.tpl');

		return true;
	}


	function _getNamePage ($id) {
		$daoPage =  & CopixDAOFactory::createRecord ('cms|cmspage');
		$dao = & CopixDAOFactory::getInstanceOf ('cms|cmspage');
		$sp  = & CopixDAOFactory::createSearchParams();

		$sp->addCondition ('publicid_cmsp', '=', $id);
		$sp->orderBy (array('version_cmsp', 'DESC'));

		$data =  $dao->findBy( $sp ) ;
		if (count ($data)){
			return $data[0]->title_cmsp;
		}
		return '';
	}
}
?>
