<?php
/**
 * MooBoxes services
 * 
 * @package MooCMS
 * @subpackage MooCMS
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */
class MooBox{
	
	/**
	 * Default getContent method which can be redeclared on mooboxes 
	 * 
	 * @param array for parameters
	 */
	public function getContent($pParams=array()){return false;}
	
	/**
	 * Default getEdit method which can be redeclared on mooboxes 
	 * 
	 */
	public function getEdit(){return false;}
	
	/**
	 * Get installed mooboxes
	 */
	function getBoxes(){
		$modules = CopixModule::getList(true);		
		
		$boxes = array();
		foreach($modules as $module){
			if(preg_match('/^moobox_(.*)/',$module,$matches)){
				$boxes[]=$matches[1];
			}
		}
		return $boxes;
	}
}
?>