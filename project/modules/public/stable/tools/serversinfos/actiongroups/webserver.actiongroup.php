<?php 
/**
 * @package devtools
 * @subpackage moduleeditor
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Informations sur le server web
 * @package devtools
 * @subpackage moduleeditor
 */
class ActionGroupWebServer extends CopixActionGroup {
	
	/**
	 * Seuls les administrateurs ont accès à cette section
	 */
	public function beforeAction ($pActionName) {
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
	
	/**
	 * Affichage du PHPInfo dans la charte courante.
	 */
	public function processDefault () {
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('webserver.titlepage');

		ob_start ();                                                                                                       
		phpinfo ();                                                                                                        
		$info = ob_get_contents ();                                                                                        
		ob_end_clean ();                                                                                                   
		$ppo->phpinfo = preg_replace ('%^.*<body>(.*)</body>.*$%ms', '$1', $info);

		return _arPpo ($ppo, 'webserver.tpl');
	}
}
 ?>