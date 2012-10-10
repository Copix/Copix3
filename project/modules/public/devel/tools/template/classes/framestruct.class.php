<?php
/**
* Défini un objet de structure d'un template
*/
class CopixTemplateFrame {
	/**
	* Identifiant de la frame
	* @access private
	*/
	var	$_id = 0;
	
	/**
	* Nom de la frame
	* @access private
	*/
	var $_name;
	
	/**
	* Propriétés de la frame
	*/
	var	$properties = array();
	
	/**
	* Liste des frame verticales contenues dans l'objet
	*/
	var	$vframe = array();
	
	/**
	* Liste des frame horizontales contenus dans l'objet
	*/
	var	$hframe = array();

	/**
	* Constructeur
	*/
	function frameStruct($name, $id){
		$this->setName($name);
		$this->setId($id);
	}

	/**
	* Définition de l'identifiant
	* @access private
	*/
	function setId($id){
		$this->_id = $id;
	}

	/**
	*
	*/
	function setName($name){
		$this->_name = $name;
	}

	function getName(){
		return $this->name;
	}

	function addVFrame($name='', $id = -1){
		if(count($this->hframe) == 0)
		$this->vframe[] = new FrameStruct($name, $id);
	}

	function addHFrame($name='', $id = -1){
		if(count($this->vframe) == 0)
		$this->hframe[] = new FrameStruct($name, $id);
	}

	function getVframe(){
		return $this->vframe;
	}

	function getHframe(){
		return $this->hframe;
	}

	function & findCellById($id){
		if(strcmp($this->id,$id) ==0 ) {
			return $this;
		}
		for($i = 0 ; $i < count($this->vframe) ; $i++){
			$subElem = & $this->vframe[$i];
			$item = & $subElem->findCellById($id);
			if($item != null){
				return $item;
			}
		}
		for($i = 0 ; $i < count($this->hframe) ; $i++){
			$subElem = & $this->hframe[$i];
			$item = & $subElem->findCellById($id);
			if($item != null){
				return $item;
			}
		}

		return null;
	}

	function delCellById($id){
		if(strcmp($this->id,$id) ==0 ) {
			return true;
		}
		for($i = 0 ; $i < count($this->vframe) ; $i++){
			if($this->vframe[$i]->delCellById($id)){
				unset($this->vframe[$i]);
				$this->vframe = array_values($this->vframe);
				return false;
			}
		}
		for($i = 0 ; $i < count($this->hframe) ; $i++){
			if($this->hframe[$i]->delCellById($id)){
				unset($this->hframe[$i]);
				$this->hframe = array_values($this->hframe);
				return false;
			}
		}
		return null;
	}
}
?>