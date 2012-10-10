<?php
/**
* @package	cms
* @author	Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
if (CopixConfig::instance ()->overrideUnserializeCallbackEnabled){
   ini_set('unserialize_callback_func','_copix_cms_callbackportlet');
   function _copix_cms_callbackportlet ($classname){
      $classname = str_replace ('portlet', '', strtolower ($classname));
      PortletFactory::includePortlet ($classname);
   }
}

/**
 * Factory de portlet
 * @package cms
 */
class PortletFactory {
    /**
    * Creates a portlet of a given kind
    * @param string $kind the portlet kind
    */
    public static function create ($kind){
        PortletFactory::includePortlet ($kind);
        $className = 'Portlet'.$kind;
        $toReturn = new $className (PortletFactory::_genId ());
        return $toReturn;
    }

    /**
    * Includes all portlets definition classes
    * @return void
    */
    public static function includesAll (){
        static $done = false;
        if (!$done){
            foreach (PortletFactory::getList () as $elem) {
                PortletFactory::includePortlet ($elem);
            }
        }
        $done = true;
    }

    /**
    * Gets the portlet list (without cms_portlet_)
    */
    public static function getList (){
        $sort = array ();
        foreach ($modules = CopixModule::getList () as $name){
            if (strpos ($name, 'cms_portlet_') === 0){
                $sort[] = substr ($name, 12);
            }
        }        
        return $sort;
    }
    
    /**
    * Include a given portlet kind
    * @param string $kind the portlet kind
    * @return void
    */
    public static function includePortlet ($kind){
        $kind = strtolower($kind);
        CopixClassesFactory::fileInclude ('cms_portlet_'.$kind.'|portlet'.$kind);
    }

    /**
    * Generates a portlet id
    * @return string
    */
    public static function _genId (){
        return UniqId ('p_');
    }
}
?>