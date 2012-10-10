<?php
/**
 * @package standard
 * @subpackage auth 
 * 
 * @author		Julien Salleyron
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Actiongroup contenant la parti administration des userhandler, grouphandler et credentialhandler
 * @package standard
 * @subpackage auth
 */
class ActionGroupAdmin extends CopixActionGroup {
	
	/**
	 * On protège la page avec les droits d'administration
	 */
	public function beforeAction ($pActionName){
		//On souhaite, pour éviter tout problème, tester explicitement les droits.
		//Cette volontée de tester explicitement les droits est pour éviter tout problème
		//de conflits avec les écrans de modification des handlers
		CopixConfig::instance ()->copixauth_cache = false;
		_currentUser ()->assertCredential ('basic:admin');
	}
	
	/**
	 * Permet de gérer les userHandler
	 *
	 * @return ActionReturn
	 */
	public function processUserHandlers () {
		return $this->_handlers ('user', _i18n ('auth.adminUserHandlers'));
	}
	
	/**
	 * Permet de gérer les groupHandler
	 *
	 * @return ActionReturn
	 */
	public function processGroupHandlers () {
		return $this->_handlers ('group', _i18n ('auth.adminGroupHandlers'));
	}
	
	/**
	 * Permet de gérer les credentialHandler
	 *
	 * @return ActionReturn
	 */
	public function processCredentialHandlers () {
		return $this->_handlers ('credential', _i18n ('auth.adminCredentialHandlers'));
	}
	
	/**
	 * Méthode qui gère réellement les handlers
	 *
	 * @param string $pType le type de handler que l'on gère
	 * @param strng $pTitle le title de la page
	 * @return ActionReturn
	 */
	private function _handlers ($pType, $pTitle){
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = $pTitle;
		
		//On lance la récupération depuis les module.xml
		$handlers = CopixModule::getParsedModuleInformation ('copix|'.$pType.'handlers','/moduledefinition/'.$pType.'handlers/'.$pType.'handler', array ('CopixAuthParserHandler', 'parse'.$pType.'Handler'));
		
		//Instanciation de l'objet qui récupère la config dans le fichier
		_classInclude ('auth|useConfigurationFile');
		$configurationFile = new useConfigurationFile($pType);
		$activeHandler = $configurationFile->get();
		$ppo->handlers = array ();
		
		//On tri les handlers activer
		foreach ($handlers as $key=>$handler) {
			$ppo->handlers[$key] = false;
			if (in_array($key, $activeHandler)) {
				$ppo->handlers[$key] = true;
			}
		}
		$ppo->type       = $pType;
		return _arPpo ($ppo, 'handlers.list.tpl');
	}
	
	/**
	 * Sauvegarde les handlers dans le fichier de configuration
	 *
	 * @return ActionReturn
	 */
	function processSaveHandlers () {
		CopixRequest::assert('type');
		$activeHandler = array ();
		foreach (_request ('handlers', array ()) as $handler) {
			$activeHandler[] = $handler;
		}

		_classInclude ('auth|useConfigurationFile');
		$configurationFile = new useConfigurationFile (_request ('type'));
		$configurationFile->write ($activeHandler);
		return _arRedirect (_url ('admin||'));
	}
}
?>