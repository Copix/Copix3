<?php
/**
 * 
 */

/**
 * Classe de base pour les portlets
 */
abstract class Portlet {
	/**
	 * Tableau des éléments de rubrique auquel la portlet fait référence
	 *
	 * @var array
	 */
	protected $_arHeadingElements = array ();
	
	/**
	 * La liste des types de contenu autorisés dans la portlet
	 *
	 * @var 
	 */
	protected $_arEnabledTypes = array ();
	
	/**
	 * L'identifiant de la portlet (dd)
	 *
	 * @var string
	 */
	protected $_randomId = null;
	
	/**
	 * L'identifiant de la portlet (utilisé en interne)
	 *
	 * @var string
	 */
	public $id_portlet = null;
	
	/**
	 * Variable de la portlet (colonne)
	 *
	 * @var string
	 */
	public $variable = null;
	
	/**
	 * Position de la portlet dans la variable
	 *
	 * @var string
	 */
	public $position = null;

	/**
	 * D'éventuelles données supplémentaires sur la façon d'afficher les éléments
	 *
	 * @var array
	 */
	protected $_moreData = array();
	
	private $_etat = null;
	
	/**
	 * En cours de modification
	 */
	const UPDATED = 1;
	
	/**
	 * Sauvegardée
	 */
	const SAVED = 2;
	
	/**
	 * En cours d'affichage
	 */
	const DISPLAYED = 3;

	/**
	 * Construction de l'objet portlet
	 */
	public function __construct (){
		$this->_randomId = uniqid ('portlet');
		$this-> content_portlet = "not defined";
		$this->_etat = self::UPDATED;
	}
	
	/**
	 * Indique si la portlet peut être mise en cache.
	 *
	 * @return boolean
	 */
	public function isCachable (){
		return false;
	}
	
	/**
	 * Identifiant de la portlet dans la bdd
	 * @return int
	 */
	public function getId (){
		return $this->id_portlet;
	}
	
	/**
	 * Identifiant de la portlet
	 * @return int
	 */
	public function getRandomId(){
		return $this->_randomId;
	}
	
	/**
	 * Retourne le contenu de la portlet dans un format affichable 
	 *
	 * @param string $pRendererMode    le mode de rendu demandé
	 * @param string $pRendererContext le contexte de rendu
	 */
	public function render ($pRendererMode, $pRendererContext, $pArOptions = array()){
		$toReturn = '';

		$params = new CopixParameterHandler();
		$params->setParams($pArOptions);

		if($params->getParam ('start', true)){
			$toReturn .= $this->_renderStart ($pRendererMode, $pRendererContext);
		}
		if($params->getParam ('content', true)){
			$toReturn .= $this->_renderContent ($pRendererMode, $pRendererContext);
		}
		if($params->getParam ('finalize', true)){
			$toReturn .= $this->_renderFinalize ($pRendererMode, $pRendererContext);
		}
		if ($pRendererContext == RendererContext::DISPLAYED){
			_notify('cms_display', array('type'=>'portlet', 'element'=>$this));
		} else {
			_notify('cms_display', array('type'=>'portlet', 'element'=>$this, 'displayToolsBar'=>false));
		}
		return $toReturn;
	}
	
	/**
	 * Lancé au début du rendu des portlets. Se charge de retourner le code "commun" a 
	 *  présenter avant le rendu spécifique de la portlet 
	 * @return string
	 */
	protected function _renderStart ($pRendererMode, $pRendererContext){
		if ($pRendererContext == RendererContext::DISPLAYED){
			return '<div class="displayPortlet" id="'.$this->getRandomId ().'">';
		}
		return '<div class="portlet clearfix" id="'.$this->getRandomId ().'">';
	}

	/**
	 * lancé a la fin du rendu des portlets. Se charge de retourner le code "commun" a présenter
	 * après le rendu spécifique de la portlet
	 * @return string
	 */
	protected function _renderFinalize ($pRendererMode, $pRendererContext){
		return '</div>';
	}

	/**
	 * Rendu du contenu de la portlet. Surchargé par les filles
	 * @return string
	 */
	abstract protected function _renderContent ($pRendererMode, $pRendererContext);
	
	/**
	 * Récupère la liste des éléments de rubriques attachés à la portlet, par type
	 *
	 * @param string $pType le type des éléments que l'on recherche
	 * @return array 
	 */
	public function getElementsByType ($pType){
		$toReturn = array ();

		foreach ($this->_arHeadingElements as $id=>$element){
			//Si $pType est un tableau, il faut que le type d'élément soit dedans
			//Si ce n'est pas un tableau, il faut que le type soit égal
			if ((is_array ($pType) && in_array ($element->type_hei, $pType)) 
			     || 
			     ($element->type_hei == $pType)){
				$toReturn[] = $toReturn; 
			}
		}

		return $toReturn;
	}

