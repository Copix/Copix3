<?php
/**
* @package	copix
* @subpackage document
* @author	Croes Gérald, see copix.org for other contributors.
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ZoneEditTemplate extends CopixZone {
	function _createContent (&$toReturn) {
		$tpl = & new CopixTpl ();
		$tpl->assign ('showErrors',$this->getParam ('showErrors', false));
        $daoTpl = CopixDAOFactory::getInstanceOf ('copixtemplate');
        if ($this->getParam ('showErrors', false)){
           $tpl->assign ('errors' ,$daoTpl->check ($this->_params['edited']));
        }else{
        	$tpl->assign ('errors' ,array ());
        }
		$tpl->assign ('edited', $this->getParam ('edited'));
		$tpl->assign ('editId', $this->getParam ('editId'));

		//Liste des themes
		$daoTheme = CopixDAOFactory::getInstanceOf ('copixtemplate_theme');
		$tpl->assign ('arTheme', $daoTheme->findAll ());
		$tpl->assign ('selectedTab', $this->getParam ('selectedTab', 0));
		$tpl->assign ('arModules', $this->_getModules ());
		$tpl->assign ('selectedModule', null);

		// recherche du template source
		$edited = $this->getParam ('edited');
		if($edited->id_ctpl != $edited->publicid_ctpl ){
			$criteres = CopixDAOFactory::createSearchParams();
			$criteres->addCondition('id_ctpl', '=', $edited->publicid_ctpl);	
			foreach($daoTpl->findBy ($criteres) as $template){
				$tpl->assign ('sourceTemplate', $template->caption_ctpl);
			}
		}
		$toReturn = $tpl->fetch ('template.edit.tpl');
		return true;
	}

	/**
	* Gets the module list
	*/
	function _getModules (){
		$modules = CopixModule::getList ();
		$toReturn = array ();
		foreach ($modules as $moduleName){
             $toReturn[$moduleName] = CopixModule::getInformations($moduleName);
             $toReturn[$moduleName]->name.='|';
		}
		return $toReturn;
	}
}
?>