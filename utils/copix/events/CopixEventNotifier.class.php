<?php
/**
* @package		copix
* @subpackage 	event
* @author		Croës Gérald, Patrice Ferlet
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Représentation d'un événement
* @package		copix
* @subpackage	event
*/
class CopixEvent {
   /**
   * Nom de l'événement
   * @var string name
   */
   private $_name = null;

   /**
   * Les paramètres de l'événement
   * @var array
   */
   private $_params = null;

   /**
   * Constructeur
   * @param	string	$pName		le nom de l'événement
   * @param	array	$pParams	les paramètres passés à l'événement
   */
   public function __construct ($pName, $pParams = array()){
      $this->_name   = $pName;
      $this->_params = $pParams;
   }

   /**
   * Retourne le nom de l'événement
   * @return string
   */
   public function getName (){
      return $this->_name;
   }

   /**
   * Récupère la valeur d'un paramètre passé à l'événement
   * @param	string $pName	le nom du paramètre dont on souhaites récupérer le paramètre.
   * @return mixed	la valeur du paramètre
   */
   public function getParam ($pName){
      return isset ($this->_params[$pName]) ? $this->_params[$pName] : null; 
   }
}

/**
* Classe qui représente une réponse à un événement.
* @package		copix
* @subpackage	event
*/
class CopixEventResponse {
   /**
    * Liste des réponses reçues
    * @var array
    */
	var $_responses = array ();

   /**
   * Ajoute une réponse à la liste
   * <code>
   *   //dans un listener
   *   $response->add (array ('element1'=>'valeur1', 'element2'=>'valeur2'));
   * </code>
   * @param	array $response un élément de réponse
   */
   public function add ($response) {
      $this->_responses[] = $response;
   }

   /**
    * Indique s'il existe un élément du nom recherché dans les réponses et qui dispose d'une valeur donnée
    * <code>
    *    CopixDB::begin ();
    *    //lance une requête de mise à jour
    *    //indique que l'on a lancé la requête aux autres modules (pour qu'ils puissent traiter les données)
    *    $response = CopixEventNotifier::notify (new CopixEvent ('SomeEvent', array ('param'=>$param)));
    *    if ($response->inResponse ('failed', true)){
    *       //un des modules nous indique ne pas être arrivé à traiter sa part de responsabilité
    *       CopixDB::rollback ();
    *    }else{
    *       //tout c'est bien passé
    *       CopixDB::commit ();
    *    } 
    * </code>
    * @param	string	$pResponseName	l'élément que l'on recherche dans les réponses
    * @param	mixed	$pValue			la valeur de l'élément que l'on veut tester
    * @param 	array	$pResponse		liste des réponses ou l'on a trouvé la correspondance
    * @return boolean si l'on a trouvé l'élément ou non
    */
   public function inResponse ($pResponseName, $pValue, & $pResponse){
      $founded  = false;
      $pResponse = array ();
      foreach ($this->_responses as $key=>$listenerResponse){
         if (isset ($listenerResponse[$pResponseName]) && $listenerResponse[$pResponseName] == $pValue){
            $founded = true;
            $pResponse[] = $this->_responses[$key];
         }
      }
      return $founded;
   }

   /**
   * Récupère la liste des réponses retournées
   * @return	array	 la liste des réponses
   */
   public function getResponse () {
      return $this->_responses;
   }
}

/**
 * Classe qui permet de notifier des événements
 * @package		copix
 * @subpackage	core
 */
class CopixEventNotifier {
   /**
   * Liste des listener chargés
   * @var array
   */
   private $_listeners = array ();
   
   /**
    * Singleton
    * @var CopixEventNotifier
    */
   private static $_instance = false;

   /**
   * Notification d'un événement
   * <code>
   *    CopixEventNotifier::notify ('eventName');
   *    //ou
   *    CopixEventNotifier:notify ('eventName', array ('param1'=>'value1'));
   *    //ou
   *    CopixEventNotifier::notify (new CopixEvent ('eventName', array ('param1'=>'value1')));
   *    //ou
   *    _notify ('eventName', array ('param1'=>'value1')); 
   * </code>
   * @param		CopixEvent/string	$event 		l'événement lancé (ou le nom de l'événement)
   * @param		array				$pParams	les paramètres passés à l'événement
   * @return 	CopixEventResponse	la réponse de l'événement
   */
   public static function notify ($pEvent, $pParams=array()) {
      //si on a passé une chaine de caractère, création de l'événement
      if (is_string ($pEvent)){
          $pEvent = new CopixEvent($pEvent, $pParams);
      }
      return CopixEventNotifier::instance ()->_dispatch ($pEvent);
   }

   /**
   * Singleton
   * @return CopixEventNotifier
   */
   public static function instance () {
      if (self::$_instance === false) {
         self::$_instance = new CopixEventNotifier ();
      }
      return self::$_instance;
   }

   /**
   * 
   * Dispatch l'événement à tous les listeners concernés
   * @param		CopixEvent	l'événement à traiter
   * @return	CopixEventResponse
   */
   private function _dispatch ($event) {
      $response = new CopixEventResponse ();
      $this->_load ($event);
      $name = $event->getName ();
      if (isset ($this->_listeners[$name])){
         foreach ($this->_listeners[$name] as $listener) {
            $listener->perform ($event, $response);
         }
      }
      return $response;
   }

   /**
   * Chargement des listeners qui réagissent à un événement donné
   * @param	CopixEvent	$event	l'événement pour lequel on souhaites charger les listener
   */
   private function _load ($event) {
      $this->_listeners[$event->getName ()] = CopixListenerFactory::createFor ($event->getName ());
   }
}
?>