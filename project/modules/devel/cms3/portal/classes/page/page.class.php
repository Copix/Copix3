<?php
/**
 * @package     cms3
 * @subpackage  portal
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      VUIDART Sylvain
 */

/**
 * Classe de base pour les pages de contenu
 * @package     cms3
 * @subpackage  portal
 */
class Page {
	const DEFAULT_TEMPLATE = "100.visuel.page.tpl";

	/**
	 * When the page is displayed
	 */
	const DISPLAY = 1;

	/**
	 * When the page is updated
	 */
	const UPDATE = 2;

	// From HeadingElementInformations
	public $id_page = null;
	public $description_hei = null;
	public $caption_hei = null;
	public $type_hei = 'page';
	public $id_helt = null;
	public $parent_heading_public_id_hei = null;

	private $_state = self::DISPLAY;

	public $browser_page = null;

	/**
	 * Les portlets contenues dans la page
	 *
	 * @var array
	 */
	protected $_arPortlets = array ();

	/**
	 * Le mode des portlets en mémoire
	 *
	 * @var string
	 */
	protected $_arPortletModes = array ();

	/**
	 * Chargement des données a partir d'un tableau
	 */
	public function load ($pPageDescription){
		_ppo ($pPageDescription)->saveIn ($this);
	}

	/**
	 * Demande de rendu d'une page
	 *
	 * @param int $pRenderedId
	 * @return string
	 */
	public function render ($pMode, $pContext, $pExtra = array ()){
		$renderMode = new RendererMode ();
		$renderMode->assertIsValid ($pMode);

		$renderContext = new RendererContext ();
		$renderContext->assertIsValid ($pContext);
		
		switch ($pMode){
			case RendererMode::HTML:
				$renderer = new PageHtmlRenderer ();
				break;
			case RendererMode::TEXT:
				$renderer = new PageTextRenderer ();
				break;
		}

		if ($pContext == RendererContext::DISPLAYED){
			_notify('cms_display', array('type'=>'page', 'element'=>$this));
		} else {
			_notify('cms_display', array('type'=>'page', 'element'=>$this, 'displayToolsBar'=>false));
		}
		return $renderer->render ($this, $pMode, $pContext, $pExtra);
	}

	/**
	 * Récupère le template a utiliser pour la page
	 */
	public function getTemplate (){
		return $this->template_page;
	}

	/**
	 * Ajout d'une portlet a la page
	 *
	 * Il ne sera pas possible de mettre deux fois de suite la même portlet dans la page
	 *
	 * @param Portlet $pPortlet    la portlet à ajouter a la page
	 * @param int     $pColumn     la variable de colonne dans laquelle on souhaite positionner la portlet
	 *
	 * @return boolean si la portlet a été ajoutée
	 */
	public function addPortlet (Portlet $pPortlet, $pColumn, $pPosition = null){
		$this->_reorder($pColumn);

		if ($this->findPortletById ($pPortlet->getRandomId ()) !== null){
			return false;
		}

		if ($pPosition === null){
			$this->_arPortlets[$pColumn][] = $pPortlet;
		}else{
			//On passe par une valeur intermédiaire, sinon $pPortlet est considéré comme un tableau
			// et la fonction insère une valeur par propriété de tableau
			if(array_key_exists($pColumn, $this->_arPortlets)){
				array_splice ($this->_arPortlets[$pColumn], $pPosition, 0, 'foo');
			}
			$this->_arPortlets[$pColumn][$pPosition] = $pPortlet;
		}

		$positions = $this->getPortletPosition($pPortlet->getRandomId());
		$pPortlet->variable = $positions['column'];
		$pPortlet->position = $positions['position'];

		$this->_reorder($pColumn);
		return true;
	}

	/**
	 * Re-organise le tableau : supprime les identifiants intermediaires manquants
	 * utilisé avant le array_splice
	 *
	 * @param int $pColumn la variable du tableau de portlet
	 */
	private function _reorder($pColumn){
		if (array_key_exists ($pColumn, $this->_arPortlets)){
			foreach ($this->_arPortlets[$pColumn] = array_values ($this->_arPortlets[$pColumn]) as $position=>$portlet){
				$portlet->position = $position;
			}
		}
	}

