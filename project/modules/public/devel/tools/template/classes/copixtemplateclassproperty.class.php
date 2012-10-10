<?php
/**
* Représente une propriété identifiant d'un élément de template
*/
CopixClassesFactory::fileInclude ('template|copixtemplatecomboproperty');

class CopixTemplateClassProperty extends CopixTemplateComboProperty {
	/**
	* Constructeur
	* @param string $pName le nom
	* @param string $pCaption le libellé
	* @param mixed $pValue la valeur
	*/
	function CopixTemplateClassProperty ($pName, $pCaption, $pValue){
		parent::CopixTemplateComboProperty ($pName, $pCaption, $pValue, array ());
	}

	/**
	* Récupère la liste des identifiants possibles
	* @return array la liste des identifiants
	* @access private
	*/
	function _getPossibleClasses (){
		//récupère la liste des propriétés de type "feuille de styles" déclarées dans l'arbre des objets
		//auquel appartient notre élémnet
		$owner = & $this->getOwner ();
		$root  = & $owner->getRootParent ();
		$properties = & $root->getPropertiesOfKind ('CopixTemplateStyleSheetProperty');

		//Récupère tous les identifiants déclarés dans ces propriétés.
		$arClasses = array ();
		foreach ($properties as $key=>$property){
			foreach ($properties[$key]->getPossibleClasses () as $name){
				if (!isset ($arClasses[$name])){
					$arClasses[$name] = $name;
				}
			}

		}
		ksort ($arClasses);

		//retourne la liste des classes trouvés.
		return $arClasses;
	}

	/**
	* Récupèration du code de l'éditeur HTML pour la propriété.
	* @return string le code HTML de l'éditeur
	*/
	function getHtml (){
		$this->setPossibleValues ($this->_getPossibleClasses ());
		return parent::getHtml ();
	}
}
?>