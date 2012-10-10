<?php
/**
 * @package standard
 * @subpackage admin 
 * 
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Actions de configuration des plugins
 * @package standard
 * @subpackage admin 
 */
class ActionGroupPlugin extends CopixActionGroup {
	/**
	 * Seul l'administrateur du site peut effectuer ce genre de choses.
	 */
	public function beforeAction (){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
	
	/**
	 * Affichage de la liste des plugins disponibles
	 */
	public function processDefault (){
		$ppo = new CopixPPO ();
		$arPlugins = array ();

		$ppo->TITLE_PAGE = _i18n ('install.plugins.title');
		foreach (CopixPluginRegistry::getAvailable () as $pluginName){
			$arPlugins[] = array ('name'=>$pluginName, 'enabled'=>CopixPluginRegistry::isRegistered ($pluginName));
		}
		$ppo->arPlugins = $arPlugins;
		return _arPpo ($ppo, 'plugins.list.tpl');
	}
	
	/**
	 * Ajout d'un plugin dans la liste
	 */
	public function processAddPlugin (){
		//on vérifie que le plugin demandé existe bien dans la liste des plugins disponibles
		if (in_array ($pluginName = CopixRequest::get ('plugin'), CopixPluginregistry::getAvailable ())){
			//si le plugin n'est pas déjà activé, on l'active
			if (! CopixPluginRegistry::isRegistered ($pluginName)){
				//on récupère la liste des plugins enregistrés et on ajoute le nouveau plugin
				$arPlugins = CopixConfig::instance ()->plugin_getRegistered ();
				$arPlugins[] = $pluginName;
				//écriture du fichier
				_class ('PluginsConfigurationFile')->write ($arPlugins);
			}
		}
		//création du tableau des plugins à enregistrer plus le plugin en question
		return _arRedirect (_url ('plugin|'));
	}
	
	/**
	 * Supression d'un plugin de la liste des plugins enregistrés
	 */
	public function processRemovePlugin (){
		//on regarde si le plugin fait parti des plugins déja enregistrés
		$arPluginsConfiguration = array ();
		if (in_array ($pluginName = CopixRequest::get ('plugin'), $arPlugins = CopixConfig::instance ()->plugin_getRegistered ())){
			foreach ($arPlugins as $plugin){
				if ($plugin != $pluginName){
					$arPluginsConfiguration[] = $plugin;
				}
			}
			_class ('PluginsConfigurationFile')->write ($arPluginsConfiguration);
		}
		return _arRedirect (_url ('plugin|'));
	}
}
?>