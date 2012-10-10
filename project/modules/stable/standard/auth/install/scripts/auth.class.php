<?php
/**
 * @package 	standard
 * @subpackage 	auth 
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Gère la création du user admin par défaut
 */
class CopixModuleInstallerAuth extends CopixAbstractModuleInstaller {
	
	/**
	 * Avant l'installation : 
	 * Génération d'un mot de passe unique
	 * Mise en session des informations de login 
	 *
	 */
	public function processPreInstall () {
		$user = _ioDAO ('dbuser')->get (1);
		switch ($hashMethod = CopixConfig::get ('auth|cryptPassword')){
			case 'md5':
				$user->password_dbuser = md5 ($pass = substr (UniqId ('p'), -5));
				break;
			case 'sha1':
				$user->password_dbuser = sha1 ($pass = substr (UniqId ('p'), -5));
				break;
			case 'sha256':
				$user->password_dbuser = hash ('sha256', $pass = substr (UniqId ('p'), -5));
				break;
			default :
				throw new CopixException (_i18n ('auth.error.unknownHashMethod', $hashMethod));	
		}
		_ioDAO ('dbuser')->update ($user);
		CopixSession::set ('admin|database|loginInformations', array ('login'=>'admin', 'password'=>$pass));
	}

	/**
	 * Avant la suppression : On supprime les variables de session
	 *
	 */
	public function processPreDelete () {
		CopixSession::set ('admin|database|loginInformations', null);
	}
}