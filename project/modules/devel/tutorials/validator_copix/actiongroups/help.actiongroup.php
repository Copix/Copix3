<?php
/**
 * @package		tutorials
 * @subpackage 	validator_copix
 * @author		Brice Favre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Aide sur le tutoriel
 * @package		tutorials
 * @subpackage 	validator_copix
 */
class ActionGroupHelp extends CopixActionGroup {

	/**
	 * Action d'accueil
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault (){
		$ppo = new CopixPpo ();
		// @todo : Ajouter une page en anglais
		return _arPpo ($ppo, 'help_fr.default.php');
	}
}