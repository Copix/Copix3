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