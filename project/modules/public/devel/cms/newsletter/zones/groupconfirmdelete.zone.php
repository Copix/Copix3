<?php
/**
* @package	cms
* @subpackage newsletter
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package	cms
 * @subpackage newsletter
 * ZoneGroupConfirmDelete
 */
class ZoneGroupConfirmDelete extends CopixZone {
	function _createContent (& $toReturn){
		$tpl = & new CopixTpl ();

		$daoGroup = & CopixDAOFactory::getInstanceOf ('NewsletterGroups');

		$tpl->assign ('group'  , $this->_getGroup ($this->_params['id_nlg']));
		$tpl->assign ('id_head', $this->_params['id_head']);
		$tpl->assign ('groups' , $daoGroup->findAll ());

		$toReturn = $tpl->fetch ('groupdeleteconfirm.tpl');
		return true;
	}

	function _getGroup ($id){
		$dao     = & CopixDAOFactory::getInstanceOf ('NewsletterGroups');
		$record  = $dao->get ($id);

		$daoLink = & CopixDAOFactory::getInstanceOf ('NewsletterMailLinkGroups');
		$sp      = & CopixDAOFactory::createSearchParams ();
		$sp->addCondition('id_nlg','=',$id);
		$record->mail_count = count($daoLink->findBy($sp));
		return $record;
	}
}
?>