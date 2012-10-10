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
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|Portlet');

/**
* @package cms
* @subpackage cms_portlet_newsdetail
* Detailed news portlet
*/
class PortletNewsDetail extends Portlet {
	/**
    * Url where we should go back to
    */
	var $detail_urlback = null;

	/**
    * gets the parsed content of the portlet
    */
	function getParsed ($context){
      $tpl  = new CopixTpl ();
      Copix::RequireClass ('CopixDateTime');
		$news = false;
		if (isset ($this->params['newsId'])) {
			$dao  = CopixDAOFactory::getInstanceOf ('news|News');
			$news = $dao->get ($this->params['newsId']);
		}
		if ($news !== false){
         $tpl->assign ('detail_titre',    $news->title_news);
			$tpl->assign ('detail_date',     CopixDateTime::yyyymmddToDate($news->datewished_news));
			$tpl->assign ('detail_content',  $news->content_news);
			$tpl->assign ('noNews', false);
		}else{
			$tpl->assign ('noNews', true);
		}

		$tpl->assign('detail_urlback',  $this->detail_urlback );
		return $tpl->fetch ($this->templateId ? $this->getTemplateSelector() : 'cms_portlet_newsdetail|detail.tpl');
	}

	/**
    * gets the group id
    */
	function getGroup (){
		return 'general';
	}
	/**
    * gets the caption of the portlet
    */
	function getI18NKey (){
		return 'cms_portlet_newsdetail|cms_portlet_newsdetail.portletdescription';
	}
	/**
    * gets the group caption 
    */
	function getGroupI18NKey (){
		return 'cms_portlet_newsdetail|cms_portlet_newsdetail.group';
	}
}

/**
* @package	 cms
* @subpackage cms_portlet_newsdetail
* Pour des raisons de compatibilité, l'ancienne convention étant de nommer les portlet XXXPortlet et non PortletXXX
*/
class NewsDetailPortlet extends PortletNewsDetail {}
?>