	/**
	 * Attache des éléments à la portlet. 
	 * 
	 * On ne peut attacher à la portlet que des éléments qui disposent d'un public id
	 * Lance une exception si l'élément que l'on tente d'attacher n'existe pas
	 * 
	 * @param mixed $pPublicId le ou les identifiants des éléments à attacher à la portlet
	 */
	public function attach ($pPublicId, $pPosition = null){
		if (is_array ($pPublicId)){
			$listePortletElements = array();
			foreach ($pPublicId as $elementId){
				$listePortletElements[] = $this->attach ($elementId);
			}
			return $listePortletElements;
		}else{
			if ($pPosition === null){
				$pPosition = $this->getLastHeadingElementPosition() + 1;
			}

			$element = _ioClass ('heading|HeadingElementInformationServices')->get ($pPublicId);
			$portletElement = _class ('portal|PortletElement');
			$portletElement->setHeadingElement ($element);
			$this->_arHeadingElements[$pPosition] = $portletElement;
			return $portletElement;
		}
	}
	
	/**
	 * Récupère un élément attaché a la portlet
	 * 
	 * @param string  
	 */
	protected function _getElement ($pPublicId){
		$informations = _ioClass ('heading|HeadingElementInformationServices')->get ($pPublicId);	
	}
	
	/**
	 * Déttache un ou des éléments de la portlet
	 *
	 * @param mixed $pPublicId 
	 */
	public function dettach ($pPosition){
		if (is_array ($pPosition)){
			//Plusieurs éléments a supprimer
			foreach ($pPosition as $position){
				$this->dettach ($position);
			}			
		}else{
			//un seul élément a supprimer
			if (!array_key_exists($pPosition, $this->_arHeadingElements)){
				throw new CopixException ('Impossible de détacher '.$pPosition.' de la portlet (élément introuvable)');
			}else{
				unset ($this->_arHeadingElements[$pPosition]);
			}
		}
	}
	
