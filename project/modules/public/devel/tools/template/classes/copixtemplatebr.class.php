<?php
/**
* Représente un saut de ligne
*/
class CopixTemplateBR extends CopixTemplateElement {
	/**
	* Constructeur
	*/
	function CopixTemplateBR (){
		parent::CopixTemplateElement ();
		$this->_addProperty (new CopixTemplateComboProperty  ('clear', 'Réinitialisation positions flottates', null, array (''=>'Aucun', 'both'=>'Droite et Gauche', 'left'=>'Gauche', 'right'=>'Droite')));
		$this->_caption = 'Saut de ligne';
	}

	/**
	* génération du code HTML
	*/
	function getHtml (){
		//Partie déclaration de la div
		if (($value = $this->getPropertyValue ('clear')) != ''){
		    return '<br style="clear: '.$value.'>';			
		}else{
			return '<br />';
		}
	}
}
?>