<?php
/**
* @package	cms
* @subpackage cms_portlet_news
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package cms
* @subpackage	cms_portlet_news
* show the list of the known pages.
*/
class ZoneEditNews extends CopixZone {
    /**
	* Attends un objet de type textpage en paramètre.
	*/
    function _createContent (&$toReturn){
        $tpl = new CopixTpl ();
        $tpl->assign ('objNews', $this->_params['toEdit']);

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

        $tpl->assign ('pageName', $this->_getNamePage($this->_params['toEdit']->urldetail));
        $dao = CopixDAOFactory::getInstanceOf ('copixheadings|copixheadings');

        if ($this->_params['toEdit']->id_head == null) {
            $heading = CopixI18N::get ('copixheadings|headings.message.root');
        }else{
            $heading = $dao->get ($this->_params['toEdit']->id_head);
            $heading = $heading->caption_head;
        }

        $tpl->assign ('headingName', $heading);
		$tpl->assign ('possibleKinds', CopixTpl::find ('cms_portlet_news', '.portlet.?tpl'));
        $tpl->assign ('show', $this->_params['toEdit']->getParsed ("content"));
        $tpl->assign ('kind', $kind);
        $tpl->assign ('select', urlencode(CopixUrl::get ('cms_portlet_news||edit')));
        $tpl->assign ('back',   urlencode(CopixUrl::get ('cms_portlet_news||edit')));

        //appel du template.
        $toReturn = $tpl->fetch ('cms_portlet_news|news.edit.tpl');
        return true;
    }

    /**
    * gets the page name
    */
    function _getNamePage ($id) {
        $dao = CopixDAOFactory::getInstanceOf ('cms|cmspage');
        $sp  = CopixDAOFactory::createSearchParams();

        $sp->addCondition ('publicid_cmsp', '=', $id);
        $sp->orderBy (array('version_cmsp', 'desc'));

        $data =  $dao->findBy( $sp ) ;
        if (count ($data)){
           return $data[0]->title_cmsp ;
        }
        return '';
    }
}
?>