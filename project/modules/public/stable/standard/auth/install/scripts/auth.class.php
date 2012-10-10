<?php
/**
 * @package standard
 * @subpackage auth 
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Gère la creation du user admin par défaut
 */
class CopixModuleInstallerAuth implements ICopixModuleInstaller {
	
	public function processInstall () {
		$user = _ioDAO ('dbuser')->get (1);
		$user->password_dbuser = md5 ($pass = substr (UniqId ('p'), -5));
		_ioDAO ('dbuser')->update ($user);
		CopixSession::set ('admin|database|loginInformations', array ('login'=>'admin', 'password'=>$pass));
	}
	
	public function processDelete () {
		CopixSession::set ('admin|database|loginInformations', null);
	}
	
	public function processUpdate () {
		
	}
}

?>