<?php
/**
* Représente un saut de ligne
*/
class CopixTemplateParagraph extends CopixTemplateElement {
	/**
	* Constructeur
	*/
	function CopixTemplateParagraph (){
		parent::CopixTemplateElement ();
		$this->_addStylesProperties ();
		$this->_addProperty (new CopixTemplateSimpleProperty  ('text', 'Contenu', 'Contenu du paragraphe'));
		$this->_caption = 'Paragraphe';
	}

	/**
	* génération du code HTML
	*/
	function getHtml (){
		return '<p>'.$this->getPropertyValue ('text').'</p>';
	}
}
?>