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
class ActionGroupUsers extends CopixActionGroup {
	/**
	 * On s'assure que pour ces tâche ce soit bien un administrateur
	 */
	public function beforeAction ($pActionName){
		CopixPage::add ()->setIsAdmin (true);
		CopixAuth::getCurrentUser()->assertCredential ('basic:admin');
	} 

	/**
	 * Page par défaut
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault (){
		return $this->processList ();
	}

	/**
	 * Liste des utilisateurs avec un écran de recherche
	 */
	public function processList (){
		$params = array ();
		if (($filter = CopixRequest::get ('filter', null)) !== null){
			$params['login'] = $filter;
		}

		foreach (CopixConfig::instance ()->copixauth_getRegisteredUserHandlers() as $handlerInformations){
			$arUsers[$handlerInformations['name']] = CopixUserHandlerFactory::create ($handlerInformations['name'])->find ($params);
		}
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('auth.userList');
		_notify ('breadcrumb', array (
			'path' => array ('#' => $ppo->TITLE_PAGE)
		));

		$ppo->arUsers = $arUsers;
		$ppo->filter = $filter;
		return _arPPO ($ppo, 'users.list.php');
	}
	
	/**
	 * Supression d'un utilisateur
	 */
	public function processDelete (){
		if (CopixRequest::getInt ('confirm') == 1){
			$sp = CopixDAOFactory::createSearchParams ();
			$sp->addCondition ('user_dbgroup', '=', 'auth|dbuserhandler:'.CopixRequest::getInt ('id'));
			_ioDAO ('dbgroup_users')->deleteBy ($sp);
			_ioDAO ('dbuser')->delete (CopixRequest::getInt ('id'));
			return _arRedirect (_url ('auth|users|'));			
		}else{
			if (! ($user = _ioDAO ('dbuser')->get (CopixRequest::getInt ('id')))){
				throw new Exception ('Utilisateur introuvable');
			}
			return CopixActionGroup::process ('generictools|Messages::getConfirm', 
				array ('message'=>_i18n ('auth.confirmDeleteUser', $user->login_dbuser),
						'confirm'=>_url ('auth|users|delete', array ('id'=>$user->id_dbuser, 'confirm'=>1)),
						'cancel'=>_url ('auth|users|')));
		}
	}
	
	/**
	 * Page de modification d'un utilisateur
	 * @return CopixActionReturn
	 */
	public function processEdit (){
		
		//On regarde si c'est une nouvelle demande d'édition
		if ($id = _request ('id')){
			
			$idForm = uniqid();
			if (! ($user = _ioDAO ('dbuser')->get ($id))){
				throw new Exception ('Utilisateur introuvable');
			}
			_sessionSet ('auth|user', $user, $idForm);
		} else {
			$idForm = _request ('idForm');
		}
		
		//Récupération de l'utilisateur à modifier
		$user = _sessionGet  ('auth|user', $idForm);
		$createUser = isset ($user->createUser);
		
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
		$ppo->TITLE_PAGE = $user->id_dbuser === null ? _i18n ('auth.newUser') : _i18n ('auth.editUser', $user->login_dbuser);
		$ppo->user = $user;
		$ppo->errors = $errors;
		$ppo->idForm = $idForm;
		$ppo->createUser = $createUser;
		return _arPPO ($ppo, 'user.edit.php');
	}
	
	/**
	 * Validation des modifications apportées sur un utilisateur
	 * @return CopixActionReturn
	 */
	public function processValid (){
		CopixRequest::assert ('login_dbuser');
		CopixRequest::assert ('enabled_dbuser');
		CopixRequest::assert ('email_dbuser');
		CopixRequest::assert ('password_dbuser');
		CopixRequest::assert ('password_confirmation_dbuser');
		CopixRequest::assert ('idForm');
		
		$idForm = _request ('idForm');
		
		$user    = _sessionGet  ('auth|user', $idForm);
		$userOld = _ioDAO ('dbuser')->get ($user->id_dbuser);
		
		$user->login_dbuser = _request ('login_dbuser', '');
		$user->email_dbuser = _request ('email_dbuser');
		
		$errors = array();
		
		// On vérifie si le login n'est pas vide
		if ($user->login_dbuser === '') {
			$errors['loginEmpty'] = 1;
			
		// On vérifie si le login n'est pas déja pris si c'est un nouvel utilisateur
		// ou si il y a changement de login
		} else if (!$userOld || $userOld->login_dbuser != $user->login_dbuser) {
			if (count (_ioDAO ('dbuser')->findBy (_daoSP ()->addCondition ('login_dbuser', '=', $user->login_dbuser)))){
				$errors['loginNotAvailable'] = 1;
			}
		}
		
		if (_request ('enabled_dbuser') == 0) {
			$user->enabled_dbuser = 0;
		} else {
			$user->enabled_dbuser = 1;
		}
		
		// On vérifie si un mot de passe est donné qu'ils soient bien identiques
		if (CopixRequest::get ('password_dbuser')){
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
			// Si c'est un nouvel utilisateur, il est obligatoire de saisir un nouveau mot de passe.
			if (!$user->id_dbuser){
				$errors['passwordEmpty'] = 1;
			}
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
			// Sauvegarde l'edition des champs personnalisé pour pouvoir les réafficher avec leur modification
			if(CopixModule::isEnabled('authextend')) {
				_class('authextend|authextend')->setEditSession ($idForm);
			}
			$errors['idForm'] = $idForm;
			return _arRedirect (_url ('auth|users|edit', $errors));
		}
				
		//sauvegarde de l'utilisateur
		if ($user->id_dbuser) {
			_ioDAO ('dbuser')->update ($user);
		} else {
			_ioDAO ('dbuser')->insert ($user);
		}
		
		// Enregistre les paramètre utilisateurs personnalisés
		if(CopixModule::isEnabled('authextend')) {
			_class('authextend|authextend')->addValues ($user->id_dbuser, 'auth|dbuserhandler');
		}
		
		CopixSession::destroyNamespace ($idForm);
		
		return _arRedirect ( _url ('auth|users|'));
	}
	
	/**
	 * Création d'un nouvel utilisateur
	 * @return CopixActionReturn
	 */
	public function processCreate (){
		$idForm = uniqid();
		$user = _record ('dbuser');
		$user->createUser = true;
		_sessionSet ('auth|user', $user, $idForm);
		return _arRedirect ( _url ('auth|users|edit', array ('idForm'=>$idForm)));
	}
}