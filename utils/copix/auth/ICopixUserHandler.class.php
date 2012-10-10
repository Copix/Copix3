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
 * Interface pour les handlers d'utilisateur
 * 
 * @package copix
 * @subpackage auth
 */
interface ICopixUserHandler {
	/**
	 * Demande de connexion
	 * 
	 * @param array $pParams Paramètres envoyés à la demande de login
	 */
	public function login ($pParams);
	
	/**
	 * Demande de déconnexion
	 * 
	 * @param array $pParams Paramètres envoyés à la demande de logout
	 */
	public function logout ($pParams);
	
	/**
	 * Informations sur l'utilisateur
	 * 
	 * @param mixed Identifiant de l'utilisateur
	 * @return ICopixUser
	 */
	public function getInformations ($pUserId);
	
	/**
	 * Recherche d'utilisateurs
	 * 
	 * @param array $pParams Critères de recherche (id, login et caption au minimum)
	 * @return ICopixUser[] 
	 */
	public function find ($pParams = array ());
}