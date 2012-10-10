<?php
/**
 * @package standard
 * @subpackage admin
 * @author Gérald Croës, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Administration des plugins
 *
 * @package standard
 * @subpackage admin 
 */
class ActionGroupPlugin extends CopixActionGroup {
	/**
	 * Executée avant toute action
	 *
	 * @param string $pActionName Nom de l'action
	 */
	protected function _beforeAction ($pActionName) {
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
	
	/**
	 * Liste des plugins
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_PLUGIN_LIST);
		$plugins = array ();
		foreach (CopixPluginRegistry::getAvailable () as $pluginName) {
			$plugins[] = CopixPluginRegistry::getDescription ($pluginName);
		}

		// tri
		if (($order = _request ('order')) != null) {
			CopixSession::set ('plugin|list|order', $order, 'admin');
		}
		$ppo->order = CopixSession::get ('plugin|list|order', 'admin', 'id_asc');
		$unsorted = array ();
		list ($orderName, $orderType) = explode ('_', $ppo->order);
		foreach ($plugins as $plugin) {
			switch ($orderName) {
				case 'caption' :
					$key = $plugin->getCaption ();
					break;
				case 'id' :
					$key = $plugin->getId ();
					break;
				case 'module' :
					$key = $plugin->getModule () . '_' . $plugin->getCaption ();
					break;
				default :
					throw new ModuleAdminException (_i18n ('commons.error.invalidOrder', $ppo->order), ModuleAdminException::PLUGIN_INVALID_ORDER);
					break;
			}
			$unsorted[$key] = $plugin;
		}
		if ($orderType == 'asc') {
			ksort ($unsorted);
		} else {
			krsort ($unsorted);
		}
		// utilisation de array_values pour ne pas garder les clefs de $unsorted, générées pour trier facilement
		$ppo->plugins = array_values ($unsorted);
		$ppo->highlight = _request ('highlight');

		return _arPPO ($ppo, 'plugin/list.php');
	}
	
	/**
	 * Activation d'un plugin
	 *
	 * @return CopixActionReturn
	 */
	public function processEnable () {
		$plugin = _request ('plugin');
		ToolsAdmin::setPage (ToolsAdmin::PAGE_PLUGIN_EDIT, array ('plugin' => substr ($plugin, strpos ($plugin, '|') + 1)));
		CopixPluginConfigFile::enable ($plugin);
		return _arRedirect (_url ('admin|plugin|', array ('highlight' => $plugin)));
	}
	
	/**
	 * Désactivation d'un plugin
	 *
	 * @return CopixActionReturn
	 */
	public function processDisable () {
		$plugin = _request ('plugin');
		ToolsAdmin::setPage (ToolsAdmin::PAGE_PLUGIN_EDIT, array ('plugin' => substr ($plugin, strpos ($plugin, '|') + 1)));
		CopixPluginConfigFile::disable ($plugin);
		return _arRedirect (_url ('admin|plugin|', array ('highlight' => $plugin)));
	}

	/**
	 * Affiche la configuration du plugin
	 *
	 * @return CopixActionReturn
	 */
	public function processInformations () {
		$plugin = _request ('plugin');
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_PLUGIN_INFORMATIONS, array ('plugin' => substr ($plugin, strpos ($plugin, '|') + 1)));
		$ppo->plugin = CopixPluginRegistry::getDescription ($plugin);
		
		// recherche de la config
		$ppo->config = CopixPluginRegistry::getConfig ($plugin);
		// getConfig peut retourner null
		if (!is_object ($ppo->config)) {
			$ppo->config = array ();
		}
		return _arPPO ($ppo, 'plugin/informations.php');
	}
}