<?php 
/**
 * @package	webtools
 * @subpackage	wiki
* @author	Patrice Ferlet
* @copyright CopixTeam
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe de gestion des droits
 * @package	webtools
 * @subpackage	wiki
 */
class WikiAuth { 
	/**
	 * Est-ce que l'utilisateur courant peut lire les pages du wiki ?
	 * @return boolean
	 */
	public function canRead() {
		return CopixAuth::getCurrentUser ()->testCredential ('module:read@wiki');
	}

	/**
	 * Est-ce que l'utilisateur courant peut écrire les pages du wiki ?
	 * @return boolean true or false for current user
	 */
	public function canWrite() {
		return CopixAuth::getCurrentUser ()->testCredential ('module:write@wiki');
	}
	
	/**
	 * Est-ce que l'utilisateur courant peut se servir des tags spéciaux ?
	 * @return boolean
	 */
	public function canWriteSpecialsTags() {
		switch (CopixConfig::get ('wiki|writespecialtags')){
			case 'public':
				return true;
			case 'registered':
				return CopixAuth::getCurrentUser ()->testCredential ('basic:registered');
			default:
				return CopixAuth::getCurrentUser ()->testCredential ('basic:admin');
		}
	}
}
?>