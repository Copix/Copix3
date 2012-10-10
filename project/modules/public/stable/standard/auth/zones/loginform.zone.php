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
 * Zone de login
 * @package standard
 * @subpackage auth
 */
class ZoneLoginForm extends CopixZone {
	/**
	 * Création de la zone de login
	 */
	function _createContent (& $toReturn) {
		$ppo = new CopixPPO ();
		$ppo->user = CopixAuth::getCurrentUser ()->isConnected () ? CopixAuth::getCurrentUser () : null;
		$ppo->auth_url_return = $this->getParam ('auth_url_return', _url ('#'));
		$ppo->createUser = CopixConfig::get ('auth|createUser');
		$ppo->ask_remember = false;
		$toReturn = $this->_usePPO ($ppo, $this->getParam ('template', 'login.form.php'));
	}
}
?>