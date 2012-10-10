<?php
class MooBox{
	
	//this two function can be redeclare in boxes
	public function getContent(){return false;}
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