<?php
 /**
 * @package		tutorials
 * @subpackage	event_catch2
 * @author	 	Fersing Estelle
 * @copyright 	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

 /**
 * Classe interceptant les évènements avec ou sans paramètre envoyé par le module event_launch
 * @package		tutorials
 * @subpackage	event_catch2
 */
class ListenerEvent_Catch2 extends CopixListener {

	/*
	 * Cette fonction récupère tous les évènements de nom newEventOnly 
	 * et ajout un nouvel enregistrement dans la base avec la date du jour de réception
	 * ainsi qu'un champ information indiquant que cet évènement n'avait pas de paramètre
	 * @param $pEvent : évènement recu avec les éventuels paramètres
	 * @param $pEventRep : évènement en réponse
	 */
	public function processNewEventOnly ($pEvent, $pEventRep) {
		$event = _record ('tutorial_event2');
		// On affecte les valeurs aux champs
		$event->titre = 'Evènement sans paramètre';
		$event->dtcreation = date("Ymd");
		$event->information = 'Pas de paramètre';
		
		// On insert l'enregistrement
		_dao ('tutorial_event2')->insert ($event);
	}
	
	/*
	 * Cette fonction récupère tous les évènements de nom newEventOnly 
	 * et ajout un nouvel enregistrement dans la base avec la date du jour de réception
	 * et un champ information contenant le paramètre information envoyé par l'évènement
	 * @param $pEvent : évènement recu avec les éventuels paramètres
	 * @param $pEventRep : évènement en réponse
	 */
	public function processNewEvent ($pEvent, $pEventRep) {
		$event = _record ('tutorial_event2');
		// On affecte les valeurs aux champs
		$event->titre = 'Evènement avec paramètre';
		$event->dtcreation = date("Ymd");
		$event->information = $pEvent->getParam ('information');
		
		// On insert l'enregistrement
		_dao ('tutorial_event2')->insert ($event);
	}
}
?>