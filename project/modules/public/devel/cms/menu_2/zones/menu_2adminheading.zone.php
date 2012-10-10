<?php
/**
 * @package	cms
 * @subpackage menu_2
 * @author	Sylvain DACLIN
 * @copyright 2001-2006 CopixTeam
 * @link		http://copix.org
 * @license  http://www.gnu.org/licenses/lgpl.htmlGNU Leser General Public Licence, see LICENCE file
 */
 
 /**
  * @ignore
  */
require_once (CopixModule::getPath('menu_2').'menu_2/'.COPIX_ACTIONGROUP_DIR.'admin.actiongroup.php');

/**
 * @package	cms
 * @subpackage menu_2
 * Zone that handles the menu administration.
 */
class ZoneMenu_2AdminHeading extends CopixZone {
    function _createContent (&$toReturn) {
    	
        $tpl = new CopixTpl ();
        $cmsOk = in_array ('cms', CopixModule::getList ());
      
        $headingServices        = CopixClassesFactory::getInstanceOf ('CopixHeadings|CopixHeadingsServices');
        //profile information appending.
        $idHead = $this->_params['id_head'];
/*      if ($idHead === null) {
            $idHead = 0;
        }
  */
        $id_menu = $this->getParam('id_menu');
 /*       
        if ($id_menu === null || $id_menu=='') {
            $id_menu = 1;
        }
	*/	
        $dao = CopixDAOFactory::getInstanceOf ('Menu');
        $arMenus = $dao->getMenu($id_menu, array('depth'=>1, 'isOnline'=>0, 'idHead'=>$idHead));

        $arInheritedMenus = null;
        //if ($id_menu == 1){         
            $arInheritedMenus = $dao->findByHeadingMenu($idHead);
            foreach ($arInheritedMenus as $key=>$cur){
                foreach ($arMenus as $overload){
                    if ($overload->var_name_menu == $cur->var_name_menu) {
                        unset($arInheritedMenus[$key]);
                    }
                }
            }
        //}            
        $curMenu = $dao->getWithProfile($id_menu);
        $pasteMenu=null;
        $tpl->assign ('pasteEnabled',false);
        if (isset($_SESSION['MODULE_MENU_CUTEDMENU'])) {
        	$pasteMenu = $_SESSION['MODULE_MENU_CUTEDMENU'];
        	$dao = CopixDAOFactory::getInstanceOf ('Menu');

			//does the cuted menu exists ?
			if (($cuted_menu = $dao->get ($_SESSION ['MODULE_MENU_CUTEDMENU'])) == false){
				return CopixActionGroup::process ('genericTools|Messages::getError',
				array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
				'back'=>CopixUrl::get ('menu_2|admin|')));
			}

            if (($cuted_menu->father_menu!=null && isset($curMenu->id_menu)) || ($cuted_menu->father_menu==null && !isset($curMenu->id_menu))) {
               $tpl->assign ('pasteEnabled',true);
	        }
        }
        $tpl->assign ('arMenus',            $arMenus);
        $tpl->assign ('arInheritedMenus',   $arInheritedMenus);
        $tpl->assign ('adminValue',         PROFILE_CCV_ADMIN);
        $tpl->assign ('currentMenu',        $curMenu);   
        $tpl->assign ('id_head',            ($idHead==0)?null:$idHead);   
        $tpl->assign ('pathMenu',           $dao->getPath($id_menu));
        $tpl->assign ('cmsOk', $cmsOk);

		$arTpl=CopixTpl::find('menu_2','*.menu.*tpl');
        $tpl->assign('arTpl',$arTpl);
        


        $toEdit = ActionGroupAdmin::getSessionMenu();
        if ($toEdit !== null) {
            $tpl->assign ('toEdit', $toEdit);
            //Regarde si cela corresponds à une page cms
            if ($cmsOk && (intval($toEdit->id_cmsp) > 0)) {
                $tpl->assign ('cmsPageName', $this->_getPageName($toEdit->id_cmsp));
                $tpl->assign ('cmsId', $toEdit->id_cmsp);
            }else{
                $tpl->assign ('cmsPageName', null);
                $tpl->assign ('cmsId', null);
            }            
            $toReturn = $tpl->fetch ('menu.edit.tpl');
        }else{
            $toReturn = $tpl->fetch ('menu.adminheading.tpl');
        }
        return true;
    }
    
    function _getPageName ($id_cmsp) {
        CopixContext::push ('cms');
        CopixClassesFactory::fileInclude ('cms|ServicesCMSpage');
        $page = ServicesCMSPage::getOnline ($id_cmsp);
        CopixContext::pop ();
        if ($page!=null) {
        	return $page->title_cmsp;
        }else{
        	return null;
        }
	}
}
?>