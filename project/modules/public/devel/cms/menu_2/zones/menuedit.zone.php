<?php
/**
* @package	cms
* @subpackage menu_2
* @author	Sylvain DACLIN
* @copyright 2001-2005 CopixTeam
* @link		http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.htmlGNU Leser General Public Licence, see LICENCE file
*/

/**
 * @package	cms
 * @subpackage menu_2
 * ZoneMenuEdit
 */
class ZoneMenuEdit extends CopixZone {
	function _createContent (&$toReturn) {
        require_once (COPIX_CORE_PATH.'CopixModule.class.php');
        $cmsOk = in_array ('cms', CopixModule::getList ());
        $tpl = new CopixTpl ();
        $tpl->assign ('toEdit', $this->_params['toEdit']);
        if ($showErrors = $this->getParam ('e', false)){
            $dao = CopixDAOFactory::getInstanceOf ('Menu');
            $tpl->assign ('errors', $dao->check ($this->_params['toEdit']));
        }
        $tpl->assign ('showErrors', $showErrors);
        $tpl->assign ('cmsOk', $cmsOk);
        $tpl->assign ('id_head', $this->_params['id_head']);

        //Regarde si cela corresponds à une page cms
        if ($cmsOk && (intval($this->_params['toEdit']->id_cmsp) > 0)) {
            $tpl->assign ('cmsPageName', $this->_getPageName($this->_params['toEdit']->id_cmsp));
            $tpl->assign ('cmsId', $this->_params['toEdit']->id_cmsp);
        }else{
            $tpl->assign ('cmsPageName', null);
            $tpl->assign ('cmsId', null);
        }
        $arTpl=CopixTpl::find('menu_2','*.menu.*tpl');
        $tpl->assign('arTpl',$arTpl);
        
        $toReturn = $tpl->fetch ('menu.edit.tpl');
        return true;
	}

    function _getPageName ($id_cmsp) {
        CopixContext::push ('cms');
        CopixClassesFactory::fileInclude ('cms|ServicesCMSPage');
        $page = ServicesCMSPage::getOnline ($id_cmsp);
        CopixContext::pop ();
        return $page->title_cmsp;
	}
}
?>