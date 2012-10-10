<?php
CopixClassesFactory::fileInclude ('template|copixtemplateelement');
CopixClassesFactory::fileInclude ('template|copixtemplatecontainer');
CopixClassesFactory::fileInclude ('template|copixtemplatedivcontainer');
CopixClassesFactory::fileInclude ('template|copixtemplatehtmlcontainer');
CopixClassesFactory::fileInclude ('template|copixtemplatetitle');
CopixClassesFactory::fileInclude ('template|copixtemplatebr');
CopixClassesFactory::fileInclude ('template|copixtemplateparagraph');
CopixClassesFactory::fileInclude ('template|copixtemplateproperty');
CopixClassesFactory::fileInclude ('template|copixtemplatesimpleproperty');
CopixClassesFactory::fileInclude ('template|copixtemplatecomboproperty');
CopixClassesFactory::fileInclude ('template|copixtemplatewritableproperty');
CopixClassesFactory::fileInclude ('template|copixtemplatestylesheetproperty');
CopixClassesFactory::fileInclude ('template|copixtemplateidproperty');
CopixClassesFactory::fileInclude ('template|copixtemplateclassproperty');

/**
* Classe permettant de réalisation les opération d'édition d'un template.
*/
class CopixTemplateEditor {
	/**
	* L'élément principal
	* @var CopixTemplateContainer
	* @access private
	*/
	var $_root = null;

	/**
	* Le clipboard
	* @var CopixTemplateElement
	* @access private
	*/
	var $_clipboard = null;
	
	/**
	* La pile de contexte pour la gestion des undo / redo
	* @var array
	* @access private
	*/
	var $_contextStack = null;
	
	/**
	* Le catalogue des objets disponibles pour l'édition
	* @var array of string (liste des noms de classe que l'on est succeptible d'utiliser)
	* @access private
	*/
	var $_elementsCatalog = array ();

	/**
	* Récupère l'élément de template grâce à son identifiant
	* @param string $pId l'identifiant de l'élément.
	* @return CopixTemplateElement
	*/
	function & getTemplateElementById ($pId){
		return $this->_root->getTemplateElementById ($pId);
	}

	/**
	* Constructeur, instancie les premiers objets indispensable au fonctionnement du template
	*/
	function CopixTemplateEditor (){
		$this->_clipboard = & new CopixTemplateContainer ();
	}

	/**
	* Définition de l'objet root
	* @param CopixTemplateElement $pRoot L'élément "root" à d&éfinir dans l'éditeur (généralement une page HTML, un template global, ...
	*/
	function setRoot (& $pRoot){
		$this->_root = & $pRoot;
	}

	/**
	* Chargement du contenu de l'éditeur depuis une chaine de caractère
	* @param string $string la chaine de caractère reécupérée depuis la méthode getSaveString (). Si null est donné, crée un nouveau template.
	* @return contenu chargé (true) ou (non)
	*/
	function loadFromString ($string){
		if ($string === null){
			$this->_root = new CopixTemplateHTMLContainer ();
			return true;
		}

		if (($root = unserialize ($string)) !== false){
			$this->_root = $root;
			return true;
		}
		return false;
	}

	/**
	* Récupère le contenu de l'éditeur sous la forme d'une chaine de caractère.
	* 
	* Cette chaine de caractère poura être utilisée pour recharger l'éditeur avec la méthode loadFromString
	* @return string
	*/
	function getSaveString (){
		return serialize ($this->_root);
	}

	/**
	* Place un élément dans le clipboard
	* @param string $pId l'identifiant de l'élément que l'on souhaite mettre dans le clipboard
	*/
	function setClipboardFromId ($pId){
		if (($element = & $this->getTemplateElementById ($pId)) !== null){
			$this->setClipboardFromObject ($element);
		}
	}

	/**
	* Sets an object into the clipboard
	* @param CopixTemplateElement $pObject l'élément à ajouter au clipboard
	*/
	function setClipboardFromObject (& $pObject){
		$this->_clipboard->addElement ($pObject);
	}

	/**
	* Coupe un objet dans le clipboard
	* @param string $pId l'identifiant de l'objet à couper
	*/
	function cut ($pId){
		$this->_saveContextStack ();
		$object = & $this->findCopixTemplateElementFromId ($pId);
		if (($parent = & $object->getParent ()) !== null){
			$parent->removeElement ($object->getId ());
		}
		$this->setClipboardFromObject ($object);
	}

