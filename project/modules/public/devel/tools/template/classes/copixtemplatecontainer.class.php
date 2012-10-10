<?php
/**
* Elements capable de contenir de sous éléments
*/
CopixClassesFActory::fileInclude ('template|copixtemplateelement');
CopixClassesFActory::fileInclude ('template|copixtemplatesimpleproperty');
CopixClassesFActory::fileInclude ('template|copixtemplatecomboproperty');

class CopixTemplateContainer extends CopixTemplateElement {
	/**
	* Elements contenus dans le template
	* @var array
	*/
	var $_elements = array ();

	/**
	* Constructor
	*/
	function CopixTemplateContainer (){
		parent::CopixTemplateElement();
		$this->_caption = 'Conteneur générique';
	}

	/**
	* Ajout d'un élément dans le template
	* @param CopixTemplateElement $element l'élément à ajouter
	*/
	function addElement (& $element){
		$element->setParent ($this);
		$this->_elements[$element->getId ()] = & $element;
	}

	/**
	* Suppression d'un élément
	* @param string $pId l'identifiant de l'élément à supprimer.
	* @return CopixTemplateElement
	*/
	function & removeElement ($pId){
		//L'élément est un fils direct
		if (($this->getElement ($pId)) !== null){
			$element = & $this->_elements[$pId];
			unset ($this->_elements[$pId]);
			$null = null;
			$element->setParent ($null);
			return $element;
		}

		//removes the element
		foreach ($this->getElements () as $name=>$element){
			if (is_a ($element, 'copixtemplatecontainer')){
				if (($removed = & $this->_elements[$name]->removeElement ($pId)) !== null){
					return $removed;
				}
			}
		}

		//No removed
		$toReturn = null;
		return $toReturn;
	}

	/**
	* Récupère un élément d'identifiant donné
	* @param int $id l'identifiant de l'élément à récupérer.
	* @return CopixTemplateElement
	*/
	function & getElement ($id){
		//Pas d'utilisation de l'opérateur ternaire, fait planter les références
		if (isset ($this->_elements[$id])){
			return $this->_elements[$id];
		}
		$toReturn = null;
		return $toReturn;
	}

	/**
	* Récupération de l'ensemble des élméents
	* @return array of CopixTemplateElement
	*/
	function & getElements (){
		return $this->_elements;
	}

	/**
	* Indique si l'objet accepte les éléments de type $className
	* @param string $className le nom de l'objet à tester
	* @return boolean si l'on accepte (true) ou non (false) l'objet passé
	*/
	function accept ($className){
		return true;
	}

	/**
	* Indique si l'on accepte l'objet passé en paramètre
	* @param Mixed $object 
	*/
	function acceptObject ($object){
		return $this->accept (get_class ($object));
	}

	/**
    * Retourne le code HTML du container
    * @return string
    */
	function getHtml (){
		$buffer = '';
		foreach ($this->_elements as $elementId => $element){
			$buffer .= $element->getHtml ();
		}
		return $buffer;
	}

	/**
	* Recherche un élément grâce à son identifiant
	* @param int $pId l'identifiant de l'élément que l'on recherche
	* @return CopixTemplateElement l'élément trouvé ou null si non trouvé
	*/
	function & getTemplateElementById ($pId){
		$toReturn = null;
		if (strcmp($this->_id, $pId) == 0) {
			return $this;
		}
		foreach ($this->getElements() as $elementId=>$element){
			if (($toReturn = & $this->_elements[$elementId]->getTemplateElementById ($pId)) !== null){
				return $toReturn;
			}
		}
		return $toReturn;
	}

	/**
    * Retourne la liste des classes que l'objet s'attend à avoir "au minimum" pour être valide.
    * 
    * Un container titre par exemple n'est pas valide tant qu'un texte ne lui à pas été assigné.
    * 
    * @access private
    * @return array of string (liste des classes attendues)
    */
	function _expects (){
		return array ();
	}

	/**
    * Indique si l'objet dispose de toutes les composantes auxquelles il s'attend (expects)
    */
	function isComplete (){
		//on parcours l'ensemble des éléments attendus pour voir si l'un des scénario est repecté.
	}

	/**
    * Rétablit les références
    */
	function __wakeup (){
		foreach ($this->_elements as $key=>$element){
			$this->_elements[$key]->setParent ($this);
		}
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

		//manipulations bizarres pour être sûr d'obtnir la références aux objets
		$childsProperties = array ();
		foreach ($this->getElements () as $name=>$element){
			$childProperties = & $this->_elements[$name]->getPropertiesOfKind ($pKind);
			foreach ($childProperties as $childNameProperty=>$childProperty){
				if (is_a ($childProperty, $pKind)){
					$results[] = & $childProperties[$childNameProperty];
				}
			}
		}
		return $results;
	}
}
?>