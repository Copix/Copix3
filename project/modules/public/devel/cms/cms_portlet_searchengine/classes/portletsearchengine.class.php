<?php

/**
 * @package cms
 * @subpackage cms_portlet_searchengine
 */
 
 /**
  * @ignore
  */
CopixClassesFactory::fileInclude ('cms|Portlet');
/**
 * @package cms
 * @subpackage cms_portlet_searchengine
 * PortletSearchEngine
 */
 class PortletSearchEngine extends Portlet {
    /**
    * The text that will be displayed before the search zone.
    * @var string
    */
    var $presentation_text;

    /**
    * The title of the portlet
    * @var string
    */
    var $title;

    /**
    * Default size of the search zone
    * @var int
    */
    var $size;

    /**
    * Constructor
    */
    function PortletSearchEngine ($id) {
        parent::Portlet ($id);
        $this->presentation_text = null;
        $this->title = null;
        $this->size = 14;
        $this->template = 'normal';
        $this->idPortletResultPage=null;
    }

    /**
    * gets the parsed searchengine zone
    * @return string
    */
    function getParsed ($context) {
        $tpl = & new CopixTpl ();
        $tpl->assign ('toShow',   $this);
        $tpl->assign ('keywords', isset ($this->params['criteria']) ? $this->params['criteria'] : null);
        $tpl->assign ('noForm',  $context == CMSParseContext::edit);
        $tpl->assign ('title', $this->title);
        $tpl->assign ('size', $this->size);
        $tpl->assign ('pageDest', $this->idPortletResultPage);

         return $tpl->fetch ($this->templateId ? $this->getTemplateSelector() : 'cms_portlet_searchengine|normal.searchengine.tpl');
    }

    /**
    * Gets the group of the portlet
    */
    function getGroup (){
        return 'general';
    }
    /**
    * Gets the i18n key for the portlet
    */
    function getGroupI18NKey (){
        return 'cms_portlet_searchengine|searchengine.group';
    }
    /**
    * Gets the i18n key for the group name
    */
    function getI18NKey (){
        return 'cms_portlet_searchengine|searchengine.description';
    }
}
/**
 * @package cms
 * @subpackage cms_portlet_searchengine
* Pour des raisons de compatibilité, l'ancienne convention étant de nommer les portlet XXXPortlet et non PortletXXX
*/
class SearchEnginePortlet extends PortletSearchEngine {}
?>