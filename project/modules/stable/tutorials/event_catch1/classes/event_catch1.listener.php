<?php
 /**
 * @package		tutorials
 * @subpackage	event_catch1
 * @author	 	Fersing Estelle
 * @copyright 	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
* Classe interceptant les évènements sans paramètre envoyé par le module event_launch
 * @package		tutorials
 * @subpackage	event_catch1
*/
class ListenerEvent_Catch1 extends CopixListener {

	/*
	 * Cette fonction récupère tous les évènements de nom newEventOnly 
	 * et ajout un nouvel enregistrement dans la base avec la date du jour de réception	 
	 * @param $pEvent : évènement recu avec les éventuels paramètres
	 * @param $pEventRep : évènement en réponse
	 */
	public function processNewEventOnly ($pEvent, $pEventRep) {
		$event = _record ('tutorial_event1');
		// On affecte les valeurs aux champs
		$event->titre = 'Evènement sans paramètre';
		$event->dtcreation = date("Ymd");
		
		// On insert l'enregistrement
		_dao ('tutorial_event1')->insert ($event);
	}
}
?>