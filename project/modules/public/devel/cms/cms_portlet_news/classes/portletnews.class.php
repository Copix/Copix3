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
 * @ignore
 */

CopixClassesFactory::fileInclude ('cms|Portlet');

/**
 * @package	cms
 * @subpackage cms_portlet_news
 * PortletNews
 */
class PortletNews extends Portlet {
    /**
    * Le sujet du groupe de nouvelles
    */
    var $subject   = null;

    /**
    * Le nombre de nouvelles a afficher
    */
    var $numToShow = null;

    /**
    * Le nombre de nouvelles à récupérer pour le choix d'affichage
    */
    var $fromCountLastNews = null;
    
    /**
    * La rubrique dans laquelle on va chercher les nouvelles
    */
    var $id_head   = null;

    /**
    * L'url ou seront accessibles le détail des nouvelles
    */
    var $urldetail = null;

    /**
    * Constructeur
    * @param string $id l'identifiant de la portlet
    */
    function PortletNews ($id) {
        parent::Portlet ($id);
        $this->numToShow         = 5;
        $this->fromCountLastNews = 5;
    }

    /**
    * Récupère la portlet au format HTML
    * @return string
    */
    function getParsed ($context) {
        $tpl = new CopixTpl ();
        $tpl->assign ('arNews', $this->_getNewsToShow ());
        $tpl->assign ('commentEnabled', CopixConfig::get('cms_portlet_news|commentEnabled') == 1);
        $tpl->assign ('back'          , CopixUrl::getCurrentUrl());
        $tpl->assign ('url',            $this->urldetail);
        $tpl->assign ('subject',        $this->subject );

        return $tpl->fetch ($this->templateId ? $this->getTemplateSelector() : 'cms_portlet_news|normal.news.tpl');
    }

    function getGroup (){
        return 'general';
    }
    function getI18NKey (){
        return 'cms_portlet_news|news.portletdescription';
    }
    function getGroupI18NKey (){
        return 'cms_portlet_news|news.group';
    }
    
    /**
    * Indique le nombre de nouvelles à récupérer pour affichage
    * Dans X parmis Y, cela corresponds à Y
    * @return int
    */
    function _getNumToSelect (){
       return $this->numToShow > $this->fromCountLastNews ? $this->numToShow : $this->fromCountLastNews;
    }
    
    /**
    * Récupération de la liste des nouvelles à afficher
    * @return tableau de nouvelles
    */
    function _getNewsToShow (){
        CopixContext::push ('news');
        $daoNews  = CopixDAOFactory::getInstanceOf ('News');
        $workflow = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $arNews   = $daoNews->getRestrictedListByCat ($this->id_head, $this->_getNumToSelect (), $workflow->getPublish (), false);
        CopixContext::pop ();

        $toShowNewsCount   = $this->numToShow;

        //On doit faire une sélection parmis les nouvelles existantes.
        //On va supprimer un élément au hazard tant que le nombre désiré n'est pas
        //atteind
        while ($toShowNewsCount < count ($arNews)){
           //supprime un élément au hazard
           array_splice ($arNews, rand (0, count ($arNews)-1), 1);
        }
        return $arNews;
    }
}

/**
* @package	cms
* @subpackage cms_portlet_news
* Pour des raisons de compatibilité, l'ancienne convention étant de nommer les portlet XXXPortlet et non PortletXXX
*/
class NewsPortlet extends PortletNews {}
?>
