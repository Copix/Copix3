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
 * Opération sur la gestion des utilisateurs
 * @package standard
 * @subpackage auth
 */
class ActionGroupUsersRegister extends CopixActionGroup {
	/**
	 * On vérifie que l'on a activé les fonctions de création de compte
	 */
	public function beforeAction ($pActionName){
		if (! CopixConfig::get ('auth|createUser')){
			throw new Exception (_i18n ('auth.notAllowed'));
		}
	}
	
	/**
	 * Page de modification d'un utilisateur
	 * @return CopixActionReturn
	 */
	public function processEdit (){
		// Création du tableau d'erreur
		$errors = array ();
		if (_request ('loginNotAvailable', '0') == 1){
			$errors[] = _i18n ('auth.error.loginNotAvailable');
		}
		if (_request ('loginEmpty', '0') == 1){
			$errors[] = _i18n ('auth.error.loginEmpty');
		}
		if (_request ('passwordDoNotMatch', '0') == 1){
			$errors[] = _i18n ('auth.error.passwordDoNotMatch');
		}
		if (_request ('passwordEmpty', '0') == 1){
			$errors[] = _i18n ('auth.error.passwordEmpty');
		}
		if (_request ('emailEmpty', '0') == 1){
			$errors[] = _i18n ('auth.error.emailEmpty');
		}
		if (_request ('emailIsBad', '0') == 1){
			$errors[] = _i18n ('auth.error.emailIsBad');
		}
		if (_request ('confirmCodeBad', '0') == 1){
			$errors[] = _i18n ('auth.error.confirmCodeBad');
		}
		
		// Si le module authextend est activer tester les erreurs
		if (CopixModule::isEnabled('authextend') && 
		    ($errorsExtend = _request ('authextendError', array()))) {
			
			foreach ($errorsExtend as $arValue) {
				if (is_array ($arValue)) {
					$value = array_shift ($arValue);
				} else {
					$value = $arValue;
					$arValue = array ();
				}
				$errors[] = _i18n ($value, $arValue);
			}
		}
		
		//Affichage de la page
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE =  _i18n ('auth.newUser');
		$ppo->errors = $errors;
		$ppo->createInProcess = true;
		$ppo->createUser  = _request ('auth|createUser');
		$ppo->typeConfirm = _request ('auth|typeConfirm');
		$ppo->idForm      = _request ('idForm', uniqid ());
		
		$ppo->user = _sessionGet  ('auth|user', $ppo->idForm);
		if (!$ppo->user) {
			$ppo->user = _record('dbuser');
		}
		
		return _arPPO ($ppo, 'user.edit.php');
	}

	/**
	 * Validation des modifications apportées sur un utilisateur
	 * @return CopixActionReturn
	 */
	public function processValid (){
		
		CopixRequest::assert ('login_dbuser');
		CopixRequest::assert ('email_dbuser');
		CopixRequest::assert ('password_dbuser');
		CopixRequest::assert ('password_confirmation_dbuser');
		
		$idForm = _request ('idForm');
		
		$user = _record('dbuser');
		
		$user->login_dbuser = _request ('login_dbuser', '');
		$user->email_dbuser = _request ('email_dbuser');
		
		$errors = array();
		
		
		// On vérifie si le login n'est pas vide
		if ($user->login_dbuser === '') {
			$errors['loginEmpty'] = 1;
			
		// On vérifie si le login n'est pas déja pris
		}else if (count (_ioDAO ('dbuser')->findBy (_daoSP ()->addCondition ('login_dbuser', '=', $user->login_dbuser)))){
			$errors['loginNotAvailable'] = 1;
		}
		
		// On vérifie si un mot de passe est donné qu'ils soient bien identiques
		if (_request ('password_dbuser')){
			if (CopixRequest::get ('password_dbuser') != 
				CopixRequest::get ('password_confirmation_dbuser')){
					$errors['passwordDoNotMatch'] = 1;
			}else{
				switch ($hashMethod = CopixConfig::get ('auth|cryptPassword')){
					case 'md5':
						$user->password_dbuser = md5 (CopixRequest::get ('password_dbuser'));
						break;
					case 'sha1':
						$user->password_dbuser = sha1 (CopixRequest::get ('password_dbuser'));
						break;
					case 'sha256':
						$user->password_dbuser = hash ('sha256', CopixRequest::get ('password_dbuser'));
						break;
					default :
						throw new CopixException (_i18n ('auth.error.unknownHashMethod', $hashMethod));	
				}
			}			
		}else{
			// Comme c'est automatiquement un nouvel utilisateur, il est obligatoire de saisir un nouveau mot de passe.
			$errors['passwordEmpty'] = 1;
		}
		
		// Test l'email
		if (!$user->email_dbuser){
			$errors['emailEmpty'] = 1;
		}else{
			try {
				CopixFormatter::getMail($user->email_dbuser);
			} catch (CopixException $e) {
				$errors['emailIsBad'] = 1;
			}
		}
		
		if (Copixconfig::get('auth|typeConfirm') == "email"){

			$user->enabled_dbuser = 0;
		}else {
			$user->enabled_dbuser = 1;
		}
		
		// Si le module anti-spamest activé test la protection imageprotect 
		if(CopixModule::isEnabled('antispam')) {
			
			// Test si le code de ssession est valide
			if (!_class('antispam|captcha')->create ()->check ()) {
				$errors['confirmCodeBad'] = 1;
			}
		}
		
		// Test les paramètres utilisateur personnalisés
		if(CopixModule::isEnabled('authextend')) {
			
			// Test si les champs personnalisés sont valides
			if ($errorsExtend = _class('authextend|authextend')->valid ()) {
				$errors['authextendError'] = $errorsExtend;
			}
		}
		
		// Redirige vers l'éditeur si il y a des erreurs
		if (count($errors) != 0) {
			_sessionSet ('auth|user', $user, $idForm);
			if(CopixModule::isEnabled('authextend')) {
				_class('authextend|authextend')->setEditSession ($idForm);
			}
			$errors['idForm'] = $idForm;
			return _arRedirect (_url ('auth|usersregister|edit', $errors));
		}
		
		//sauvegarde de l'utilisateur
		_ioDAO ('dbuser')->insert ($user);
		
		// Enregistre les paramètre utilisateurs personnalisés
		if(CopixModule::isEnabled('authextend')) {
			_class('authextend|authextend')->addValues ($user->id_dbuser, 'auth|dbuserhandler');
		}
		
		CopixSession::destroyNamespace ($idForm);
		
		return _arRedirect ( _url (''));
	}
}