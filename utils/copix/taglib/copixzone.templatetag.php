<?php
/**
* @package		copix
* @subpackage	taglib
* @author		Gérald Croës
* @copyright	CopixTeam
* @link			http://www.copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Pour afficher une zone Copix 
* @package		copix
* @subpackage	taglib
*/
class TemplateTagCopixZone extends CopixTemplateTag  {
	public function process ($pParams, $pContent=null){
	 	$assign = '';
		if (!isset ($pParams['process'])){
			throw new CopixTemplateTagException ('[copixzone smarty tag] - missing required process parameter'); 
		}else{
			$id = $pParams['process'];
			unset ($pParams['process']);
		}

		//On regarde si l'on assigne la sortie à un élément
		if (isset($pParams['assign'])){
			$assign = $pParams['assign'];
			unset ($pParams['assign']);
		}
		
		$fileInfo = new CopixModuleFileSelector ($id);
		if (! CopixModule::isEnabled ($fileInfo->module)) {
			if (isset ($pParams['required']) && $pParams['required'] == false) {
				return "";
			}
	    }
		return CopixZone::process ($id, $pParams);
	}
}	
?>