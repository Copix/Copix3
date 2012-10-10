<?php
/**
 * @package devel
 * @subpackage auth_foo
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Gestion des groupes fictive pour les tests en développement
 * 
 * @package devel
 * @subpackage auth_foo
 */
class FooGroupHandler implements ICopixGroupHandler {

	/**
	 * Récupération des groupes pour un identifiant d'utilisateur donné
	 *
	 * @param	string	$pUserId	l'identifiant de l'utilisateur, null si on test pour un utilisateur non connecté
	 * @return array of groups
	 */
	public function getUserGroups ($pUserId, $pUserHandler){
		if ($pUserId == 'test' && $pUserHandler == 'auth|foouserhandler'){
			// return array (new FooUserGroup ());
		}
		return array ();
	}

	/**
	 * Récupère les informations sur un groupe donné
	 */
	public function getInformations ($pGroupId){
		return _ppo ();
	}
	
	/**
	 * Récupère la liste des groupes
	 */
	public function find ($pParams = array ()){
		return array ();
	}
}