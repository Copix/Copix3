<?php
/**
 * @package		404
 * @author		Favre Brice
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * @package		standard 
 * @subpackage	404 
 */
class ActionGroupDefault extends CopixActionGroup {

	/**
	 * Action par défaut
	 *
	 * @return CopixPPO
	 */
	function processDefault (){
		$ppo = new CopixPPO ();
		$ppo->home_url = CopixConfig::get ('default|homePage');
		if (strpos ($ppo->home_url , 'http://')!==0){
			$ppo->home_url  = _url ().$ppo->home_url;
		}
		$ppo->referrer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
		$ppo->search_url = CopixConfig::get ('404|search_url');
		$ppo->sitemap_url = CopixConfig::get ('404|sitemap_url');

		// Mise en place des en-têtes 404
		header ("HTTP/1.0 404 Not Found");	
		header ("Status: 404 Not found");
		return _arPPO ($ppo, 'default.tpl');
	}
}
?>