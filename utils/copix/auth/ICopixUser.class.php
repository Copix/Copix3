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
 * Interface des classes décrivant un utilisateur
 *
 * @package copix
 * @subpackage auth
 */
interface ICopixUser {
	/**
	 * Retourne le libellé de l'utilisateur
	 *
	 * @return string
	 */
	public function getCaption ();
	
	/**
	 * Retourne le login de l'utilisateur
	 * 
	 * @return string
	 */
	public function getLogin ();

	/**
	 * Retourne l'identifiant technique de l'utilisateur
	 *
	 * @return mixed
	 */
	public function getId ();
	
	/**
	 * Retourne le nom du handler responsable de cet utilisateur
	 *
	 * @return string
	 */
	public function getHandler ();
}