	/**
	* Copie un objet dans le clipboard
	* @param string $pId l'identifiant de l'objet à copier dans le clipboard
	*/
	function copy ($pId){
		$this->setClipboardFromId ($pId);
	}
	
	/**
	* supprime l'élément donné de l'arborescence des objets
	* @param string $pId l'identifiant de l'élément à supprimer
	*/
	function remove ($pId){
		$this->_root->removeElement ($pId);
	}

	/**
	* Colle l'élément du clipboard dans le container d'identifiant donné
	* @param string $pContainerId L'identifiant de l'élément dans lequel coller le contenu du clipboard
	*/
	function paste ($pContainerId){
	}

	/**
	* Restores the last available context in the action stack
	* @return boolean true if we did undo, false if not
	*/
	function undo (){
	}

	/**
	* Restores the next available context une the action stack
	*/
	function redo (){
	}

	/**
	* Sauvegarde l'état courant de l'objet dans la pile de contexte pour autoriser les undo/redo
	*/
	function _saveContextStack (){
	}

	/**
	* Pop la pile de contexte qui permet la gestion du undo/redo
	* @return string le root (CopixTemplateElement) sérialisé que l'on vient de poper
	*/
	function _popContextStack (){
	}
	
	/**
	* Gets the root element
	* @return CopixTemplateContainer
	*/
	function & getRoot (){
		return $this->_root;
	}
	
	/**
	* Charge le catalogue des classes disponibles pour l'éditeur
	*/
	function loadCatalog (){
		$this->_elementsCatalog = array ();
		$this->addToCatalog ('CopixTemplateDivContainer');
		$this->addToCatalog ('CopixTemplateTitle');
		$this->addToCatalog ('CopixTemplateBR');		
		$this->addToCatalog ('CopixTemplateParagraph');		
	}
	
	/**
	* Ajoute des éléments au catalogue des éléments disponible pour l'éditeur
	* @param string $className La classe que l'on souhaite rendre disponible pour l'editeur
	*/
	function addToCatalog ($className){
		$this->_elementsCatalog[] = $className;
	}
	
	/**
	* Création d'un nouvel élément de type $pClassName et ajout à l'élément $pId
	* @param string $pClassName le nom de la classe de l'élément que l'on souhaite rajouter
	* @param string $pId l'identifiant de l'élément auquel on souhaites rajouter un élément.
	* @return boolean si nous avons effectivement (true) rajouté l'élément à l'éditeur ou non (false)
	*/
	function addElementTo ($pClassName, $pId){
		//On vérifie si l'élémnet existe
		if (($element = & $this->getTemplateElementById ($pId)) === null){
			return false;
		}
		//est-ce un container ?
		if (! is_a ($element, 'copixtemplatecontainer')){
			return array ();
		}
		//accepte t il l'élément ?
		if (! $element->accept ($pClassName)){
			return false;
		}
		
		$newElement = & new $pClassName ();
		$element->addElement ($newElement);
		return $newElement->getId ();
	}

	/**
	* Indique les éléments que l'on peut ajouter à l'élément d'identifiant donné
	* @param string $pId l'identifiant de l'élément auquel on souhaites ajouter 
	*/
	function getAddPossibilitiesForElementById ($pId){
		//si l'élément n'existe pas, rien à ajouter...
		if (($element = & $this->getTemplateElementById ($pId)) === null){
			return array ();
		}

		//Ce n'est pas un container, on ne peut donc rien ajouter à l'élément
		if (! is_a ($element, 'copixtemplatecontainer')){
			return array ();
		}

		//C'est un container, on va lui demander pour chaque élément du catalogue s'il l'accepte
		$toReturn = array ();
		foreach ($this->_elementsCatalog as $key=>$classInCatalog){
			if ($element->accept ($classInCatalog)){
				$tmpObject = & new $classInCatalog ();
				$toReturn[$classInCatalog] = $tmpObject->getCaption ();
			}
		}
		return $toReturn;
	}
	
	/**
	* Loads the catalog on each unserializz operation
	*/
	function __wakeup (){
		$this->loadCatalog ();
	}
	
	/**
	* Validation des propriétés d'un élément du template
	* @param string $pElementId l'identifiant de l'élménet dont on veut valider les propriétés
	* @param array $pArValues un tableau associatif contenant la liste des valeurs de la propriété
	* @return int nombre de propriété dont la valeur à été mise à jour
	*/
	function validProperties ($pElementId, $pArValues){
		if (($element = & $this->getTemplateElementById ($pElementId)) === null){
			return 0;
		}
		return $element->validProperties ($pArValues);
	}
}
?>