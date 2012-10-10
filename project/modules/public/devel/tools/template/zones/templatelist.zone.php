<?php
/**
* @package	copix
* @author	Croës Gérald, Chazot Virginie see copix.org for other contributors.
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ZoneTemplateList extends CopixZone {
   function _createContent (&$toReturn){
		$daoTemplate      = & CopixDAOFactory::getInstanceOf ('copixtemplate');
		$daoTemplateTheme = & CopixDAOFactory::getInstanceOf ('copixtemplate_theme');

		//getting the theme lists by caption
		$spTemplateTheme = CopixDAOFactory::createSearchParams ();
		$spTemplateTheme->orderBy ('caption_ctpt');
		$themeList = $daoTemplateTheme->findBy ($spTemplateTheme);
		
		//getting the selected theme, default is "default" 
		//(and is the first entry in the database)
		$selectedTheme = $this->getParam ('selectedTheme', null);

		//getting all the kinds of template in this theme
        $qualifierList = $daoTemplate->getModuleQualifierListForTheme ($selectedTheme);
		$qualifierList = $this->_getQualifiersName ($qualifierList);

		if (count ($qualifierList) > 0){
			list ($index, $firstQualifier) = each ($qualifierList);
		}else{
			$firstQualifier = null;
		}
		$selectedQualifier = $this->getParam ('selectedQualifier', $firstQualifier);

		//Looking for the templates.
		$spTemplateList = CopixDAOFactory::createSearchParams ();
		$spTemplateList->addCondition ('id_ctpt', '=', $selectedTheme);
		$spTemplateList->addCondition ('modulequalifier_ctpl', '=', $selectedQualifier);
		$templateList = $daoTemplate->findBy ($spTemplateList);

		$tpl = & new CopixTpl ();
		$tpl->assign ('selectedQualifier', $selectedQualifier);
		$tpl->assign ('selectedTheme'    , $selectedTheme);
		$tpl->assign ('qualifierList'    , $qualifierList);
		$tpl->assign ('themeList'        , $themeList);
		$tpl->assign ('templateList'     , $templateList);

		$toReturn = $tpl->fetch ('template|templatelist.show.tpl');
		return true;
	}
	
	/**
	* Module qualifiers are module names, we then ask CopixModule for the names of the qualifiers
	* @return array () module_id => moduleInformations
	*/
	function _getQualifiersName ($modules){
		$toReturn = array ();
		foreach ($modules as $moduleName){
			$moduleName = substr ($moduleName, 0, -1);
			if ($moduleName == ''){
    			$toReturn[$moduleName]->name = '|';
				$toReturn[$moduleName]->description = CopixI18N::get ('template.message.project');
			}else{
            $toReturn[$moduleName] = CopixModule::getInformations($moduleName);
				$toReturn[$moduleName]->name.='|';
			}
		}
		return $toReturn;
	}
}
?>
