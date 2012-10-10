<?php
/**
 * @package standard
 * @subpackage admin 
* 
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Actions pour afficher les logs
 * @package standard
 * @subpackage admin  
 */
class ActionGroupLog extends CopixActionGroup {
	/**
	 * Vérifie que l'on est bien administrateur
	 */
	public function beforeAction (){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Page par défaut
	 */
   	public function processDefault(){
   		return $this->processShow ();
   	}

	/**
	 * Page d'affichage des logs
	 */
   	public function processShow(){
	   	$ppo = new CopixPPO ();
	   	$profil = CopixRequest::get ('profile');
	   	
		$ppo->TITLE_PAGE = _i18n ('logs.show');
	   	$ppo->profil  = $profil;
	   	$page = (CopixRequest::get ('page', null) !== null) ? CopixRequest::get ('page', null) : 1;
	   	CopixSession::set ('log|numpage', $page);
	   	
	   	foreach (CopixConfig::instance()->copixlog_getRegistered () as $profil){
	   		$ppo->profils[$profil] = $profil;
	   	}
	   	return _arPPO ($ppo, array ('template'=>'logs.show.php'));
	}
	
	/**
	 * Vide un log donné
	 */
	public function processDelete (){
		CopixLog::deleteProfile ($profil = CopixRequest::get ('profile'));
		_log (_i18n ('logs.action.emptyLog').'['.$profil.']');
		return _arRedirect (_url ('log|show', array ('profile'=>$profil)));
	}
	
	/**
	 * Ecran d'accueil pour l'administration des logs
	 */
	public function processAdmin (){
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('logs.admin');
		$ppo->arRegistered = CopixConfig::instance ()->copixlog_getRegistered ();
		
		return _arPpo ($ppo, 'logs.admin.tpl');
	}
	
	/**
	 * Modification d'un profil de log
	 */
	public function processEdit (){
		if ($profile = CopixRequest::get ('profile')){
			if (!in_array ($profile, CopixConfig::instance ()->copixLog_getRegistered ())){
				return _arRedirect (_url ('admin||'));
			}
			CopixSession::set ('admin|log|edit', CopixConfig::instance ()->copixlog_getProfile ($profile));
		} 

		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = _i18n ('logs.update');
		$ppo->log = CopixSession::get ('admin|log|edit');
		
		if (!isset ($ppo->log['email'])) {
			$ppo->log['email'] = '';
		}
		$ppo->arErrors = array ();
		if (_request ('error') !== null) {
			$ppo->arErrors[] = _i18n ('logs.error.' . _request ('error'));
		}

		$ppo->arStrategies = CopixLog::getStrategies ();
		$ppo->arLevel = CopixLog::getLevels ();

		return _arPpo ($ppo, 'log.update.tpl');
	}
	
	/**
	 * Création d'un profil de log
	 */
	public function processCreate (){
		$profile = CopixRequest::get ('profile', null, true);
		if ($profile === null ) {
			return CopixActionGroup::process ('genericTools|Messages::getError',array ('message'=>CopixI18N::get ('logs.error.noname'), 'back'=>_url('admin|log|admin')));
		}
		//On utilise les fonctions de CopixConfig pour être sur d'avoir un profil complètement initialisé
		CopixConfig::instance ()->copixlog_registerProfile (array ('name'=>$profile, 
				'enabled'=>false));
		$profile = CopixConfig::instance ()->copixlog_getProfile ($profile);
		CopixSession::set ('admin|log|edit', $profile);
		
		return _arRedirect (_url ('log|edit'));
	}
	
	/**
	 * Validation des modification sur le profil de log
	 */
	public function processValid (){
		$profile = CopixSession::get ('admin|log|edit');
		if (CopixRequest::get ('enabled')){
			$profile['enabled'] = true;
		} else {
		    $profile['enabled'] = false;
		}
		
		$profile['strategy'] = CopixRequest::get ('strategy');
		if (CopixRequest::get ('strategy_class', null, true)){
			$profile['strategy'] = CopixRequest::get ('strategy_class'); 			
		}
		$profile['level'] = CopixRequest::get ('level');
		if ($handle = CopixRequest::get ('handle', null, true)){
			if (is_array ($profile['handle'])){
				if (!in_array ($handle, $profile['handle'])){
					$profile['handle'][] = $handle;	
				}
			}else{
				$profile['handle'] = array ($handle);
			}
		}
	    if (!isset ($profile['handleExcept'])) {
		    $profile['handleExcept'] = array ();
		}
		if(!is_array($profile['handle'])){
			if ($handleExcept = CopixRequest::get ('handleExcept', null, true)){
				if (is_array ($profile['handleExcept'])){
					if (!in_array ($handleExcept, $profile['handleExcept'])){
						$profile['handleExcept'][] = $handleExcept;	
					}
				}else{
					$profile['handleExcept'] = array ($handleExcept);
				}
			}
		}else if(is_array($profile['handleExcept'])){
			unset($profile['handleExcept']);
		}
		
		// email de destination dans le cas d'une stratégie de profil de type email
		if ($profile['strategy'] == 'email') {
			$emails = explode (';', _request ('email'));
			
			foreach ($emails as $email) {
				try {
					$email = CopixFormatter::getMail ($email);
				} catch (CopixException $e) {
					return _arRedirect (_url ('log|edit', array ('profile' => $profile['name'], 'error' => 'invalidEMail'))); 
				}
			}
			$profile['email'] = _request ('email');
		}
		
		CopixSession::set ('admin|log|edit', $profile);
		
		if (CopixRequest::get ('save')){
			$profiles = CopixConfig::instance ()->copixlog_getRegisteredProfiles ();
			$profiles[$profile['name']] = $profile;
			_class ('LogConfigurationFile')->write ($profiles);
			CopixSession::set ('admin|log|edit', null);
			if (strtoupper($profile['strategy'])=="FIREBUG"){
				if (! CopixPluginRegistry::isRegistered ("admin|firebug")){
					//on récupère la liste des plugins enregistrés et on ajoute le nouveau plugin
					$arPlugins = CopixConfig::instance ()->plugin_getRegistered ();
					$arPlugins[] = "admin|firebug";
					//écriture du fichier
					_class ('PluginsConfigurationFile')->write ($arPlugins);
				}
			}
			
			return _arRedirect (_url ('log|admin'));
		}else{
			return _arRedirect (_url ('log|edit'));
		}
	}
	
	/**
	 * Supression d'un handler pour le profil en cours de modification
	 */
	public function processRemoveHandle (){
		if ($handleToRemove = CopixRequest::get ('handle')){
			if ($profile = CopixSession::get ('admin|log|edit')){
				$handle = $profile['handle'];
				if (is_array ($handle)){
					$newHandle = array ();
					foreach ($handle as $element){
						if ($element != $handleToRemove){
							$newHandle[] = $element;
						}
					}
					if (count ($newHandle) == 0){
						$newHandle = 'all';
			 		}
				}
				
				$profile['handle'] = $newHandle;
				CopixSession::set ('admin|log|edit', $profile);
			}
		}
		return _arRedirect (_url ('log|edit'));
	}
	/**
	 * Supression d'un handleExcept pour le profil en cours de modification
	 */
	public function processRemoveHandleExcept (){
		if ($handleExceptToRemove = CopixRequest::get ('handleExcept')){
			if ($profile = CopixSession::get ('admin|log|edit')){
				unset($profile['handleExcept'][array_search($handleExceptToRemove, $profile['handleExcept'])]);
				CopixSession::set ('admin|log|edit', $profile);
			}
		}
		return _arRedirect (_url ('log|edit'));
	}
	
	
	/**
	 * Supression d'un log
	 */
	public function processDeleteProfile (){
		$profile = CopixRequest::get ('profile');
		
		if (CopixRequest::getInt ('confirm') == 1){
			CopixLog::deleteProfile ($profile);
			$profiles = CopixConfig::instance ()->copixlog_getRegisteredProfiles ();
			unset ($profiles[$profile]);
			_class ('LogConfigurationFile')->write ($profiles);
			return _arRedirect (_url ('log|admin'));
		}else{
			if (!in_array ($profile, CopixConfig::instance ()->copixlog_getRegistered ())){
				return _arRedirect (_url ('log|admin'));
			}
			return CopixActionGroup::process ('generictools|Messages::getConfirm', 
				array ('message'=>_i18n ('logs.delete', $profile),
						'confirm'=>_url ('admin|log|deleteProfile', array ('profile'=>$profile, 'confirm'=>1)),
						'cancel'=>_url ('admin|log|admin')));
		}
	}
}
?>