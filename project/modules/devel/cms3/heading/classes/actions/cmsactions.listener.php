<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Ecoute l'événement cms_action
 * 
 * @package cms
 * @subpackage heading
 */
class ListenerCMSActions extends CopixListener {
	/**
	 * Ecoute l'événement cms_action
	 *
	 * @param CopixEvent $pEvent Evénement
	 * @param CopixEventResponse $pEventResponse Réponse à l'événement
	 */
	public function processCMS_Action ($pEvent, $pEventResponse) {
		$extras = $pEvent->getParams ();
		unset ($extras['message']);
		_log ($pEvent->getParam ('message'), 'cms_action', CopixLog::INFORMATION, $extras);
	}
}