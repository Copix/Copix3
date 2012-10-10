<?php
 /**
 * @package		tutorials
 * @subpackage	event_catch2bis
 * @author	 	Fersing Estelle
 * @copyright 	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

 /**
 * Autre listener, copie de ListenerEvent_Catch2 pour valider la double déclaration des listeners. 
 * 
 * @package		tutorials
 * @subpackage	event_catch2bis
 */
class ListenerEvent_Catch2bis extends CopixListener {
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
		$event->titre = '2B - NewEventOnly';
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
		$event->titre = '2B - newEvent + param';
		$event->dtcreation = date("Ymd");
		$event->information = $pEvent->getParam ('information');
		
		// On insert l'enregistrement
		_dao ('tutorial_event2')->insert ($event);
	}
}
?>