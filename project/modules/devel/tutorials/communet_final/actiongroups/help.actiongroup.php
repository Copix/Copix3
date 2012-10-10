<?php
/**
 * @package tutorials
 * @subpackage communet_final
 * @author		Brice Favre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Module de démonstration et aide de Copix 
 * @package tutorials
 * @subpackage communet_final 
 */

class ActionGroupHelp extends CopixActionGroup {
	function processDefault (){
		$ppo = new CopixPPO ();
		return _arPpo ($ppo, 'help_fr.tpl');
	}
}
?>