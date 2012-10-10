<?php
/**
* Représente une propriété simple d'un élément de template
*/
CopixClassesFactory::fileInclude ('template|copixtemplateproperty');

class CopixTemplateSimpleProperty extends CopixTemplateProperty {
	/**
	* Constructeur
	* @param string $pName le nom
	* @param string $pCaption le libellé
	* @param mixed $pValue la valeur
	*/
	function CopixTemplateSimpleProperty ($pName, $pCaption, $pValue){
		parent::CopixTemplateProperty ($pName, $pCaption, $pValue);
	}
}
?>