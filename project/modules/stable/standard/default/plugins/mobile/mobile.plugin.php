<?php
/**
 * @package standard
 * @subpackage default
 * @author Stevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Change le main.php par main_mobile.php si on affiche le site depuis un téléphone
 *
 * @package standard
 * @subpackage default
 */
class PluginMobile extends CopixPlugin implements ICopixBeforeProcessPlugin{
	/**
	 * Retourne le libellé
	 *
	 * @return string
	 */
	public function getCaption () {
		return 'Site pour téléphones';
	}

	/**
	 * Retourne la description
	 *
	 * @return string
	 */
	public function getDescription () {
		return 'Change le main.php par main_mobile.php si on affiche le site depuis un téléphone';
	}

	/**
	 * Appelée avant le process d'une action
	 *
	 * @param CopixPPO $pAction Nom de l'action
	 */
	public function beforeProcess (&$pAction) {
		if (CopixMobile::isMobileAgent ()) {
			CopixConfig::instance ()->mainTemplate = 'default|main_mobile.php';
		}
	}
}