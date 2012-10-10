<?php
/**
 * @package copix
 * @subpackage auth
 * @author Croës Gérald
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Interface de gestion des groupes
 * 
 * @package copix
 * @subpackage auth
 */
interface ICopixGroupHandler {
	/**
	 * Retourne les groupes auquel appartient l'utilisateur $pUserId
	 * 
	 * @param mixed $pUserId Identifiant de l'utilisateur
	 * @param string $pUserHandler Nom
	 * @return array Clefs : identifiants, valeurs : noms des groupes
	 */
	public function getUserGroups ($pUserId, $pUserHandler);
	
	/**
	 * Retourne des informations sur un groupe
	 * 
	 * @param mixed $pGroupId Identifiant du groupe
	 * @return object Les propriétés contiennent les informations sur le groupe
	 */
	public function getInformations ($pGroupId);
	
	/**
	 * 
	 * Retourne les groupes gérés par ce handler
	 * @return array[IDGROUP] => LIBELLE_GROUP
	 */
	public function getGroupList ();
	
	/**
	 * 
	 * Retourne le libellé du handler
	 * @return string
	 */
	public function getLabel();
	
	
}