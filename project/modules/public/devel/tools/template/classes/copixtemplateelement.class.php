<?php
/**
* Classe de base pour tous les éléments de template dynamique Copix
*/
class CopixTemplateElement {
	/**
	* Identifiant
	* @var string
	*/
	var $_id;
	
	/**
	* Libellé de l'objet
	* @var string
	*/
	var $_caption;

	/**
	* Propriétés de l'objet
	* @var array
	*/
	var $_properties = array ();
	
	/**
	* Le parent de l'objet (l'objet dans lequel l'élement courant appartient).
	* 
	* Seul l'objet template "général" ne dispose pas de parent.
	* @var CopixTemplateContainer
	*/
	var $_parent = null;

	/**
	* Constructeur
	*/
	function CopixTemplateElement () {
		$this->_id      = uniqid ('TE', true);
		$this->_caption = 'Element';
	}
	
	/**
	* Ajoute les propriétés de style standard
	* @access private
	*/
	function _addStylesProperties (){
		$this->_addProperty (new CopixTemplateClassProperty  ('class', 'Classe CSS', null));
		$this->_addProperty (new CopixTemplateIdProperty     ('id', 'Identifiant', null));

		$this->_addProperty (new CopixTemplateSimpleProperty ('width', 'longueur', '100px'));
		$this->_addProperty (new CopixTemplateSimpleProperty ('height', 'hauteur', '100px'));
		$this->_addProperty (new CopixTemplateComboProperty  ('position', 'Position', null, array ('relative'=>'Relative', 'absolute'=>'Absolue', null=>'Défaut')));
		$this->_addProperty (new CopixTemplateComboProperty  ('float', 'Flottante ?', null, array ('left'=>'à gauche', 'right'=>'à droite', null=>'Non flottant')));
		$this->_addProperty (new CopixTemplateSimpleProperty ('border', 'Bordure', '1px solid #000000'));
		$this->_addProperty (new CopixTemplateSimpleProperty ('top', 'haut', ''));
		$this->_addProperty (new CopixTemplateSimpleProperty ('left', 'gauche', ''));
		$this->_addProperty (new CopixTemplateSimpleProperty ('margin-left', 'Marge gauche', ''));
		$this->_addProperty (new CopixTemplateSimpleProperty ('margin-right', 'Marge droite', ''));
		$this->_addProperty (new CopixTemplateSimpleProperty ('margin-top', 'Marge haut', ''));
		$this->_addProperty (new CopixTemplateSimpleProperty ('margin-bottom', 'Marge bas', ''));
	}
	
	/**
	* Création de la balise style=""
	* @param bool $pWithPropertyName indique si l'on souhaites que la fonction génère également style=""
	*/
	function _getStyleDeclaration ($pWithPropertyName = true){
		//propriétés de style
		$buffer = '';
		foreach (array ('position', 'float', 'border', 'width', 'height', 'top', 'left', 'margin-left', 'margin-right', 'margin-top', 'margin-bottom') as $propertyName){
			$property = & $this->getProperty ($propertyName);
			if ($property->getValue () !== null){
				$buffer .= $property->getName ().': '.$property->getValue ().';';
			}
		}
		if ((strlen ($buffer) > 0) && $pWithPropertyName){
			return ' style="'.$buffer.'" ';
		}
		return '';
	}
	
	/**
	* Récupération de la déclaration de la classe
	* @return string
	*/
	function _getClassDeclaration (){
		$buffer = '';
		$property = & $this->getProperty ('class');
		if ($property->getValue () !== null){
			$buffer .= ' class="'.$property->getValue ().'" ';
		}
		return $buffer;
	}

	/**
	* Récupère la déclaration de l'identifiant
	* @return string
	*/
	function _getIdDeclaration (){
		$buffer = '';
		$property = & $this->getProperty ('id');
		if ($property->getValue () !== null){
			$buffer .= ' id="'.$property->getValue ().'" ';
		}
		return $buffer;
	}

	/**
	* Retourne le libellé de l'objet (affichable)
	* @return string
	*/
	function getCaption (){
		return $this->_caption;
	}
	
	/**
	* Récupère les propriétés de l'objet
	* @return CopixTemplateProperty
	*/
	function getProperties (){
		return $this->_properties;
	}

	/**
	* Récupère la propriété de nom $pName
	* @param string $pName le nom de la propriété à récupérer
	* @return CopixTemplateProperty ou null si elle n'existe pas
	*/
	function & getProperty ($pName){
		$toReturn = null;
		if (isset ($this->_properties[$pName])){
			$toReturn = & $this->_properties[$pName];
		}
		return $toReturn;
	}

