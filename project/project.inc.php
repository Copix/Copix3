<?php
/**
* @package		copix
* @subpackage	core
* @author		Croes Gérald, Jouanneau Laurent
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Contrôller du projet
 * @package		copix
 * @subpackage 	core
 */
class ProjectController extends CopixController {
	/**
	 * Actions communes à toutes les pages
	 */
	function _processStandard ($tplObject) {
		$tplVars = $tplObject->getTemplateVars ();
				
		if (! isset ($tplVars['TITLE_PAGE'])) {
			$tplVars['TITLE_PAGE'] = CopixConfig::get ('|titlePage');
			$tplObject->assign ('TITLE_PAGE', $tplVars['TITLE_PAGE']);
		}

		if (! isset ($tplVars['TITLE_BAR'])) {
			$tplVars['TITLE_BAR'] = str_replace ('{$TITLE_PAGE}', $tplVars['TITLE_PAGE'], CopixConfig::get ('|titleBar'));
			$tplObject->assign ('TITLE_BAR', $tplVars['TITLE_BAR']);
		}
    }
    
    /**
	 * Si le module 404 est activé alors on redirige la page
	 */
	function _doNotExistsAction () {
		if (CopixModule::isEnabled('404')){
			header ('location: '._url ('404||'));
		} else {
			parent::_doNotExistsAction();
		}
	}
}