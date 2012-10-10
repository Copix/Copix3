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
 * @ignore
 */
require_once (COPIX_UTILS_PATH.'CopixPager.class.php');
/**
* @package cms
* @subpackage newsletter
* Administration pannel
*/
class ZoneNewsletterAdminHeading extends CopixZone {
	function _createContent (&$toReturn) {
		//Getting the user.
		$userPlugin = CopixController::instance ()->getPlugin ('auth|auth');
		$user = $userPlugin->getUser ();

		//Create Services, and DAO
		$tpl = & new CopixTpl ();
		$workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
		$daoPages         = & CopixDAOFactory::getInstanceOf ('cms|CMSPage');

		// assign id of the current CopixHeadings
		$tpl->assign ('id_head', $this->_params['id_head']);

		//capability
		$newsletterModerateEnabled = CopixUserProfile::valueOf ('modules|cms|newsletter', 'newsletter') >= PROFILE_CCV_MODERATE;
		$newsletterSendEnabled     = CopixUserProfile::valueOf ('modules|cms|newsletter', 'newsletter') >= PROFILE_CCV_PUBLISH;

		$tpl->assign ('newsletterModerateEnabled', $newsletterModerateEnabled);
		$tpl->assign ('newsletterSendEnabled'    , $newsletterSendEnabled);

		switch ($this->getParam ('kind', 0)){
			case 1:
			$kind      = "groups";

			$daoGroup  = & CopixDAOFactory::getInstanceOf ('newslettergroups');
			$tpl->assign ('arGroups' , $daoGroup->findAll ());

			$toEdit = $this->_getSessionGroup ();
			$dao    = CopixDAOFactory::getInstanceOf ('newslettergroups');
			if ($this->getParam('e')){
				$tpl->assign ('errors' ,$dao->check ($toEdit));
			}
			$tpl->assign ('showErrors'       , $this->getParam ('e'));
			$tpl->assign ('toEdit'           , $toEdit);
			break;

			case 2:
			$kind = "users";

			$dao = & CopixDAOFactory::getInstanceOf ('newslettermaillinkgroups');
			/*
			Correction PGU pour faire un tri cohérent
			$sp  = CopixDAOFactory::createSearchParams ();
			$sp->orderBy('id_nlg');
			*
			* $arNewsletter = $dao->findBy ($sp);
			*/
			$arNewsletter = $dao->findAllOrderByGroupMail ();

			if (count($arNewsletter)>0) {
				$params = Array(
				'perPage'    => intval(CopixConfig::get ('newsletter|perPage')),
				'delta'      => intval(CopixConfig::get ('newsletter|delta')),
				'recordSet'  => $arNewsletter,
				'template'   => CopixConfig::get ('newsletter|multipageTemplate')
				);
				$Pager = CopixPager::Load($params);
				$tpl->assign ('multipage' , $Pager->GetMultipage());
				$tpl->assign ('arMails'   , $Pager->data);
			}
			break;

			case 3:
			$kind      = "history";

			$dao           = & CopixDAOFactory::getInstanceOf ('newslettersend');
			$daoGroup      = & CopixDAOFactory::getInstanceOf ('newslettergroups');
			$daoCopixGroup = & CopixDAOFactory::getInstanceOf ('copix:CopixGroup');
			$sp            = & CopixDAOFactory::createSearchParams ();
			$sp->orderBy (array ('date_nls', 'desc'));
			$arAlreadySend = $dao->findBy($sp);
			foreach ((array)$arAlreadySend as $key=>$newsletter){
				if (strlen(trim($newsletter->id_nlg)) > 0) {
					foreach (explode(';', $newsletter->id_nlg) as $id_nlg){
						$group = $daoGroup->get($id_nlg);
						$arAlreadySend[$key]->groups[] = $group->name_nlg;
					}
				}
				if (strlen(trim($newsletter->id_cgrp)) > 0) {
					foreach (explode(';', $newsletter->id_cgrp) as $id_cgrp){
						$group = $daoCopixGroup->get($id_cgrp);
						$arAlreadySend[$key]->groups[] = $group->name_cgrp;
					}
				}
			}
			$tpl->assign ('arAlreadySend', $arAlreadySend);
			break;

			case 0:
			default:
			$kind = "general";
			$tpl->assign ('arCMSPagePublish',    $daoPages->findByStatusIn ($workflow->getPublish(),$this->_params['id_head']));
			break;
		}
		$tpl->assign ('kind', $kind);
		$toReturn = $tpl->fetch ('newsletter.adminheading.tpl');
		return true;
	}

	/**
   * gets the current edited group.

   */
	function _getSessionGroup (){
		CopixDAOFactory::fileInclude ('newslettergroups');
		return isset ($_SESSION['MODULE_NEWSLETTER_EDITED_GROUP']) ? unserialize ($_SESSION['MODULE_NEWSLETTER_EDITED_GROUP']) : null;
	}
}
?>