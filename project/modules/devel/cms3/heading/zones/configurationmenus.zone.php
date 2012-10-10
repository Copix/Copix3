<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 */

/**
 * Informations sur un élément
 *
 * @package cms
 * @subpackage heading
 */
class ZoneConfigurationMenus extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn Code HTML à retourner
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$pRecord = $this->getParam("record");

		if ($pRecord != null && (is_array ($pRecord) || !HeadingElementCredentials::canModerate ($pRecord->public_id_hei))) {
			return null;
		}
		
		$heiservices = _ioClass ('heading|headingelementinformationservices');
		$menuServices = _ioClass ('heading|headingelementmenuservices');
		$module = false;
		
		if ($pRecord == null){
			$module = true;
			$listInformationsMenus = $menuServices->getListMenus(CopixTpl::getTheme());
			foreach ($listInformationsMenus as $menuInfos){
				$menu = $menuServices->getModuleMenu ($menuInfos['name']);
				if ($menu){
					$pRecord = $heiservices->get ($menu->public_id_hei);
					break;
				}
			}
			if ($pRecord == null){
				$pRecord = $heiservices->get (0);
			}
		}

		//récupération des informations de visibilité
		$visibility_inherited_from = false;
		$visibility = $heiservices->getVisibility ($pRecord->public_id_hei, $visibility_inherited_from);
		if ($visibility_inherited_from != null) {
			$visibility_inherited_from = $heiservices->get ($visibility_inherited_from)->caption_hei;
		}
			
		//on recupere le theme de l'element pour aller chercher les informations de menus du theme
		$fooParameterIn = null;
   		$theme = $heiservices->getTheme ($pRecord->public_id_hei, $fooParameterIn);
		
		//menus
		if (!$theme){
			$theme = CopixConfig::get ('default|publicTheme');
		}
		$listInformationsMenus = $menuServices->getListMenus($theme);
		$inherited_menu = array();
		foreach ($listInformationsMenus as $menu){
			$inherited_menu[$menu['name']] = $menuServices->getInheritedHeadingElementMenu ($pRecord->public_id_hei, $menu['name']);
		}
		
		$results = $menuServices->getMenu ($pRecord->public_id_hei);
		if (empty ($listInformationsMenus)) {
			$modules_hem = null;
		} else {
			$modules_hem = null;
			foreach ($listInformationsMenus as $index => $infos) {
				if (isset ($results[$listInformationsMenus[$index]['name']])) {
					$modules_hem = $results[$listInformationsMenus[$index]['name']]->modules_hem;
					break;
				}
			}
		}

		$tpl = new CopixTpl();
		$tpl->assign ('uniqId', $this->getParam("uniqid", uniqid()));
		$tpl->assign ('module', $module);
		$tpl->assign ('record', $pRecord);
		$tpl->assign ('uniqueElement', !is_array ($pRecord));
		$tpl->assign ('visibility_inherited_from', $visibility_inherited_from);
		$tpl->assign ('visibility', $visibility);
		$tpl->assign ('listInformationsMenus', $listInformationsMenus);
		$tpl->assign ('inherited_menu', $inherited_menu);
		$tpl->assign ('liste_menus', $results);
		$tpl->assign ('modules_hem', $modules_hem);
		$pToReturn = $tpl->fetch ($this->getParam("template", 'heading|informations/menus.php'));
	}
}