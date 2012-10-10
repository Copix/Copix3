<?php
/**
 * @package devtools
 * @subpackage sqldesigner
 * @author Steevan Barboyon
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Intègre l'outil WWW SQL Designer dans un module Copix
 * 
 * @package devtools
 * @subpackage sqldesigner
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Affiche l'iframe qui affichera le designer
	 * 
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$links = array (
			_url ('admin||') => _i18n ('admin|breadcrumb.admin'),
			'#' => _i18n ('default.breadcrumb.edit', 'temp.xml')
		);
		_notify ('breadcrumb', array ('path' => $links));
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('default.title.edit');
		return _arPPO ($ppo, 'default.tpl');
	}
	
	/**
	 * Afficher la partie SQL Designer dans son template uniquement
	 *
	 * @return CopixActionReturn
	 */
	public function processShowDesigner () {
		$ppo = new CopixPPO ();
		CopixHTMLHeader::addJSDOMReadyCode ('var d = new SQL.Designer();');
		return _arPPO ($ppo, array ('template' => 'generictools|blanknohead.tpl', 'mainTemplate' => 'sqldesigner|mainshowdesigner.php'));
	}
}