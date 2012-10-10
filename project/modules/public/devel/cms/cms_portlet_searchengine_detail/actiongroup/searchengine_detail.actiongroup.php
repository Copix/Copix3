<?php

/**
 * @package cms
 * @subpackage cms_portlet_searchengine_detail
 */

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|cmsportlettools');
PortletFactory::includePortlet ('searchengine_detail');


/**
 * @package cms
 * @subpackage cms_portlet_searchengine_detail
 * ActionGroupSearchEndinge_Detail
 */

class ActionGroupSearchEngine_Detail extends CopixActionGroup {

	function doEdit (){
      return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get('cms|admin|validPortlet'));
   }
}
?>
