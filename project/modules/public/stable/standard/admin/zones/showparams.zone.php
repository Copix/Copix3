<?php
/**
 * @package standard
 * @subpackage admin 
* 
* @author   Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Display parameters of the given  module
 * @package standard
 * @subpackage admin 
* 
*/
class ZoneShowParams extends CopixZone {
	function _createContent (& $toReturn){
		$tpl = new CopixTpl ();
		if (($this->_params['choiceModule'] !== false) && CopixModule::isValid ($this->_params['choiceModule'])) {
 			$params = CopixConfig::getParams ($this->_params['choiceModule']);
		}else{
			$params = null;
		}
		$tpl->assign ('paramsList'  , $params);
		$tpl->assign ('moduleList'  , $this->_getModuleWithParams());
		$tpl->assign ('choiceModule', $this->_params['choiceModule']);
		$tpl->assign ('editParam'   , $this->_params['editParam']);
		
		$paramIndex = $this->_params['choiceModule'] . '|' . $this->_params['editParam'];
		      
		// si on a pass� une erreur en parametre
		if (!is_null (_request ('error'))) {
			switch (_request ('error')) {
				case 'typeTextMax' :
					$errorStr = _i18n ('params.error.typeTextMax', array ($params[$paramIndex]['MaxLength']));
					break;
				case 'typeIntMin' :
					$errorStr = _i18n ('params.error.typeIntMin', array ($params[$paramIndex]['MinValue']));
					break;
				case 'typeIntMax' :
					$errorStr = _i18n ('params.error.typeIntMax', array ($params[$paramIndex]['MaxValue']));
					break;
				case 'typeEmailMax' :
					$errorStr = _i18n ('params.error.typeEmailMax', array ($params[$paramIndex]['MaxLength']));
					break;
					
				default :
					$errorStr = _i18n ('params.error.' . _request ('error'));
					break;
			}
			$tpl->assign ('error', $errorStr);
		}

		$toReturn = $tpl->fetch ('parameters.tpl');
		return true;
   }

   /**
   * Gets module list, we only get modules with parameters
   */
   function _getModuleWithParams (){
       $toReturn = array ();
       foreach (CopixModule::getList() as $moduleName){
           if (count (CopixConfig::getParams ($moduleName)) > 0) {
               $informations = CopixModule::getInformations($moduleName);
               $toReturn[$moduleName] = $informations->description;
           }
       }
       asort ($toReturn);
       return $toReturn;
   }
}
?>