<?php
/**
 * @package standard
 * @subpackage default
* @author		Croes Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Actions par défaut réalisées par le framework
 * @package standard
 * @subpackage default
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
    * Par défaut, on redirige vers l'url d'accueil définie dans les paramètres
    */
	public function processDefault () {
		$home = CopixConfig::get ('|homePage');
		if (strpos ($home, 'http://')!==0){
			$home = _url ().$home;
		}
		return _arRedirect ($home);
	}

	/**
	 * Page d'accueil du framework
	 */
	public function processWelcome (){
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('default.welcome2Copix3');
		try {
			CopixDB::getConnection ();
			$ppo->dbOK = true;
		}catch (Exception $e){
			$ppo->dbOK = false;
		}
		return _arPPO ($ppo, 'welcome.php');
	}
}
?>