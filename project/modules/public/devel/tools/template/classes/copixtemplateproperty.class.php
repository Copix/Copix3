<?php
/**
* Propriété d'un élément de template.
*/
class CopixTemplateProperty {
	/**
	* Nom de la propriété
	* @access private
	*/
	var $_name = null;

	/**
	* La valeur de la propriété
	* @access private
	*/
	var $_value = null;

	/**
	* Le libellé
	* @var string
	*/
	var $_caption = null;
	
	/**
	* L'objet auquel appartient la propriété
	* @var CopixTemplateElement
	*/
	var $_owner = null;

	/**
	* Constructeur
	* @param string $pName le nom de la propriété
	* @param string $pCaption le libellé
	* @param string $pValue la valeur de la propriété
	*/
	function CopixTemplateProperty ($pName, $pCaption, $pValue){
		$this->_name    = $pName;
		$this->_caption = $pCaption;
		$this->_value   = $pValue;
	}

	/**
	* Récupère le nom de la propriété
	* @return string
	*/
	function getName (){
		return $this->_name;
	}

	/**
	* Récupère le libellé
	* @return string
	*/
	function getCaption (){
		return $this->_caption;
	}

	/**
	* Récupère la valeur de la propriété
	* @return string
	*/
	function getValue (){
		return (strlen (trim ($this->_value)) > 0) ? $this->_value : null;
	}
	
	/**
	* Définit la valeur de la propriété
	* @param string $pValue la nouvelle valeur de la propriété
	*/
	function setValue ($pValue){
		$this->_value = (strlen (trim ($pValue)) > 0) ? $pValue : null;
	}

	/**
	* retourne le code HTML pour éditer la propriété
	* @return string
	*/
	function getHtml (){
		return '<label for="'.$this->_name.'">'.$this->_caption.'</label><input type="text" value="'.$this->_value.'" name="'.$this->_name.'" id="'.$this->_name.'" />';
	}
	
	/**
	* On indique quel est le parent qui porte la propriété
	* 
	* Cette fonctionnalité permettra aux propriétés dynamiques d'inscpecter les parents pour savoir quels sont les éléments disponibles
	* (par exemple les variables de templates en fonction de la position dans la structure).
	* 
	* @param CopixTemplateElement $pCopixTemplateElementOwner L'élément parent qui portera la propriété.
	*/
	function setOwner (& $pCopixTemplateElementOwner){
		$this->_owner = & $pCopixTemplateElementOwner;
	}
	
	/**
	* Récuère l'objet auquel appartient  la propriété
	*/
	function & getOwner (){
		return $this->_owner;		
	}
}
?>