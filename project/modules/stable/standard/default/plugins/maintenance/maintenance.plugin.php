<?php
/**
 * @package standard
 * @subpackage default
 * @author Stevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Bloque l'accès au site à tous les utilisateurs non admin
 *
 * @package standard
 * @subpackage default
 */
class PluginMaintenance extends CopixPlugin implements ICopixBeforeProcessPlugin, ICopixAfterProcessPlugin {
	/**
	 * Retourne le libellé
	 *
	 * @return string
	 */
	public function getCaption () {
		return 'Maintenance du site';
	}

	/**
	 * Retourne la description
	 *
	 * @return string
	 */
	public function getDescription () {
		return 'Bloque l\'accès au site à tous les utilisateurs non admin';
	}

	/**
	 * Indique si le mode maintenance est activé
	 *
	 * @return boolean
	 */
	private function _isEnabled () {
		return CopixConfig::get ('default|maintenance');
	}

	/**
	 * Indique si l'utilisateur courant est admin, ou a mit le bon paramètre pour passer outre le mode maintenance
	 *
	 * @return boolean
	 */
	private function _isAdmin () {
		return _currentUser ()->testCredential ('basic:admin') || CopixSession::get ('maintenance', 'default|maintenance') == CopixConfig::get ('default|maintenanceCancel');
	}

	/**
	 * Appelée avant le process d'une action
	 *
	 * @param CopixPPO $pAction Nom de l'action
	 */
	public function beforeProcess (&$pAction) {
		if (_request ('maintenance') == CopixConfig::get ('default|maintenanceCancel')) {
			CopixSession::set ('maintenance', _request ('maintenance'), 'default|maintenance');
		}

		if ($this->_isEnabled ()) {
			if ($this->_isAdmin ()) {
				return ;
			}
			return _arPPO (_ppo (), array ('template' => 'generictools|blank.tpl', 'mainTemplate' => 'maintenancefront.php'));
		}
	}

	/**
	 * Appelée avant l'affichage, pour changer le thème
	 *
	 * @return CopixActionReturn
	 */
	public function afterProcess ($pActionReturn) {
		if ($this->_isEnabled () && $this->_isAdmin () && !CopixRequest::isAJAX ()) {
			if (is_array ($pActionReturn->more)) {
				$pActionReturn->more['mainTemplate'] = 'default|maintenance.php';
			} else {
				$pActionReturn->more = array ('template' => $pActionReturn->more, 'mainTemplate' => 'default|maintenance.php');
			}
			return $pActionReturn;
		}
	}
}