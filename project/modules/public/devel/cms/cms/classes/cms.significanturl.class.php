<?php
/**
* @package	cms
* @author	 Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms 
* Gestion des URL significatives
*/
class CMSSignificantUrl {
    /**
    * Décryptage d'une url
    *
    * @param path          array of path element
    * @param parameters    array of parameters (eq $this-vars)
    * @param mode          urlsignificantmode : prepend / none
    * @return array([module]=>, [desc]=>, [action]=>, [parametre]=>)
    */
    function parse ($path, $mode) {
        if ($mode=='none'){
            return false;
        }
        if (count($path) == 3 && $path[0] == 'cms' && (!in_array ($path[1], array ('admin', 'default', 'htme', 'html', 'portlet', 'wiki', 'workflow')))) {
            if (! isset($path[1])) {
                return false;
            }
            $id = $path[1];

            $toReturn = array();
            $toReturn['module']  = 'cms';
            $toReturn['desc']    = 'default';
            $toReturn['action']  = 'get';
            $toReturn['id']  = $id;
            return $toReturn;
        }else{
            return false;
        }
    }

    /**
    * get
    *
    * Handle url encryption
    *
    * @param dest          array([module]=>, [desc]=>, [action]=>)
    * @param parameters    array of parameters (eq $this-vars)
    * @param mode          urlsignificantmode : prepend / none
    * @return object([path]=>, [vars]=>)
    */
    function get ($dest, $parameters, $mode) {
        if ($mode == 'none'){
            return false;
        }
        $toReturn = new StdClass ();
        if ($dest['module'] == 'cms' && ($dest['desc'] == 'default' || $dest['desc'] == '') && ($dest['action'] == 'get')){
            if (!isset($parameters['id'])){
                return false;
            }
            CopixClassesFactory::fileInclude ('cms|ServicesCMSPage');
            if (($page = ServicesCMSPage::getOnline ($parameters['id']) )!== null){
                $toReturn->path = array ('cms', $page->publicid_cmsp, CopixUrl::escapeSpecialChars($page->title_cmsp));
                unset ($parameters['id']);
                $toReturn->vars = $parameters;
                $headingServices = CopixClassesFactory::getInstanceOf ('copixheadings|CopixHeadingsServices');
                $toReturn->basePath  = $headingServices->getDomainFor ($page->id_head);
                if (($toReturn->basePath !== null) && (($scriptName = substr ($toReturn->basePath, strrpos ($toReturn->basePath, '/'))) != '/')){
                	$toReturn->scriptName = $scriptName;
                	$toReturn->basePath   = substr ($toReturn->basePath, 0, strrpos ($toReturn->basePath, '/'));
                }
                return $toReturn;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
?>