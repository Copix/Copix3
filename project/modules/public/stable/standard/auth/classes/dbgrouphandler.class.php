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
 * Gestion des groupes depuis la base de données (table dbgroup).
 * @package standard
 * @subpackage auth
 */
class DBGroupHandler implements ICopixGroupHandler {

	/**
	 * Récupération des groupes pour un identifiant d'utilisateur donné
	 *
	 * @param	string	$pUserId	l'identifiant de l'utilisateur
	 * @return array of groups
	 */
	public function getUserGroups ($pUserId, $pUserHandler){
		if ($pUserId !== null){
			$registered = ' or g.registered_dbgroup=1  ';
		}
		$arGroup = array ();
		foreach (CopixDB::getConnection ()->doQuery ('select g.id_dbgroup, g.caption_dbgroup from dbgroup g, dbgroup_users gu where ((g.public_dbgroup=1 
          '.$registered.') or (gu.user_dbgroup=:user_dbgroup and gu.id_dbgroup=g.id_dbgroup)) and gu.userhandler_dbgroup=:userhandler_dbgroup', 
		array (':user_dbgroup'=>$pUserId, ':userhandler_dbgroup'=>$pUserHandler)) as $group){
			$arGroup[$group->id_dbgroup] = $group->caption_dbgroup;
		}
		return $arGroup;
	}

	/**
	 * Récupère les informations sur un groupe donné
	 */
	public function getInformations ($pGroupId){
		$result = CopixDB::getConnection ()->doQuery ('select superadmin_dbgroup, public_dbgroup, registered_dbgroup, description_dbgroup from dbgroup where id_dbgroup=:id_dbgroup',
			array ('id_dbgroup'=>$pGroupId));
		if (count ($result)){
			return $result[0];
		}
		throw new Exception ('No informations on Group '.$pGroupId);
	}
}
?>