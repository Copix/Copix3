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
	 * @param	string	$pUserId	l'identifiant de l'utilisateur, null si on test pour un utilisateur non connecté
	 * @return array of groups
	 */
	public function getUserGroups ($pUserId, $pUserHandler){
		$registered = ($pUserId !== null) ? ' or g.registered_dbgroup=1 ' : null;

		$arGroup = array ();
		$query = '
			select g.id_dbgroup, g.caption_dbgroup
			from dbgroup g, dbgroup_users gu
			where
				(g.public_dbgroup = 1 ' . $registered . ')
				or (
					gu.user_dbgroup = :user_dbgroup
					and gu.id_dbgroup = g.id_dbgroup
					and gu.userhandler_dbgroup = :userhandler_dbgroup
				)';
		$binds = array (':user_dbgroup'=>$pUserId, ':userhandler_dbgroup'=>$pUserHandler);
		foreach (CopixDB::getConnection ()->doQuery ($query, $binds) as $group) {
			$arGroup[$group->id_dbgroup] = $group->caption_dbgroup;
		}
		return $arGroup;
	}

	/**
	 * Récupère les informations sur un groupe donné
	 */
	public function getInformations ($pGroupId){
		$result = CopixDB::getConnection ()->doQuery ('select superadmin_dbgroup, public_dbgroup, registered_dbgroup, description_dbgroup from dbgroup where id_dbgroup=:id_dbgroup',
			array (':id_dbgroup'=>$pGroupId));
		if (count ($result)){
			return $result[0];
		}
		throw new CopixException ('No informations on Group '.$pGroupId);
	}
	
	/**
	 * 
	 * Retourne les groupes gérés par ce handler
	 * @return array[IDGROUP] => LIBELLE_GROUP
	 */
	public function getGroupList (){
		$arGroup = array ();
		$query = '
			select g.id_dbgroup, g.caption_dbgroup
			from dbgroup g';
		
		foreach (CopixDB::getConnection ()->doQuery ($query) as $group) {
			$arGroup[$group->id_dbgroup] = $group->caption_dbgroup;
		}
		return $arGroup;
	}
	
	/**
	 * 
	 * Retourne le libellé du handler
	 * @return string
	 */
	public function getLabel(){
		return 'Groupe base de donnée';
	}
	
	
}
?>