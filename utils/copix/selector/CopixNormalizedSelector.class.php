<?php
class CopixNormalizedSelector {
	public function __construct ($pArray){
		_ppo ($pArray)->saveIn ($this);
		$this->pipedModule = $this->module ? $this->module.'|' : ''; 
	}
	
	public function asString (){
		return $this->container.':'.$this->pipedModule.$this->relativePath.$this->element;
	}
}