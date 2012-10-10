<?php 
/**
* @package	webtools
* @subpackage	blog
* @author	Patrice Ferlet
* @copyright CopixTeam
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * Classe de gestion des droits pour le wiki
 */
class blogAuth { 

	/**
	 * Est-ce que l'utilisateur courant peut écrire les pages du wiki ?
	 * @return boolean true or false for current user
	 */
	public function canWrite() {
		switch (CopixConfig::get ('blog|write')){
			case 'public':
				return true;
			case 'registered':
				return CopixAuth::getCurrentUser ()->testCredential ('basic:registered');
			default:
				return CopixAuth::getCurrentUser ()->testCredential ('basic:admin');
		}
	}
	
	/**
	 * Vérifie si l'utilisateur peut écrire ou non.
	 */
	public function assertWrite (){
		switch (CopixConfig::get ('blog|write')){
			case 'public':
				return true;
			case 'registered':
				return CopixAuth::getCurrentUser ()->assertCredential ('basic:registered');
			default:
				return CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
		}
	}
}
