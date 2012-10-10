<?php
/**
* Représente une propriété identifiant d'un élément de template
*/
CopixClassesFactory::fileInclude ('template|copixtemplatecomboproperty');

class CopixTemplateIdProperty extends CopixTemplateComboProperty {
	/**
	* Constructeur
	* @param string $pName le nom
	* @param string $pCaption le libellé
	* @param mixed $pValue la valeur
	*/
	function CopixTemplateIdProperty ($pName, $pCaption, $pValue){
		/**
		On initialise la liste des valeurs possibles à vide, car on devra rechercher les propriétés 
		juste à temps avant l'affichage html
		*/
		parent::CopixTemplateComboProperty ($pName, $pCaption, $pValue, array ());
	}

	/**
	* Récupère la liste des identifiants possibles
	* @return array la liste des identifiants
	* @access private
	*/
	function _getPossibleId (){
		//récupère la liste des propriétés de type "feuille de styles" déclarées dans l'arbre des objets
		//auquel appartient notre élémnet
		$owner = & $this->getOwner ();
		$root  = & $owner->getRootParent ();
		$properties = & $root->getPropertiesOfKind ('CopixTemplateStyleSheetProperty');

		//Récupère tous les identifiants déclarés dans ces propriétés.
		$arId = array ();
		foreach ($properties as $key=>$property){
			foreach ($properties[$key]->getPossibleId () as $name){
				if (!isset ($arId[$name])){
					$arId [$name] = $name;
				}
			}

		}
		asort ($arId);

		//retourne la liste des classes trouvés.
		return $arId;
	}

	/**
	* Récupèration du code de l'éditeur HTML pour la propriété.
	* @return string le code HTML de l'éditeur
	*/
	function getHtml (){
		$this->setPossibleValues ($this->_getPossibleId ());
		return parent::getHtml ();
	}
}
?>