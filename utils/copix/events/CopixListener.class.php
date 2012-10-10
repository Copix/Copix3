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
* Classe abstraite pour les listener
* @package		copix
* @subpackage 	event
*/
abstract class CopixListener {
   /**
   * Demande de traitement d'un événement donné
   * @param CopixEvent			$pEvent			l'événement à traiter
   * @param CopixEventResponse	$pEventResponse	la réponse à renseigner
   */
   public function perform ($pEvent, $pEventResponse) {
      $methodName = 'process'.$pEvent->getName ();
      $this->$methodName ($pEvent, $pEventResponse);
   }
}