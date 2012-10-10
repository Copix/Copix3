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
	public function beforeAction (){
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
	 */
	public function processEdit (){
		//On regarde si c'est une nouvelle demande d'édition
		if (CopixRequest::get ('id')){
			if (! ($user = _ioDAO ('dbuser')->get (CopixRequest::get ('id')))){
				throw new Exception ('Utilisateur introuvable');
			}
			CopixSession::set ('auth|user', $user);
		}
		
		//Récupération de l'utilisateur à modifier
		$user = CopixSession::get ('auth|user');
		
		//création du tableau d'erreur
		$errors = array ();
		if (CopixRequest::get ('loginNotAvailable', '0') == 1){
			$errors[] = _i18n ('auth.error.loginNotAvailable');
		}
		if (CopixRequest::get ('passwordDoNotMatch', '0') == 1){
			$errors[] = _i18n ('auth.error.passwordDoNotMatch');
		}
		if (CopixRequest::get ('passwordEmpty', '0') == 1){
			$errors[] = _i18n ('auth.error.passwordEmpty');
		}
		if (CopixRequest::get ('emailEmpty', '0') == 1){
			$errors[] = _i18n ('auth.error.emailEmpty');
		}

		//Affichage de la page
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = $user->id_dbuser === null ? _i18n ('auth.newUser') : _i18n ('auth.editUser', $user->login_dbuser);
		$ppo->user = $user;
		$ppo->errors = $errors;
		return _arPPO ($ppo, 'user.edit.php');
	}
	
	/**
	 * Validation des modifications apportées sur un utilisateur
	 */
	public function processValid (){
		CopixRequest::assert ('login_dbuser');
		$user = CopixSession::get ('auth|user');
		$user->login_dbuser = CopixRequest::get ('login_dbuser');
		$user->email_dbuser = CopixRequest::get ('email_dbuser');
		if (CopixRequest::get ('enabled_dbuser') == 0) {
			$user->enabled_dbuser = 0;
		} else {
			$user->enabled_dbuser = 1;
		}
		CopixSession::set ('auth|user', $user);
		
		//on vérifie si le login n'est pas déja pris
		$sp = _daoSp ()->addCondition ('login_dbuser', '=', $user->login_dbuser);
		if ($user->id_dbuser){
			//l'utilisateur existe déja, on demande à vérifier l'unicité du login pour l'utilisateur courant
			$sp->addCondition ('id_dbuser', '<>', $user->id_dbuser);
		}
		if (count (_ioDAO ('dbuser')->findBy ($sp))){
			return _arRedirect (_url ('auth|users|edit', array ('loginNotAvailable'=>'1')));
		}
		
		//on vérifie si un mot de passe est donné qu'ils soient bien identiques
		if (CopixRequest::get ('password_dbuser')){
			if (CopixRequest::get ('password_dbuser') != 
				CopixRequest::get ('password_confirmation_dbuser')){
					return _arRedirect (_url ('auth|users|edit', array ('passwordDoNotMatch'=>'1')));
			}else{
				$user->password_dbuser = md5 (CopixRequest::get ('password_dbuser'));
			}			
		}else{
			//si c'est un nouvel utilisateur, il est obligatoire de saisir un nouveau mot de passe.
			if (!$user->id_dbuser){
				return _arRedirect (_url ('auth|users|edit', array ('passwordEmpty'=>'1')));
			}
		}
		
		if (!$user->email_dbuser){
			return _arRedirect (_url ('auth|users|edit', array ('emailEmpty'=>'1')));
		}
		
		//sauvegarde de l'utilisateur
		if ($user->id_dbuser){
			_ioDAO ('dbuser')->update ($user);
		}else{
			_ioDAO ('dbuser')->insert ($user);
		}

		CopixSession::set ('auth|user', null);
		return _arRedirect ( _url ('auth|users|'));
	}
	
	/**
	 * Création d'un nouvel utilisateur
	 */
	public function processCreate (){
		CopixSession::set ('auth|user', _record ('dbuser'));
		return _arRedirect ( _url ('auth|users|edit'));
	}
}
?>