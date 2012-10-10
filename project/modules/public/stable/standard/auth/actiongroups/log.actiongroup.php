<?php
/**
 * @package standard
 * @subpackage auth
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * OpÃ©rations de connexions / déconnexion
 * @package standard
 * @subpackage auth
 */
class ActionGroupLog extends CopixActionGroup {
	/**
	 * Action par défaut.... logique
	 */
	public function processDefault (){
		return $this->processForm ();		
	}

	/**
	 * Login
	 */
	public function processIn (){
		CopixRequest::assert ('login', 'password');
		$noCredential = _request ('noCredential', false);
		
		$config = CopixConfig::instance();
		if ($noCredential && count ($config->copixauth_getRegisteredUserHandlers()) > 1 && CopixConfig::get('auth|multipleConnectionHandler')) {
			$connected = CopixAuth::getCurrentUser ()->login (array ('login'=>CopixRequest::get ('login'),
			                                                         'password'=>CopixRequest::get ('password'),
			                                                         'append'=>true));
		} else {
			$connected = CopixAuth::getCurrentUser ()->login (array ('login'=>CopixRequest::get ('login'),
			                                                         'password'=>CopixRequest::get ('password')));
		}
		if ($connected){
			//insert token for remember_me plugin
			$response = CopixAuth::getCurrentUser()->getResponses();
			foreach ($response as $key=>$r){
				if(($r instanceof CopixUserLogResponse) && $r->getResult ()){
					$handlername = $key;
				}
			}

			CopixEventNotifier::notify ('login', array ('login'=>CopixRequest::get ('login')));
			if (CopixConfig::get('auth|authorizeRedirectIfOK')) {
				$urlReturn = CopixRequest::get ('auth_url_return', _url ('log|'));
			} else {
				$urlReturn = _url ('log|');
			}
			return _arRedirect ($urlReturn);
		}
		if (CopixConfig::get('auth|authorizeRedirectIfNoK')) {
			$urlReturn = CopixRequest::get ('auth_failed_url_return', _url ('log|', array ('failed'=>1, 'auth_url_return'=>CopixRequest::get ('auth_url_return', CopixUrl::get ('auth_url_return')))));
		} else {
			$urlReturn = _url ('log|', array ('failed'=>1, 'auth_url_return'=>CopixRequest::get ('auth_url_return', CopixUrl::get ('auth_url_return'))));
		}
		
		return _arRedirect ($urlReturn);
	}

	/**
	 * Logout
	 */
	public function processOut (){
		CopixAuth::getCurrentUser ()->logout (array ());
		CopixEventNotifier::notify ('logout', array ('login'=>CopixAuth::getCurrentUser()->getLogin ()));
		CopixAuth::destroyCurrentUser ();
		return _arRedirect (CopixRequest::get ('auth_url_return', _url ('||')));
	}

	/**
	 * Ecran de connexion
	 */
	public function processForm (){
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('auth.connect');
		if (CopixAuth::getCurrentUser ()->isConnected ()){
			$ppo->user = CopixAuth::getCurrentUser ();
		}
		
		$config = CopixConfig::instance();
		if (count ($config->copixauth_getRegisteredUserHandlers()) > 1 && CopixConfig::get('auth|multipleConnectionHandler')) {
			$ppo->noCredential = true;
		}
		
		$ppo->auth_url_return = CopixRequest::get ('auth_url_return', _url ('#'));
		$ppo->failed = array ();
		if (CopixRequest::getInt ('noCredential', 0)){
			$ppo->failed[] = _i18n ('auth.error.noCredentials');
		}
		if (CopixRequest::getInt ('failed', 0)){
			$ppo->failed[] = _i18n ('auth.error.failedLogin');
		}
		
		$ppo->createUser = Copixconfig::get('auth|createUser');
		return _arPPO ($ppo, 'login.form.php');
	}
}
?>