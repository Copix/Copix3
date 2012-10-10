<?php
/**
* Représente un titre
*/
class CopixTemplateTitle extends CopixTemplateElement {
	/**
	* Constructeur
	*/
	function CopixTemplateTitle (){
		parent::CopixTemplateElement ();
		$this->_addStylesProperties ();
		$this->_addProperty (new CopixTemplateWritableProperty ('titleContent', 'Contenu', 'Titre par défaut'));
		$this->_addProperty (new CopixTemplateComboProperty  ('titleLevel', 'Niveau', '1', array ('1'=>'1', '2'=>'2', '3'=>'3', '4'=>'4', '5'=>'5')));
		$this->_caption = 'Titre';
	}

	/**
	* génération du code HTML
	*/
	function getHtml (){
		//Partie déclaration de la div
		$buffer  = '<h'.intval ($this->getPropertyValue ('titleLevel')).'>';
		$buffer .= $this->getPropertyValue ('titleContent');
        $buffer .= '</h'.intval ($this->getPropertyValue ('titleLevel')).'>';
		return $buffer;
	}
}
?>