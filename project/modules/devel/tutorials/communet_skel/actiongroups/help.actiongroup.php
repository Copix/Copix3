<?php
/**
 * @package tutorials
 * @subpackage communet_skel
 * @author		Brice Favre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Aide de mise en place d'un module communet
 * @package tutorials
 * @subpackage communet_skel 
 */
class ActionGroupHelp extends CopixActionGroup  {
	
	function processDefault (){
		$ppo = new CopixPPO ();
		return _arPpo ($ppo, 'help_fr.tpl');
	}
}
?>