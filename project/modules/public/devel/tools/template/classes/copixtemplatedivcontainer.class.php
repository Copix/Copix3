<?php
/**
* Représente une div
*/
class CopixTemplateDivContainer extends CopixTemplateContainer {
	/**
	* Constructeur
	*/
	function CopixTemplateDivContainer (){
		parent::CopixTemplateContainer();
		$this->_addStylesProperties ();
		$this->_caption = 'Cadre';
	}

	/**
	* génération du code HTML
	*/
	function getHtml (){
		//Partie déclaration de la div
		$buffer = '<div ';
		$buffer .= $this->_getIdDeclaration ();
		$buffer .= $this->_getClassDeclaration ();
		$buffer .= $this->_getStyleDeclaration ();
		$buffer .= '>';

		//partie intérieur de la div
		foreach ($this->_elements as $elementId => $element){
			$buffer .= $element->getHtml ().'
			';
		}
		
		//Fermeture de la div
		$buffer .= '</div>
		';
		return $buffer;
	}
}
?>