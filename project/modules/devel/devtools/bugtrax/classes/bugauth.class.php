<?php 
/**
 * 
 */

/**
 * Classe de gestion des droits pour le wiki
 */
class BugAuth { 
	/**
	 * Est-ce que l'utilisateur courant peut lire les pages du wiki ?
	 * @return boolean
	 */
	public function canRead() {
		switch (CopixConfig::get ('bugtrax|readbug')){
			case 'public':
				return true;
			case 'registered':
				return CopixAuth::getCurrentUser ()->testCredential ('basic:registered');
			default:
				return CopixAuth::getCurrentUser ()->testCredential ('basic:admin');
		}
	}

	/**
	 * Est-ce que l'utilisateur courant peut écrire les pages du bug ?
	 * @return boolean true or false for current user
	 */
	public function canWriteAdmin() {
		switch (CopixConfig::get ('bugtrax|writebug')){
			case 'public':
				return true;
			case 'registered':
				return CopixAuth::getCurrentUser ()->testCredential ('basic:registered');
			default:
				return CopixAuth::getCurrentUser ()->testCredential ('basic:admin');
		}
	}
	
	/**
	 * Est-ce que l'utilisateur courant peut se servir des tags spéciaux ?
	 * @return boolean
	 */
	public function canWrite() {
		switch (CopixConfig::get ('bugtrax|writeadmin')){
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