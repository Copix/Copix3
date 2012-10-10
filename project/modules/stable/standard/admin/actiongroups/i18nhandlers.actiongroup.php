<?php
/**
 * @package standard
 * @subpackage admin
 * @author Duboeuf Damien
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Gestion des handler d'internationalisation
 * 
 * @package standard
 * @subpackage admin 
 */
class ActionGroupI18nHandlers extends CopixActionGroup {
	
	/**
	 * Executé avant toute action
	 *
	 * @param string $pActionName Nom de l'action
	 */
	public function beforeAction ($pActionName) {
		CopixPage::add ()->setIsAdmin (true);
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Affiche la liste des handler d'i18n disponibles
	 * 
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		
		$ppo = _rPPO (array ('TITLE_PAGE'=>_i18n ('i18n.adminI18nHandlers')));
		
		//On lance la récupération depuis les module.xml
		$handlers = CopixModule::getParsedModuleInformation ('copix|i18nhandlers','/moduledefinition/i18nhandlers/i18nhandler', array ('CopixI18nParserHandler', 'parsei18nHandler'));
		$configurationFile = _ioClass ('admin|i18nConfigurationFile');
		$configurationFile->isWritable ();//Crée le fichier si il n'existe pas
		$activeHandler = $configurationFile->get();
		$ppo->handlers = array ();
		
		//On tri les handlers activer
		foreach ($handlers as $key=>$handler) {
			$ppo->handlers[$key] = $handler;
			$ppo->handlers[$key]['active'] = false;
			if (isset ($activeHandler[$key])) {
				$ppo->handlers[$key]['active'] = true;
			}
		}
		
		return _arPpo ($ppo, 'i18nhandlers/handlers.list.tpl');
	}
	
	
	/**
	 * Sauvegarde les handlers dans le fichier de configuration
	 *
	 * @return CopixActionReturn
	 */
	function processSaveHandlers () {
		$handlers = CopixModule::getParsedModuleInformation ('copix|i18nhandlers','/moduledefinition/i18nhandlers/i18nhandler', array ('CopixI18nParserHandler', 'parsei18nHandler'));
		
		$activeHandler = array ();
		$order = array();
		foreach (_request ('handlers', array ()) as $handler) {
			if (isset ($handlers[$handler])){
				$activeHandler[$handler] = $handlers[$handler];
				$order[$handler] = $handlers[$handler]['order'];
			}
		}
		
		array_multisort ($order, $activeHandler);
		
		$configurationFile = _ioClass ('admin|i18nConfigurationFile');
		$configurationFile->write ($activeHandler);
		return _arRedirect (_url ('admin||'));
	}
	
}