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
 * Actions relatives à la gestion des groupes
 * @package standard
 * @subpackage auth
 */
class ActionGroupGroups extends CopixActionGroup {
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
	 * Liste des groupes d'utilisateur
	 */
	public function processList (){
		$arGroups = _ioDAO ('dbgroup')->findAll ();			

		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('auth.groupList');
		$ppo->arGroups = $arGroups;

		return _arPPO ($ppo, 'groups.list.php');
	}
	
	/**
	 * Supression d'un groupe d'utilisateur
	 */
	public function processDelete (){
		if (CopixRequest::getInt ('confirm') == 1){
			$sp = CopixDAOFactory::createSearchParams ();
			$sp->addCondition ('id_dbgroup', '=', CopixRequest::getInt ('id'));
			_ioDAO ('dbgroup_users')->deleteBy ($sp);
			_ioDAO ('dbgroup')->delete (CopixRequest::getInt ('id'));
			return _arRedirect (_url ('auth|groups|'));			
		}else{
			if (! ($group = _ioDAO ('dbgroup')->get (CopixRequest::getInt ('id')))){
				throw new Exception ('Groupe introuvable');
			}
			return CopixActionGroup::process ('generictools|Messages::getConfirm', 
				array ('message'=>_i18n ('auth.confirmDeleteGroup', $group->caption_dbgroup),
						'confirm'=>_url ('auth|groups|delete', array ('id'=>$group->id_dbgroup, 'confirm'=>1)),
						'cancel'=>_url ('auth|groups|')));
		}
	}
	
	/**
	 * Page de modification d'un groupe d'utilisateurs
	 */
	public function processEdit (){
		//On regarde si c'est une nouvelle demande d'édition
		if (CopixRequest::get ('id')){
			if (! ($group = _ioDAO ('dbgroup')->get (CopixRequest::get ('id')))){
				throw new Exception ('Groupe introuvable');
			}
			$usersToSet = array ();

			$sp = CopixDAOFactory::createSearchParams ();
			$sp->addCondition ('id_dbgroup', '=', $group->id_dbgroup);
//			$sp->addCondition ('userhandler_dbgroup', "=", 'auth|dbuserhandler');
			$arUsers = array ();
			foreach (_ioDAO ('dbgroup_users')->findBy ($sp) as $userInfo){
				if (!isset ($arUsers[$userInfo->userhandler_dbgroup])){
					$arUsers[$userInfo->userhandler_dbgroup] = array ();
				}				
				$arUsers[$userInfo->userhandler_dbgroup][] = $userInfo->user_dbgroup;
			}

			$usersToSet = array ();
			foreach ($arUsers as $userHandler=>$users){
				$handler = CopixUserHandlerFactory::create ($userHandler);
				foreach ($handler->find (array ('id'=>$users)) as $userInformations){
					$usersToSet[$userHandler][$userInformations->id] = $userInformations->login; 
				}
			}

			CopixSession::set ('auth|group', $group);
			CopixSession::set ('auth|usersgroup', $usersToSet);
		}

		//errerurs éventuelles
		$errors = array ();

		//Récupération de l'utilisateur à modifier
		$group = CopixSession::get ('auth|group');

		//Affichage de la page
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = $group->id_dbgroup  === null ? _i18n ('auth.newGroup') : _i18n ('auth.editGroup', $group->caption_dbgroup);
		$ppo->group = $group;
		$ppo->arUsers = CopixSession::get ('auth|usersgroup');
		$ppo->errors = $errors;
		return _arPPO ($ppo, 'group.edit.php');
	}
	
	/**
	 * Validation des modifications apportées sur un utilisateur
	 */
	public function processValid (){
		$this->_validFromRequest ();
		$group = CopixSession::get ('auth|group');
		
		//sauvegarde de l'utilisateur
		if ($group->id_dbgroup){
			_ioDAO ('dbgroup')->update ($group);
			$sp = _daoSp ()->addCondition ('id_dbgroup', '=', $group->id_dbgroup);
			_ioDAO ('dbgroup_users')->deleteBy ($sp);
		}else{
			_ioDAO ('dbgroup')->insert ($group);
		}

		foreach (CopixSession::get ('auth|usersgroup') as $handler=>$usersInfo){
			foreach ($usersInfo as $id=>$caption){
				$record = _record ('dbgroup_users');
				$record->id_dbgroup = $group->id_dbgroup;
				$record->userhandler_dbgroup = $handler;
				$record->user_dbgroup = $id;
				_ioDAO ('dbgroup_users')->insert ($record);
			}
		}

		CopixSession::set ('auth|group', null);
		CopixSession::set ('auth|usersgroup', null);
		return _arRedirect (_url ('auth|groups|'));
	}
	
	/**
	 * Création d'un nouvel utilisateur
	 */
	public function processCreate (){
		CopixSession::set ('auth|group', _record ('dbgroup'));
		CopixSession::set ('auth|usersgroup', array ());

		return _arRedirect (_url ('auth|groups|edit'));
	}

	/**
	 * Sélection des utilisateurs 
	 */
	public function processSelectUsers (){
		$this->_validFromRequest ();
	
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('auth.group.selectUser');
		$ppo->arUsers = array ();
		foreach (CopixConfig::instance ()->copixauth_getRegisteredUserHandlers () as $handlerInformations){
			$ppo->arUsers[$handlerInformations['name']] = CopixUserHandlerFactory::create ($handlerInformations['name'])->find ();
		}
		return _arPPO ($ppo, 'users.select.php');
	}
	
	/**
	 * Ajout d'utilisateurs au groupe
	 */
	public function processAddUsers (){
		$arCurrentUsers = CopixSession::get ('auth|usersgroup');
		foreach (CopixRequest::get ('users', array ()) as $handler=>$infos){
			if (isset ($arCurrentUsers[$handler])){
				foreach ($infos as $id=>$caption){
					$arCurrentUsers[$handler][$id] = $caption;
				}
			}else{
				$arCurrentUsers[$handler] = $infos;
			}
		}
		CopixSession::set ('auth|usersgroup', $arCurrentUsers);
		return _arRedirect (_url ('auth|groups|edit'));		
	}
	
	/**
	 * Validation du groupe depuis la requête
	 */
	private function _validFromRequest (){
		CopixRequest::assert ('caption_dbgroup');

		$group = CopixSession::get ('auth|group');
		$group->caption_dbgroup = CopixRequest::get ('caption_dbgroup');
		$group->description_dbgroup = CopixRequest::get ('description_dbgroup');

		$group->superadmin_dbgroup = CopixRequest::get ('superadmin_dbgroup', null) ? 1 : 0;
		$group->registered_dbgroup = CopixRequest::get ('registered_dbgroup', null) ? 1 : 0;
		$group->public_dbgroup     = CopixRequest::get ('public_dbgroup', null) ? 1 : 0;

		CopixSession::set ('auth|group', $group);
	}
	
	/**
	 * Supression d'un utilisateur du groupe
	 */
	public function processRemoveUser (){
		$this->_validFromRequest ();
		$users = CopixSession::get ('auth|usersgroup');
		unset ($users[CopixRequest::get ('handlerUser')][CopixRequest::get ('idUser')]);
		CopixSession::set ('auth|usersgroup', $users);
		return _arRedirect (_url ('auth|groups|edit'));
	}
}
?>