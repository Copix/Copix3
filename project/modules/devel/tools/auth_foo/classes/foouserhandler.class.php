<?php
/**
 * @package 	devel
 * @subpackage 	auth_foo
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Gestionnaire d'utilisateur fictif pour des tests en développement
 * 
 * @package devel
 * @subpackage auth_foo
 */
class FooUserHandler implements ICopixUserHandler {
	/**
	 * Connexion
	 *
	 * @param array $pParams	paramètres de connexion
	 * @return CopixUserLogResponse
	 */
	public function login ($pParams){
		if (!isset ($pParams['login'])){
			return new CopixUserLogResponse (false, null, null, 'login obligatoire');
		}
		if ($pParams['login'] == 'test' && $pParams['password'] == 'test'){
			return new CopixUserLogResponse (true, 'auth_foo|foouserhandler', 'test', 'test');			
		}elseif ($pParams['login'] !== 'test'){
			return new CopixUserLogResponse (false, null, null, 'Le compte spécifié n\'existe pas');
		}else{
			return new CopixUserLogResponse (false, null, null, 'Le couple login / mot de passe est incorrect');
		}
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
	 * @return array of DBUser
	 */
	public function find ($pParams = array ()){
		return array (new FooUser (_ppo (array ('login'=>'test', 'id'=>'test_id'))));
	}

	/**
	 * L'email de l'utilisateur est renvoyé
	 * @param integer identifiant de l'utilisateur
	 * @return DBUser L'utilisateur
	 */
	public function getInformations ($pUserId){
		if ($pUserId === 'test_id'){
			return new FooUser (_ppo (array ('login'=>'test', 'id'=>'test_id')));
		}
		throw new CopixException ('No informations on user '.$pUserId);
	}
}