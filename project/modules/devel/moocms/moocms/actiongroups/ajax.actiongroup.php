<?php
/**
 * Action called by Ajax
 * 
 * @package MooCMS
 * @subpackage MooCMS
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * 
 */
class ActionGroupAjax extends CopixActionGroup{
	/**
	 * Display a HTML/wiki editor
	 *
	 * @return ActionReturn::DirectPPO
	 */
	public function processCodeEditor(){
		$language = CopixRequest::get('lang','html');

		$ppo = new CopixPPO();
		$tpl = new CopixTpl();
		$tpl->assign('lang',$language);
		$ppo->MAIN = $tpl->fetch('code.editor.php');
		return _arDirectPPO($ppo,'generictools|blank.tpl');
	}
}
