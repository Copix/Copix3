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
 * Gestionnaire des utilisateurs depuis la base de données
 * @package standard
 * @subpackage auth
 */
class DBUserHandler implements ICopixUserHandler {
	/**
	 * Connexion
	 *
	 * @param array $pParams	paramètres de connexion
	 * @return CopixUserLogResponse
	 */
	public function login ($pParams){
		if (!isset ($pParams['login'])){
			return new CopixUserLogResponse (false, null, null, null);
		}

		if (count ($results = CopixDB::getConnection ()->doQuery ('select id_dbuser, login_dbuser, password_dbuser, enabled_dbuser from dbuser where login_dbuser=:login', 
				array ('login'=>$pParams['login'])))){
			if ($results[0]->enabled_dbuser == 0){
				return new CopixUserLogResponse (false, null, null, null);
			}
			if ($results[0]->password_dbuser == $this->_cryptPassword (isset ($pParams['password']) ? $pParams['password'] : '')){
				return new CopixUserLogResponse (true, 'dbuserhandler', $results[0]->id_dbuser, $results[0]->login_dbuser);
			}
		}
		return new CopixUserLogResponse (false, null, null, null);
	}

	/**
	 * Déconnexion
	 *
	 * @param array $pParams tableau de paramètres
	 * @return CopixUserLogResponse
	 */
	public function logout ($pParams){
		return new CopixUserLogResponse (true, null, null, null);
	}
	
	/**
	 * Récupération d'une liste d'utilisateurs (id, login, caption, email, enabled)
	 * @param 	array	$pMatchPatterns	tableau d'éléments de recherche
	 * @todo	Implémenter les patterns de recherche	
	 * @return array
	 */
	public function find ($pParams = array ()){
		$sp = CopixDAOfactory::createSearchParams ();
		if (isset ($pParams['login'])){
			if (!is_array ($pParams['login']) && strpos ($pParams['login'], '%')){
				$sp->addCondition ('login_dbuser', 'like', $pParams['login']);
			}else{
				$sp->addCondition ('login_dbuser', '=', $pParams['login']);
			}
		}

		if (isset ($pParams['id'])){
			if (!is_array ($pParams['id']) && strpos ($pParams['id'], '%')){
				$sp->addCondition ('id_dbuser', 'like', $pParams['id']);
			}else{
				$sp->addCondition ('id_dbuser', '=', $pParams['id']);
			}
		}

		if (isset ($pParams['caption'])){
			if (!is_array ($pParams['caption']) && strpos ($pParams['caption'], '%')){
				$sp->addCondition ('login_dbuser', 'like', $pParams['caption']);
			}else{
				$sp->addCondition ('login_dbuser', '=', $pParams['caption']);
			}
		}
		
		if (isset ($pParams['email'])){
			if (!is_array ($pParams['email']) && strpos ($pParams['email'], '%')){
				$sp->addCondition ('email_dbuser', 'like', $pParams['email']);
			}else{
				$sp->addCondition ('email_dbuser', '=', $pParams['email']);
			}
		}
		
		if (isset ($pParams['enabled'])){
			$sp->addCondition ('enabled_dbuser', '=', $pParams['enabled']);
		}

		$results = array ();
		foreach (_ioDAO ('dbuser')->findBy ($sp) as $result){
			$result->caption = $result->login_dbuser;
			unset ($result->password_dbuser);
			$result->login = $result->login_dbuser;
			$result->id = $result->id_dbuser;
			$result->email = $result->email_dbuser;
			$result->enabled = $result->enabled_dbuser;
			$results[] = $result;
		}
		return $results;
	}
	
	/**
	 * Donne la forme cryptée du mot de passe.
	 * @param string	$pClearPassword	le mot de passe en clair
	 * @return string	le mot de passe crypté
	 */
	private function _cryptPassword ($pClearPassword){
		switch ($hashMethod = CopixConfig::get ('auth|cryptPassword')){
			case 'md5':
				return md5 ($pClearPassword);
			case 'sha1':
				return sha1 ($pClearPassword);
			case 'sha256':
				return hash ('sha256', $pClearPassword);
			default :
				throw new CopixException (_i18n ('auth.error.unknownHashMethod', $hashMethod));				
		}
	}
	
	/**
	 * L'email de l'utilisateur est renvoyé
	 * @param integer identifiant de l'utilisateur
	 * @return string email de l'utilisateur
	 */
	public function getInformations ($pUserId){
		if ($objUser = _ioDAO ('dbuser')->get($pUserId)){
			$myObject->email = $objUser->email_dbuser;
			return $myObject;
		}
		throw new CopixException ('No informations on user '.$pUserId);
	}
}
?>