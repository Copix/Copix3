<?php
/**
 * @package copix
 * @subpackage events
 * @author Croës Gérald, Patrice Ferlet
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Représentation d'un événement
 * 
 * @package copix
 * @subpackage events
 */
class CopixEvent {
	/**
	 * Nom de l'événement
	 * 
	 * @var string
	 */
	private $_name = null;

	/**
	 * Paramètres de l'événement
	 * 
	 * @var array
	 */
	private $_params = null;

	/**
	 * Constructeur
	 * 
	 * @param string $pName Nom de l'événement
	 * @param array $pParams Paramètres passés à l'événement
	 */
	public function __construct ($pName, $pParams = array ()) {
		$this->_name = $pName;
		$this->_params = $pParams;
	}

	/**
	 * Retourne le nom de l'événement
	 * 
	 * @return string
	 */
	public function getName () {
		return $this->_name;
	}

	/**
	 * Retourne la valeur d'un paramètre passé à l'événement
	 * 
	 * @param string $pName Nom du paramètre dont on souhaites récupérer la valeur
	 * @return mixed
	 */
	public function getParam ($pName, $pDefaultValue = null) {
		return (isset ($this->_params[$pName])) ? $this->_params[$pName] : $pDefaultValue;
	}
}

/**
 * Représente une réponse à un événement
 * 
 * @package copix
 * @subpackage events
 */
class CopixEventResponse {
	/**
	 * Liste des réponses reçues
	 * 
	 * @var array
	 */
	public $_responses = array ();

	/**
	 * Ajoute une réponse à la liste
	 *
	 * @param array $pResponse Element de réponse. Exemple : array ('element1' => 'valeur1', 'element2' => 'valeur2')
	 */
	public function add ($pResponse) {
		$this->_responses[] = $pResponse;
	}

	/**
	 * Indique s'il existe un élément du nom recherché dans les réponses et qui dispose d'une valeur donnée
	 * <code>
	 * CopixDB::begin ();
	 * //lance une requête de mise à jour
	 * //indique que l'on a lancé la requête aux autres modules (pour qu'ils puissent traiter les données)
	 * $response = CopixEventNotifier::notify (new CopixEvent ('SomeEvent', array ('param'=>$param)));
	 * if ($response->inResponse ('failed', true)){
	 *     //un des modules nous indique ne pas être arrivé à traiter sa part de responsabilité
	 *     CopixDB::rollback ();
	 * } else {
	 *     //tout s'est bien passé
	 *     CopixDB::commit ();
	 * } 
	 * </code>
	 * 
	 * @param string $pResponseName Elément que l'on recherche dans les réponses
	 * @param mixed $pValue Valeur de l'élément que l'on veut tester
	 * @param array $pResponse Liste des réponses où l'on a trouvé la correspondance
	 * @return boolean Si l'on a trouvé l'élément ou non
	 */
	public function inResponse ($pResponseName, $pValue, &$pResponse) {
		$founded  = false;
		$pResponse = array ();
		foreach ($this->_responses as $key => $listenerResponse) {
			if (isset ($listenerResponse[$pResponseName]) && $listenerResponse[$pResponseName] == $pValue) {
				$founded = true;
				$pResponse[] = $this->_responses[$key];
			}
		}
		return $founded;
	}

	/**
	 * Récupère la liste de toutes les réponses retournées
	 * 
	 * @return array
	 */
	public function getResponse () {
		return $this->_responses;
	}
}

/**
 * Permet de notifier des événements
 * 
 * @package copix
 * @subpackage events
 */
class CopixEventNotifier {
	/**
	 * Liste des listener chargés
	 * 
	 * @var array
	 */
	private $_listeners = array ();
	
	/**
	 * Singleton
	 * 
	 * @var CopixEventNotifier
	 */
	private static $_instance = false;

	/**
	 * Notification d'un événement
	 * <code>
	 * CopixEventNotifier::notify ('eventName');
	 * //ou
	 * CopixEventNotifier:notify ('eventName', array ('param1'=>'value1'));
	 * //ou
	 * CopixEventNotifier::notify (new CopixEvent ('eventName', array ('param1'=>'value1')));
	 * //ou
	 * notify ('eventName', array ('param1'=>'value1')); 
	 * </code>
	 * 
	 * @param CopixEvent/string $pEvent Evénement lancé (ou le nom de l'événement)
	 * @param array $pParams Paramètres passés à l'événement
	 * @return CopixEventResponse	Réponse de l'événement
	 */
	public static function notify ($pEvent, $pParams = array ()) {
		//si on a passé une chaine de caractère, création de l'événement
		if (is_string ($pEvent)) {
			 $pEvent = new CopixEvent ($pEvent, $pParams);
		}
		return CopixEventNotifier::instance ()->_dispatch ($pEvent);
	}

	/**
	 * Retourne le singleton
	 * 
	 * @return CopixEventNotifier
	 */
	public static function instance () {
		if (self::$_instance === false) {
			self::$_instance = new CopixEventNotifier ();
		}
		return self::$_instance;
	}

	/**
	 * Dispatch l'événement à tous les listeners concernés
	 * 
	 * @param CopixEvent $pEvent Evénement à traiter
	 * @return CopixEventResponse
	 */
	private function _dispatch ($pEvent) {
		$response = new CopixEventResponse ();
		$this->_load ($pEvent);
		$name = $pEvent->getName ();
		if (isset ($this->_listeners[$name])) {
			foreach ($this->_listeners[$name] as $listener) {
				$listener->perform ($pEvent, $response);
			}
		}
		return $response;
	}

	/**
	 * Chargement des listeners qui réagissent à un événement donné
	 * 
	 * @param CopixEvent $pEvent Evénement pour lequel on souhaites charger les listener
	 */
	private function _load ($pEvent) {
		$this->_listeners[$pEvent->getName ()] = CopixListenerFactory::createFor ($pEvent->getName ());
	}
}