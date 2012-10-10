<?php
/**
* @package	cms
* @author	Croës Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package cms
* Show a select page "dialog"
*/
class ZoneSelectPage extends CopixZone {
    function _createContent (&$toReturn) {
        //Inclusions & instanciations
        CopixClassesFactory::fileInclude ('cms|ServicesCMSPage');
    	CopixClassesFactory::fileInclude('cms|CMSAuth');
        $cmsPages  = new ServicesCMSPage ();
        $sHeadings = CopixClassesFactory::getInstanceOf ('copixheadings|CopixHeadingsServices');
        
        //récupération des informations
        $headings  = $sHeadings->getTree();
        if ($this->getParam ('draft') == 1){
        	$user = CMSAuth::getUser ();
        	$pages = $cmsPages->getList (1, $user->login);
        }else{
        	$pages     = $cmsPages->getList ();
        }

		//initialisation de $arPages
		$arPages=array();

        //pagination
        foreach ($pages as $page){
            $arPages[$page->id_head][] = $page;
        }

    	//Template
        $tpl = new CopixTpl ();
        $tpl->assign ('arPublished', $arPages);
        $tpl->assign ('arHeadings',  $headings);
        $tpl->assign ('select',      $this->_params['select']);
        $tpl->assign ('back',        $this->_params['back']);

        $toReturn = $tpl->fetch ('page.select.ptpl');
        return true;
    }
}
?>