	/**
	 * Recherche d'une portlet a partir de son identifiant
	 *
	 * @param  int $pPortletId l'identifiant de la portlet a rechercher dans la page
	 * @return object the portlet / null si non trouvé
	 */
	public function findPortletById ($pPortletId){
		foreach ($this->_arPortlets as $column=>$arPortletsInColumn){
			foreach ($arPortletsInColumn as $position => $portlet){
				if ($portlet->getRandomId () == $pPortletId){
					return $portlet;
				}
			}
		}
		return null;
	}

	/**
	 * Retourne la portlet d'identifiant donné
	 *
	 * @param int $pPortletId l'identifiant de la portlet à récupérer
	 * @see page::findPortletById
	 * @throws pageException
	 */
	public function getPortlet ($pPortletId){
		if (($result = $this->findPortletById ($pPortletId)) !== null){
			return $result;
		}
		throw new pageException ('Impossible de récupérer la portlet avec un identifiant de '.$pPortletId);
	}

	/**
	 * Supprime la portlet d'identifiant donné dans le tableau des portlets
	 *
	 * @param  int $pPortletId l'identifiant aleatoire (randomId) de la portlet
	 *
	 * @return boolean si la portlet a bien été supprimée
	 */
	public function deletePortlet ($pPortletId){
		foreach ($this->_arPortlets as $column=>$arPortletsInColumn){
			$moved = false;
			foreach ($arPortletsInColumn as $position => $portlet){
				if ($portlet->getRandomId () == $pPortletId){
					array_splice ($this->_arPortlets[$column], $position, 1);
					$this->_reorder($column);
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Récupération de la position de la portlet d'identifiant donné
	 *
	 * @param int $pPortletId l'identifiant de la portlet dont on rechercher la position
	 * @return array[]
	 */
	public function getPortletPosition ($pPortletId){
		foreach ($this->_arPortlets as $column=>$arPortletInColumn){
			foreach ($arPortletInColumn as $position => $portlet){
				if(!($portlet instanceof Portlet)){
					array_splice ($this->_arPortlets[$column], $position, 1);
					$this->_reorder($column);
					return $this->getPortletPosition ($pPortletId);
				}
				if ($portlet->getRandomId () == $pPortletId){
					return _ppo (array ('position'=>$position, 'column'=>$column));
				}
			}
		}
		throw new pageException ('Impossible de récupérer la portlet avec un identifiant de '.$pPortletId);
	}

	/**
	 * Déplace la portlet d'identifiant donné dans une autre colonne, à une position indiquée
	 * (si rien n'est indiqué, la place a la dernière position de la colonne)
	 *
	 * @param int    $pPortletId l'identifiant de la portlet a déplacer
	 * @param string $pColumn    le nom de la colonne dans laquelle mettre la portlet
	 * @param int    $pPosition  la position souahitée de la portlet
	 */
	public function movePortlet ($pPortletId, $pColumn, $pPosition = null){
		//on récupère les positions de la portlet
		$position = $this->getPortletPosition ($pPortletId);

		//on récupère la portlet elle même et on la supprime de son ancienne position
		$portletToMove = $this->getPortlet ($pPortletId);
		$this->deletePortlet ($pPortletId);

		//si la colonne de départ et de fin de la portlet est la même, on met a jour
		// la position souhaitée (car les autres portlets ont étés décalées vers le bas)
		if ($pColumn == $position['column']){
			if ($pPosition > $position['position']){
				$pPosition -= 1;
			}
		}

		//Insertion de la portlet a sa nouvelle position
		$this->addPortlet ($portletToMove, $pColumn, $pPosition);
	}

	/**
	 * Récupère la liste des portlets
	 *
	 * @return array
	 */
	public function getPortlets (){
		return $this->_arPortlets;
	}

	/**
	 * Retourne la liste des portlets positionnées dans la variable de template donné
	 *
	 * @param string $pVariableName le nom de la variable dont on souhaite la liste des portlets
	 *
	 * @return array
	 */
	public function getPortletsIn ($pVariableName){
		if (isset ($this->_arPortlets[$pVariableName])){
			return $this->_arPortlets[$pVariableName];
		}
		return array ();
	}

	/**
	 * Définition des modes de comportement pour les portlets
	 *
	 * @param array $pArPortletMode tableau de mode pour les portlets.
	 */
	public function setPortletModes ($pArPortletMode){
		//On a donné null, aucun mode défini, on met tableau vide à la place
		if ($pArPortletMode === null){
			$pArPortletMode = array ();
		}

		//ce n'est pas un tableau ? on ne pourra pas le traiter, exception
		if (!is_array ($pArPortletMode)){
			throw new CopixException ('setPortletModes accepte seulement un tableau en paramètre');
		}

		//Parcour du tableau pour définir le mode d'édition des portlets
		foreach ($pArPortletMode as $portletId=>$mode){
			$this->setPortletMode ($portletId, $mode);
		}
	}

	/**
	 * Définition du mode de comportement pour la portlet donnée
	 *
	 * @param string $pPortletId l'identifiant de la portlet
	 * @param int    $pMode      le mode de comportement
	 *
	 */
	public function setPortletMode ($pPortletId, $pMode){
		//on passe par getPortlet pour lancer une exception si cette dernière n'existe pas dans la page
		$this->_arPortletModes[$this->getPortlet ($pPortletId)->getRandomId ()] = $pMode;
	}

	/**
	 * Supression des états sauvegardés pour les portlets
	 *
	 * @return void
	 */
	public function clearPortletModes (){
		$this->_arPortletModes = array ();
	}

	/**
	 * Récupère le mode de la portlet demandé
	 */
	public function getPortletMode ($pPortletId){
		return isset ($this->_arPortletModes[$pPortletId]) ? $this->_arPortletModes[$pPortletId] : 'default';
	}

	/**
	 *
	 * @param void $pEtat @deprecated
	 */
	public function setEtat ($pEtat){
		$this->setState ($pEtat);
	}

	/**
	 * Gets the state of the page
	 * @return const
	 */
	public function getEtat (){
		return $this->getState ();
	}

	/**
	 * Gets the page state
	 * return const
	 */
	function getState (){
		return $this->_state;
	}

	/**
	 * Sets the state of the page
	 * @param const $pState
	 * @throws PageStateException
	 */
	public function setState ($pState){
		if (($pState !== self::DISPLAY) && ($pState !== self::UPDATE)){
			throw new PageStateException ($pState);
		}
		$this->_state = $pState;
	}

	/**
	 * Méthode créée pour replacer une portlet de type image en diaporama et vice versa
	 * @param Portlet $pPortlet
	 * @param  $pColumn
	 * @param  $pPosition
	 * @return
	 */
	public function refreshPortletType(Portlet $pPortlet){
		if ($this->findPortletById ($pPortlet->getRandomId ()) !== null){
			$position = $this->getPortletPosition ($pPortlet->getRandomId());
			$pColumn = $position['column'];
			$pPosition = $position['position'];
			$this->_arPortlets[$pColumn][$pPosition] = $pPortlet;
		}
	}
	
	/**
	 * Indique si la page peut être mise en cache ou non.
	 * 
	 * @return boolean
	 */
	public function isCachable (){
		//si on peut modifier la page, on refuse de mettre en cache (pour éviter les problèmes 
		//   de barres d'outils)
		if (HeadingElementCredentials::canWrite($this->public_id_hei)){
			return false;
		}

		foreach ($this->_arPortlets as $column=>$arrayPosition){
			foreach ($arrayPosition as $position=>$portlet){
				if (! $portlet->isCachable ()){
					return false;
				}				
			}
		}
		return true;
	}
	
	/**
	 * 
	 * Retourne la liste de tous les éléments utilisés ou référencés dans la page
	 * @param $pStatus si on veut filtrer par statut
	 * @return array
	 */
	public function getListElementsInPage ($pStatus = false){
		$toReturn = array();
		foreach ($this->_arPortlets as $variablePortlet){
			foreach ($variablePortlet as $portlet){
				$arElements = $portlet->getElementsToSave ();
				foreach ($arElements as $element){
					$elementToReturn = _ioClass ('heading|headingelementinformationservices')->get($element->getHeadingElement()->public_id_hei);
					if ($pStatus === false || ($pStatus !== false && $elementToReturn->status_hei == $pStatus)){					
						$toReturn[$element->getHeadingElement()->public_id_hei] = $elementToReturn;
					}
				}
			}
		}
		return $toReturn;
	}
}