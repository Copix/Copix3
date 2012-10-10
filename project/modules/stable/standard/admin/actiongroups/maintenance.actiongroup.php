<?php
/**
 * @package standard
 * @subpackage admin
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion du mode maintenance
 *
 * @package standard
 * @subpackage admin
 */
class ActionGroupMaintenance extends CopixActionGroup {
	/**
	 * Appelée avant toute action
	 *
	 * @param string $pActionName Nom de l'action
	 */
	protected function _beforeAction ($pActionName) {
		CopixPage::add ()->setIsAdmin (true);
		_currentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Affiche l'état du mode maintenance
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$ppo = new CopixPPO (array ('TITLE_PAGE' => _i18n ('maintenance.default.title')));
		$template = (CopixConfig::get ('default|maintenance')) ? 'disable.php' : 'enable.php';
		$ppo->maintenanceParam = CopixConfig::get ('default|maintenanceCancel');
		return _arPPO ($ppo, 'maintenance/' . $template);
	}

	/**
	 * Active le mode maintenance
	 *
	 * @return CopixActionReturn
	 */
	public function processEnable () {
		CopixConfig::set ('default|maintenance', true);
		CopixPluginConfigFile::enable ('default|maintenance');
		return _arRedirect (_url ('admin||'));
	}

	/**
	 * Désactive le mode maintenance
	 *
	 * @return CopixActionReturn
	 */
	public function processDisable () {
		CopixConfig::set ('default|maintenance', false);
		CopixPluginConfigFile::disable ('default|maintenance');
		return _arRedirect (_url ('admin||'));
	}

	/**
	 * Affiche la page de maintenance
	 *
	 * @return CopixActionReturn
	 */
	public function processShowFrontMaintenance () {
		return _arPPO (_ppo (), array ('template' => 'generictools|blank.tpl', 'mainTemplate' => 'default|maintenancefront.php'));
	}

	/**
	 * Affiche le template de maintenance en mode admin
	 *
	 * @return CopixActionReturn
	 */
	public function processShowAdminMaintenance () {
		return _arPPO (_ppo (array ('MAIN' => '--- Contenu de la page demandée ---')), array ('template' => 'generictools|blank.tpl', 'mainTemplate' => 'default|maintenance.php'));
	}
}