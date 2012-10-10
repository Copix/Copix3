<?php
/**
* @package		tools 
 * @subpackage	wsserver
* @author		Favre Brice
* @copyright	2001-2007 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package		tools 
 * @subpackage	wsserver
 */
class ZoneCustomizedInstall extends CopixZone {
    
	/**
     * Création du contenu de la page
     * 
     */
    function _createContent (&$toReturn) {
      $tpl = new CopixTpl ();
      $tpl->assign ('arModulesPath', CopixConfig::instance ()->arModulesPath);
      $arModules = $this->_getModuleOrderByDescription ();
      $tpl->assign ('arModules', $arModules );
      $toReturn = $tpl->fetch ('classes.list.tpl');
    }
    
    /**
    * Récupération des modules dans l'ordre de leur description
    * 
    */
    function _getModuleOrderByDescription (){
    	$arReturn = array();
    	foreach (CopixModule::getList(true) as $module) {
    		$temp = CopixModule::getInformations ($module);
    		$modulePath  = CopixModule::getPath ($module);		
    		
    		if (is_dir ( $modulePath . COPIX_CLASSES_DIR )) {
    			$servicesDir = opendir ( $modulePath . COPIX_CLASSES_DIR );
    			
    			while ($file = readdir ($servicesDir)) {
    					
    				if (preg_match ("/class.php$/", $file)) {
    					
	    				$temp->services[] = $file;
    				}
    			}
    		}
    		if (isset($temp->services )){
    			$arReturn[] = $temp;
    		}
    		
    	}
    	return $arReturn;
 
    }
    
}
?>