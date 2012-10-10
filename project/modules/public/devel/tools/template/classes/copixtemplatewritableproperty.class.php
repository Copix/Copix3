<?php
class CopixTemplateWritableProperty extends CopixTemplateSimpleProperty {
	/**
	* Constructeur
	*/
	function CopixTemplateWritableProperty ($pName, $pCaption, $pValue) {
		parent::CopixTemplateSimpleProperty ($pName, $pCaption, $pValue);
	}

	/**
	* gets the HTML code for the editor
	* @return string
	*/
	function getHtml (){
		return '<label for="'.$this->getName ().'">'.$this->getCaption ().'</label><br /><input type="text" value="'.$this->getValue().'" name="'.$this->getName().'" />';
	}
}
?>