	/**
	 * Indique si la portlet accepte les contenus de type donné
	 *
	 * @param string $pType le type de contenu que l'on souhaite tester 
	 * @return boolean
	 */
	public function accept ($pType){
		return in_array ($pType, $this->_arEnabledTypes);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param mixed    $pPublicId l'élément recherché 
	 * @param boolean
	 */
	public function contains ($pPublicId, $pAll = true){
		//Plusieurs éléments donnés, on test sur l'ensemble des éléments
		if (is_array ($pPublicId)){
			$contains = false;
			foreach ($pPublicId as $publicId){
				if (! ($contains = $contains || $this->contains ($publicId))){
					//Un élément au moins n'a pas été trouvé 
					if ($pAll){
						//Si tous les éléments étaient requis, on indique l'échec
						return false;
					}
				}else{
					//Un élément au moins a été trouvé
					if (!$pAll){
						//Si tous les éléments ne sont pas nécessaires dans la liste, c'est ok
						return true;
					}
				} 
			}
			return $contains;//ne devrait pouvoir contenir que vrai
		}
		
		//Un seul élément a vérifier
		return isset ($this->_arHeadingElements[$pPublicId]); 
	}
	
	/**
	 * Défini les types de contenu autorisés dans la portlet
	 * 
	 * @param mixed les types d'éléments autorisés 
	 */
	public function addEnabledTypes ($pTypes){
		if (is_array ($pTypes)){
			foreach ($pTypes as $type){
				$this->addEnabledTypes ($type);
			}
		}else{
			if (!in_array ($pTypes, $this->_arEnabledTypes)){
				$this->_arEnabledTypes[] = $pTypes;
			}
		}
	}
	
	/**
	 * Supprime les types donnés des types actuellement autorisés
	 * 
	 * @param mixed   $pTypes les types que l'on souhaite supprimer des types autorisés
	 */
	public function removeEnabledTypes ($pTypes){
		if (is_array ($pTypes)){
			foreach ($pTypes as $type){
				$this->removeEnabledTypes ($type);
			}
		}else{
			if (count ($this->getElementsByType ($type)) > 0){
				throw new CopixException ("Impossible de supprimer des types de contenu autorisés le type $type qui est utilisé dans la portlet actuelle");
			}else{
				if (($pos = array_search ($pTypes, $this->_arEnabledTypes)) !== false){
					unset ($this->_arEnabledTypes[$pos]);
				}
			}
		}
	}
	
	public function getPortletElementAt ($pPosition){
		if (array_key_exists($pPosition, $this->_arHeadingElements)){
			return $this->_arHeadingElements[$pPosition];
		}
		return null;
	}
	
	/**
	 * Retourne le tableau des types autorisés dans la portlet
	 */
	public function getEnabledTypes (){
		return $this->_arEnabledTypes;
	}
	
	/**
	 * Récupère les éléments
	 */
	public function getElements (){
		return $this->_arHeadingElements;		
	}
	
	/**
	 * Récupère la liste des éléments à sauvegarder (peut être differente de la liste des elements)
	 * Comprend les elements utilisés et référencés dans la portlet 
	 */
	public function getElementsToSave (){
		return $this->getElements();		
	}
	
	/**
	 * Retourne la liste des options d'un element identifié par sa position
	 *
	 * @param int $pPublicId
	 */
	public function getElementOptions ($pPosition){
		if (array_key_exists ($pPosition, $this->_arHeadingElements)){
			return $this->_arHeadingElements[$pPosition]->getOptions ();
		}
		return array();
	}
	
	/**
	 * Retourne la cle de la derniere occurence d'un element du tableau d'element
	 *
	 * @param int $pPublicId l'identifiant de l'element
	 * @return int
	 */
	public function getLastHeadingElementPosition (){
		if (empty($this->_arHeadingElements)){
			return -1;
		}
		end ($this->_arHeadingElements);
		return key ($this->_arHeadingElements);
	}
	
	/**
	 * Renseigne l'etat de la portlet
	 *
	 * @param int $pEtat
	 */
	public function setEtat ($pEtat){
		$this->_etat = $pEtat;
	}
	
	/**
	 * Retourne l'etat de la portlet
	 *
	 * @return int
	 */
	public function getEtat (){
		return $this->_etat;
	}
	
	/**
	 * Charge les informations de la portlet à partie d'un autre objet
	 *
	 * @param Portlet $pPortlet
	 */
	public function loadFromObject (Portlet $pPortlet){
		$this->_arHeadingElements = $pPortlet->getElements ();
		$this->_arEnabledTypes = $pPortlet->getEnabledTypes ();
		$this->_moreData = $pPortlet->getOptions ();
		$this->variable = $pPortlet->variable;
		$this->position = $pPortlet->position;
	}
	
	/**
	 * Ajoute une option à la portlet : ex : le template utilisé pour afficher les elements de la portlet
	 *
	 * @param string $pKey
	 * @param string $pValue
	 */
	public function setOption ($pKey, $pValue){
		$this->_moreData[$pKey] = $pValue;
	}
	
	public function setOptions (array $pOptions){
		$this->_moreData = array_merge($this->_moreData, $pOptions);
	}
	
	/**
	 * Retourne une option de la portlet
	 *
	 * @param string $pKey
	 * @return string/null 
	 */
	public function getOption ($pKey, $pDefaultValue = null){
		return (array_key_exists ($pKey, $this->_moreData)) ? $this->_moreData[$pKey] : $pDefaultValue;
	}
	
	/**
	 * Retourne le tableau des informations supplementaires de la portlet, ex : template utilisé pour le render
	 *
	 * @return array
	 */
	public function getOptions (){
		return $this->_moreData;
	}
	
	public function moveUpElement ($pPosition){
		if ($pPosition > 0){
			$corresp = $this->removeNullElements();
			$pPosition = $corresp[$pPosition]; 
			$temp = $this->_arHeadingElements[$pPosition - 1];
			$this->_arHeadingElements[$pPosition - 1] = $this->_arHeadingElements[$pPosition];
			$this->_arHeadingElements[$pPosition] = $temp;
		}
	}
	
	public function moveDownElement ($pPosition){
		if ($pPosition < $this->getLastHeadingElementPosition ()){
			$corresp = $this->removeNullElements();
			$pPosition = $corresp[$pPosition];
			$temp = $this->_arHeadingElements[$pPosition + 1];
			$this->_arHeadingElements[$pPosition + 1] = $this->_arHeadingElements[$pPosition];
			$this->_arHeadingElements[$pPosition] = $temp;
		}
	}
	
	/**
	 * Permet de supprimer les éléments qui ont été supprimés et sont donc null et retourne le tableau de correspondance des positions 
	 * @return array
	 */
	private function removeNullElements(){
		$newArray = array();
		$corresp = array();
		ksort($this->_arHeadingElements);
		$i = 0;
		foreach ($this->_arHeadingElements as $oldKey => $element){
			if($element != null){
				$newKey = $i++;
				$newArray[$newKey] = $element;
				$corresp[$oldKey] = $newKey; 
			}
		}
		$this->_arHeadingElements = $newArray; 
		return $corresp;
	}	
}