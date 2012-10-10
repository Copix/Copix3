<?php
/**
* @package		copix
* @subpackage 	auth
* @author		Gérald Croës
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
*/

/**
 * Classe de base pour les exceptions d'authentification 
 * @package		copix
 * @subpackage	auth
 */
class CopixAuthException extends CopixException {}

/**
 * Gestion des informations sur l'authentification
 * @package   copix
 * @subpackage auth
 */
class CopixAuth {
    /**
     * Récupération de l'utilisateur courant
     * @return CopixUser
     */
    static public function getCurrentUser (){
    	if (CopixSession::get ('copix|auth|user') === null){
    		CopixSession::set ('copix|auth|user', new CopixUser ());
    	}
    	return CopixSession::get ('copix|auth|user');
    }
    
    /**
     * Destruction de l'utilisateur courant
     */
    static public function destroyCurrentUser (){
    	CopixSession::set ('copix|auth|user', null);
    }
}
?>