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
 * @ignore
 */
define ('COPIX_PROJECT_PATH', dirname (__FILE__) . '/');
define ('COPIX_TEMP_PATH',    COPIX_PROJECT_PATH.'../temp/');
define ('COPIX_CACHE_PATH',   COPIX_TEMP_PATH.'cache/');
define ('COPIX_LOG_PATH',     COPIX_TEMP_PATH.'log/');
define ('COPIX_VAR_PATH',     COPIX_PROJECT_PATH.'../var/');

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
		
		$tplObject->assign ('menuItems', array ('Accueil'=>_url ('default|default|default'),
												'Présentation'=>'http://www.copix.org/index.php/wiki/Presentation',
												'Tutoriaux'=>'http://www.copix.org/index.php/wiki/Tutoriaux',
												'Documentation'=>'http://www.copix.org/index.php/wiki/Documentation',
												'Forum'=>'http://forum.copix.org',
												'Téléchargement'=>'http://forum.copix.org',
												'Site officiel'=>'http://www.copix.org')
							);

    }
}
?>