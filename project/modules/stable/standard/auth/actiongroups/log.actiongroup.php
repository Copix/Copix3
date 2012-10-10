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
	 * Tente de se logger avec les informations passées dans CopixRequest
	 *
	 * @return boolean
	 */
	private function _logIn () {
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
		if (count ($connected['success']) > 0) {
			CopixEventNotifier::notify ('login', array ('login'=>CopixRequest::get ('login')));
			return true;
		}
		
		return false;
	}

	/**
	 * Login
	 */
	public function processIn (){
		if ($this->_logIn ()) {
			if (CopixConfig::get('auth|authorizeRedirectIfOK')) {
				$urlReturn = CopixRequest::get ('auth_url_return', _url ('log|'));
			} else {
				$urlReturn = _url ('log|');
			}
		} else {
			if (CopixConfig::get('auth|authorizeRedirectIfNoK')) {
				$urlReturn = CopixRequest::get ('auth_failed_url_return', _url ('log|', array ('failed'=>1, 'auth_url_return'=>CopixRequest::get ('auth_url_return', CopixUrl::get ('auth_url_return')))));
			} else {
				$urlReturn = _url ('log|', array ('failed'=>1, 'auth_url_return'=>CopixRequest::get ('auth_url_return', CopixUrl::get ('auth_url_return'))));
			}
		}
		return _arRedirect ($urlReturn);
	}
	
	/**
	 * Tente de se connecter, et renvoie "true" ou "false" uniquement, sans le template principal
	 * Peut tester des droits en même temps, avec des champs testCredential0, testCredential1, etc, et renvoi boolean(connexion)|boolean(testCredential0)|...
	 *
	 * @return CopixActionReturn
	 */
	public function processAjaxIn () {
		$ppo = _ppo ();
		$request = CopixRequest::asArray ();
		$main = ($this->_logIn ()) ? 'true' : 'false';
		if ($this->_logIn ()) {
			foreach ($request as $var => $value) {
				if (substr ($var, 0, 14) == 'testCredential') {
					$main .= (_currentUser ()->testCredential ($value)) ? '|true' : '|false';
				}
			}
		}
		$ppo->MAIN = $main;
		return _arDirectPPO ($ppo, 'generictools|blanknohead.tpl');
	}
	
	/**
	 * Logout
	 */
	public function processOut (){
		foreach (CopixAuth::getCurrentUser ()->getResponses () as $handler => $response) {
			if ($response->getResult ()) {
				_notify ('logout', array ('handler' => $handler, 'login' => $response->getLogin ()));
			}
		}
		_currentUser ()->logout (array ());
		CopixAuth::destroyCurrentUser ();
		return _arRedirect (_request ('auth_url_return', _url ('||')));
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