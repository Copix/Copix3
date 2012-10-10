<?php
/**
* @package	cms
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|PortletFactory');
//On regarde si on autorise la surcharge de la méthode qui prend en charge la désérialisation.
if (! CopixConfig::instance ()->overrideUnserializeCallbackEnabled){
   PortletFactory::includesAll ();		
}

/**
* @package cms 
* Portlet tools
*/
class CMSPortletTools {
    /**
    * Place $pPortlet en tant qu'élément en cours de modification
    * @param Portlet $pPortlet la portlet à placer en session.
    */
    function setSessionPortlet ($pPortlet){
        $_SESSION['MODULE_CMS_PORTLET'] = serialize ($pPortlet);
    }

    /**
    * Récupère la portlet en cours de modification dans la session.
    * @return Portlet ou null si non trouvé / valide
    */
    function getSessionPortlet (){
        if (isset ($_SESSION['MODULE_CMS_PORTLET'])){
            if (($toReturn = unserialize ($_SESSION['MODULE_CMS_PORTLET'])) !== false){
            	return $toReturn;
            }
        }
        return null;
    }

    /**
    * Place la portlet demandée en session, dans ce qui fait office de pseudo clipboard
    * @param Portlet $pPortlet La portlet à placer dans le clipboard
    */
    function setClipboardPortlet ($pPortlet){
        $_SESSION['MODULE_CMS_PORTLET_CLIPBOARD'] = serialize ($pPortlet);
    }

    /**
    * Récupère la portlet stockée en session dans la variable qui fait office de clipboard
    * @return Portlet ou null si non trouvé / valide
    */
    function getClipboardPortlet (){
        if (isset ($_SESSION['MODULE_CMS_PORTLET_CLIPBOARD'])){
            if (($toReturn = unserialize ($_SESSION['MODULE_CMS_PORTLET_CLIPBOARD'])) !== false){
            	return $toReturn;
            }
        }
        return null;
    }

    /**
    * Sets where to portlet should be
    */
    function setWishedPosition ($position) {
        $_SESSION['MODULE_CMS_PORTLET_POSITION'] = $position;
    }

    /**
    * Gets where the portlet should be (previous call os setWishedPosition)
    * @return int
    */
    function getWishedPosition () {
        return isset ($_SESSION['MODULE_CMS_PORTLET_POSITION']) ? $_SESSION['MODULE_CMS_PORTLET_POSITION'] : null; 
    }

    /**
    * récupère la page en cours de modification
    */
    function getPage () {
    	CopixClassesFactory::fileInclude ('cms|CMSPortletPage');
        return unserialize ($_SESSION['MODULE_CMS_PAGE']);
    }
}
?>