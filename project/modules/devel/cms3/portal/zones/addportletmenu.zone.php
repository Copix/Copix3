<?php

/**
 * @package     cms
 * @subpackage  portal
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author   Sylvain Vuidart
 */

/**
 * Menu affiché à gauche pour l'ajout de portlet.
 */
class ZoneAddPortletMenu extends CopixZone {
	
	public function _createContent (&$toReturn){	

		$pVariableName = $this->getParam('pVariableName', '');

		$listContent = '';
		$groups = array();
		foreach (_class ('PortletServices')->getList () as $portletId=>$portletInformations){
			if (array_key_exists('group', $portletInformations)){
				if (!array_key_exists($portletInformations['group'], $groups)){
					$groups[$portletInformations['group']] = array();
				}
				$groups[$portletInformations['group']][$portletId] = $portletInformations;
			}
			else {
				if (!array_key_exists('special', $groups)){
					$groups['special'] = array();
				}
				$groups['special'][$portletId] = $portletInformations;
			}

		}

		$arPortletsInfos = array();
		foreach (_class ('PortletServices')->getList () as $informations){
			$arPortletsInfos[$informations['portlettype']] = $informations;
		}

		$tpl = new CopixTpl();
		$tpl->assign ('arPortletsInfos', $arPortletsInfos);
		$tpl->assign ('portletsInformations', _class('portletservices')->getList());
		$tpl->assign ('portletClipBoard', CopixSession::get('portletClipBoard', 'cms3'));
		$tpl->assign ('variableName', $pVariableName);
		$tpl->assign ('groups', $groups);
		$tpl->assign ('editId', $this->_params['editId']);
		$toReturn = $tpl->fetch ('addportletmenu.php');
	}
	
}