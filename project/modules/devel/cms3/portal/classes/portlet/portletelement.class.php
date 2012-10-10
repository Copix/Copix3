<?php

/**
 * Element de portlet
 *
 */
class PortletElement {
	
	/**
	 * Tableau d'options
	 */
	private $_arOptions = array();
	
	/**
	 * element de type HeadingElement
	 */
	private $_headingElement = null;
	
	public function setHeadingElement ($pHeadingElement){
		$this->_headingElement = $pHeadingElement;
	}
	
	public function getHeadingElement (){
		return $this->_headingElement;
	}
	
	public function setOptions (array $pOptions){
		$this->_arOptions = $pOptions;
	}
	
	public function getOptions (){
		return $this->_arOptions;
	}
	
	/**
	 * Retourne une option
	 *
	 * @param string $pKey
	 * @return string/null 
	 */
	public function getOption ($pKey){
		return (array_key_exists ($pKey, $this->_arOptions)) ? $this->_arOptions[$pKey] : null; 
	}
}
?>