<?php
/**
 * @package tools
 * @subpackage advancedehelp
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Gestion des dossiers
 *
 * @package tools
 * @subpackage advancedhelp
 */
class ActionGroupFolders extends CopixActionGroup {
	/**
	 * Executée avant toute action
	 *
	 * @param string $pAction Nom de l'action
	 */
	protected function _beforeAction ($pAction) {
		_currentUser ()->assertCredential ('basic:admin');
		CopixPage::add ()->setIsAdmin (true);
		AHelpTools::breadcrumb (_request ('folder', null));
	}

	public function processDefault () {
		AHelpFoldersServices::get (_request ('folder'));
	}
}