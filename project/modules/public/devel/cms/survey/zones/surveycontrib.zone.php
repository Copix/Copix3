<?php
/**
* @package	cms
* @subpackage survey
* @author	Bertrand Yan
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package	cms
* @subpackage survey
* Home of document contrib area
* @param id_head // the current copixheading indice can be null if racine
*/
class ZoneSurveyContrib extends CopixZone {
	function _createContent (&$toReturn) {
		$tpl = & new CopixTpl ();
		$servicesHeading  = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');

		// assign id of the current CopixHeadings
		$tpl->assign ('id_head', $this->_params['id_head']);

		//get caption of the heading
		$dao = & CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
		if ($heading = $dao->get( $this->_params['id_head'] )) {
			$caption_head = $heading->caption_head;
		} else {
			$caption_head = CopixI18N::get('copixheadings|headings.message.root');
		}
		$tpl->assign ('caption_head', $caption_head);

		//can add document
		$tpl->assign ('contribEnabled',  CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'survey') >= PROFILE_CCV_WRITE);
		$tpl->assign ('moderateEnabled', CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'survey') >= PROFILE_CCV_MODERATE);

		$toReturn = $tpl->fetch ('survey.contrib.tpl');
		return true;
	}
}
?>