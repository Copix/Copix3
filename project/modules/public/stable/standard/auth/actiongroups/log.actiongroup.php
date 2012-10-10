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
 * Opérations de connexions / déconnexion
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
		if (CopixAuth::getCurrentUser ()->login (array ('login'=>CopixRequest::get ('login'),
													'password'=>CopixRequest::get ('password')))){
			CopixEventNotifier::notify ('login', array ('login'=>CopixRequest::get ('login')));
			return _arRedirect (CopixRequest::get ('auth_url_return', _url ('log|')));
		}
		return _arRedirect (_url ('log|', array ('failed'=>1, 'auth_url_return'=>CopixRequest::get ('auth_url_return', CopixUrl::get ('auth_url_return')))));
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