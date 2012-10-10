<?php
/**
 * @package tutorials
 * @subpackage communet_final 
 * @author		Brice Favre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Module de démonstration et aide de Copix
 * @package tutorials
 * @subpackage communet_final 
 */

class ActionGroupDefault extends CopixActionGroup  {

	/**
	 * Action par défaut
	 * 
	 */
	public function processDefault (){
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = 'Titre de ma page';
		$ppo->site = 'Mon site communetaire';
		if (CopixAuth::getCurrentUser ()->isConnected()) {
			$ppo->user = CopixAuth::getCurrentUser ()->getCaption();
		}
		return _arPPO ($ppo, 'default.tpl');
	}
	
	/**
	 * Page d'inscription sur le site
	 * 
	 */
	public function processSignup (){
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = 'Inscription sur le site';
		$ppo->login = _request ('login', '');
		$ppo->description = _request ('description', '');
		return _arPPO ($ppo, 'form.tpl');
	}
	
	/**
	 * Page de validation de l'inscription
	 * 
	 */
	public function processValidSignup (){
		// Ancienne méthode
		// $msgError = $this->_getErrorForm ();
		$msgError = _validator ('communet_final|myvalidator')->check ($value);
		
		
		if ($msgError !== true) {
			return CopixActionGroup::process ('genericTools|Messages::getError', array ('message'=>$msgError, 'back'=>CopixUrl::get ('communet_final||signup')));
		}
		
		$record = _record ('cn_user');
		$record->login = _request ('login', null);
		$record->password = _request ('password', null);
		$record->description = _request ('description', null);
		_ioDao ('cn_user')->insert ($record);
		return _arRedirect (_url('communet_final||page', array ('id'=>$record->id)));
	}

	/**
	 * Affichage de la page utilisateur 
	 *  
	 */
	public function processPage (){
		$id = _request ('id');
		$login = _request ('login');
		if ($id !== null) {
			$record = _ioDao('cn_user')->get ($id);
		} else if ($login !== null) {
			$sp = _daoSp ()->addCondition ('login', '=', $login);
			$results = _ioDao('cn_user')->findBy ($sp);
			$record = $results[0];
		} 
		
		if ($record->id === null) {
			return CopixActionGroup::process ('genericTools|Messages::getError', array ('message'=>'Page inexistante', 'back'=>CopixUrl::get ('communet_final||')));
		}
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = 'La page de '.$record->login;		
		$ppo->site = 'la page de '.$record->login;
		$ppo->description = CopixZone::process ('communet_final|description', array ('id'=>$record->id));
		$ppo->friendlist  = CopixZone::process ('communet_final|friendlist', array ('id'=>$record->id));
		
		if (CopixAuth::getCurrentUser ()->isConnected()) {
			$currentlogin = CopixAuth::getCurrentUser ()->getLogin();
			$ppo->isUserConnected = true; 
			if ($record->login == $currentlogin) {
				$ppo->isUserPage = true;
				$ppo->login = $record->login;
			} else {
				$results = _ioDao ('cn_friend_list')->findBy(_daoSp()->addCondition ('userid', '=', CopixAuth::getCurrentUser ()->getId())
				                                                     ->addCondition ('friendid', '=', $record->id));
															   
				if (count ($results) > 0) {
					$ppo->isfriend = $record->login. ' fait partie de votre liste d\'amis';
				} else {
					$ppo->id = $record->id;
					$ppo->isfriend = $record->login. ' ne fait pas partie de votre liste d\'amis';
				}
			}
		}
		return _arPPO ($ppo, 'page.tpl');
	}

	/**
	 * Action d'édition de la page
	 * 
	 */
	public function processEditPage (){
		$login = _request ('login');
		
		$assertString = 'user:'.$login;
		CopixAuth::getCurrentUser()->assertCredential ($assertString);

		$results = _ioDao('cn_user')->findBy (_daoSp ()->addCondition ('login', '=', $login));
		
		$ppo = new CopixPPO ();
		$ppo->login = $login;
		$ppo->description = $results[0]->description;
		$ppo->TITLE_PAGE = 'Edition de votre page';
		return _arPPO ($ppo, 'editpage.tpl');
	}


	/**
	 * Validation de l'édition des profils utilisateurs
	 * 
	 */
	public function processValidEdit (){	
		$login = _request ('login', null);
		$assertString = 'user:'.$login;
		CopixAuth::getCurrentUser()->assertCredential ($assertString);	
		$description = _request ('description', null);
		$results = _ioDao('cn_user')->findBy (_daoSp ()->addCondition ('login', '=', $login));
		$record = $results[0];
		$record->description = $description;
		_ioDao ('cn_user')->update ($record);
		return _arRedirect (_url('communet_final||page', array ('id'=>$record->id)));
	}
	
	/**
	 * Action d'ajout à la liste d'amis 
	 */
	public function processAddToFriend (){
		if (CopixAuth::getCurrentUser ()->isConnected()) {
			$record = _record ('cn_friend_list');
			$record->friendid = _request ('id', null);
			$record->userid = CopixAuth::getCurrentUser ()->getId();		
			_ioDao('cn_friend_list')->insert ($record);
			return CopixActionGroup::process ('genericTools|Messages::getMessage', array ('message'=>'Action effectu�e', 'back'=>_url ('communet_final||page', array('id' => $record->id))));
		} else {
			return CopixActionGroup::process ('genericTools|Messages::getError', array ('message'=>'Vous ne pouvez faire cette op�ration', 'back'=>CopixUrl::get ('communet_final||signup')));
		}			
	}


	/**
	 * Fonction privée de validation du formulaire 
	 * 
	 */
	private function _getErrorForm (){
		if ((_request('login') === null)) {
			return 'Veuillez renseigner le login';
		} else if ((_request('password') === null)) {
			return 'Veuillez renseigner votre mot de passe';
		} else if ((_request('password') !== _request('confirmpassword'))) {
			return 'Le mot de passe ne correspond pas';
		}
		return false;
	}
}