	/**
	* Récupère la valeur de la propriété $pName
	* @param string $pName le nom de la propriété à récupérer
	* @return mixed la valeur de la propriété $pName
	*/
	function getPropertyValue ($pName){
		if (($property = & $this->getProperty ($pName)) !== null){
			return $property->getValue ();
		}
		return null;
	}

	/**
	* Définit la valeur d'une propriété $pName à $pValue
	* @param string $pName le nom de la propriété dont on veut définir la valeur
	* @param string $pValue la valeut de la propriété
	* @return l'ancienne valeur de la propriété, false si non trouvée
	*/
	function setPropertyValue ($pName, $pValue){
		if (($property = & $this->getProperty ($pName)) !== null){
			$oldValue = $property->getValue ();
		}else{
			return false;
		}
		$property->setValue ($pValue);
		return $oldValue;
	}

	/**
	* Retourne l'identifiant unique de l'élément.
	* @return int
	*/
	function getId (){
		return $this->_id;
	}

	/**
	* setter ID
	* @param id  int
	*/
	function setId ($id){
		$this->_id = $id;
	}	

	
	/**
	* Défini la valeur d'une propriété
	* 
	* Pour que la propriété soit définie, il est nécessaire qu'elle existe dans l'objet manipulé.
	* Si la propriété ne fait pas partie de l'objet, une erreur sera générée.
	*
	* @param CopixTemplateProperty $pProperty la propriété dont la valeur est à définir.
	*/
	function defineProperty ($pProperty){
		if (isset ($this->_properties[$pProperty->getName ()])){
			$this->_properties[$pProperty->getName ()] = $pProperty;
		}else{
			trigger_error ("Ajout de propriété impossible");
		}
	}
	
	/**
	* Ajoute une propriété à l'élément.
	*
	* Cette méthode est normalement appelée lors de la construction de l'objet, par les sous classes.
	*
	* @param CopixTemplateProperty $pProperty La propriété à ajouter à l'élément.
	*/
	function _addProperty (& $pProperty){
		$pProperty->setOwner ($this);
		$this->_properties[$pProperty->getName ()] = & $pProperty;
	}
	
	/**
	* Retourne le code HTML de l'élément
	* @return string
	* @abstract
	*/
	function getHtml (){
		return '';
	}
	
	/**
	* Définit l'objet parent
	* @param CopixTemplateElement $pParent l'objet parent à définir
	*/
	function setParent (& $pParent){
		$this->_parent = & $pParent;
	}

	/**
	* Récupère le parent d'un objet
	* @return CopixTemplateElement le parent de l'objet, null si aucun parent
	*/
	function & getParent (){
		return $this->_parent;
	}

	/**
	* Recherche un élément grâce à son identifiant
	* @param int $pId l'identifiant de l'élément que l'on recherche
	* @return CopixTemplateElement l'élément trouvé ou null si non trouvé
	*/
    function & getTemplateElementById ($pId){
		$toReturn = null;
    	if (strcmp ($this->_id, $pId) == 0) {
			$toReturn = & $this;
		}
		return $toReturn;
	}
	
	/**
	* Valide les propriétés de l'élément
	* @param array $pArValues un tableau associatif contenant l'ensemble des valeurs
	* @return int le nombre de propriété dont la valeur à été mise à jour.
	*/
	function validProperties ($pArValues){
		$updated = 0;
		foreach ($this->getProperties() as $name=>$value){
			if (isset ($pArValues[$name])){
				if (($this->setPropertyValue ($name, $pArValues[$name])) !== false){
					$updated++;
				}
			}
		}
		return $updated;
	}
	
	/**
	* Récupère l'élément parent de tous les objets
	* @return CopixTemplateElement l'élément parent principal de l'objet
	*/
	function & getRootParent (){
	  	$element = & $this;
		while (($tmpParent = & $element->getParent ()) !== null){
			$element = & $tmpParent;
		}
		return $element;
	}

	/**
	* Récupère la liste des propriétés d'un type donné
	* @param string $pKind le nom de classe
	*/
	function & getPropertiesOfKind ($pKind){
		$results = array ();
		foreach ($this->getProperties() as $name=>$property){
			if (is_a ($property, $pKind)){
				$results[] = & $this->_properties[$name];
			}
		}
		return $results;
	}
}
?>