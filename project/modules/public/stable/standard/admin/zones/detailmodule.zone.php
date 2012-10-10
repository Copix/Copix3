<?php
/**
 * @package standard
 * @subpackage admin
 * @author	Salleyron Julien
 * @copyright 2001-2008 CopixTeam
 * @link      http://copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Affiche les informations connues sur un module (dépendances, chemins, ....)
 * 
 * @package standard
 * @subpackage admin
 */
class ZoneDetailModule extends CopixZone {
	function _createContent (& $toReturn){
    	$moduleName = CopixZone::getParam('moduleName');
    	
    	$infos = CopixModule::getInformations ($moduleName);
        
        $tpl = new CopixTpl ();
    	
    	if (in_array($moduleName,CopixModule::getList())) {
            $arModule = CopixModule::getDependenciesForDelete($moduleName);
    	    $template = 'detailmoduledelete.tpl';
    	    $record = _dao('Copix:copixmodule')->get($moduleName);
    	    $tpl->assign ('version',$record->version_cpm);
    	} else {
            $arDependencies = CopixModule::getDependenciesForInstall($moduleName);
            $arModule = array();
            $arExtension = array();
            $install = true;
            foreach ($arDependencies as $key=>$dependency) {
                if ($dependency->kind === 'module') {
                    if (CopixModule::testDependency($dependency)) {
                        $dependency->exists = true;
                        $dependency->isInabled = CopixModule::isEnabled ($dependency->name);
                        $arModule[] = $dependency;
                    } else {
                        $dependency->exists = false;
                        $install = false;
                        $arModule[] = $dependency;
                    }
                } else {
                    if (CopixModule::testDependency($dependency)) {
                        $dependency->exists = true;
                        $arExtension[] = $dependency;
                    } else {
                        $dependency->exists = false;
                        $install = false;
                        $arExtension[] = $dependency;
                    }
                }
            }
            $tpl->assign('arExtension', $arExtension);
            $tpl->assign('install',$install);
            $template = 'detailmoduleinstall.tpl';
    	}
    	
    	$tpl->assign ('path', CopixModule::getPath ($moduleName));
        $tpl->assign('arModule', $arModule);
        $tpl->assign('info',$infos);
        $tpl->assign('moduleName',$moduleName);
        $toReturn = $tpl->fetch($template);
        return true;
	}
}
?>