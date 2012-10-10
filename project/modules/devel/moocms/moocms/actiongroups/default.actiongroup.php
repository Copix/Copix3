<?php
/**
 * Default actions
 *
 * @author Patrice FERLET <metal3d@copix.org> 
 * @package MooCMS
 * @subpackage MooCMS
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */
class ActionGroupDefault extends CopixActionGroup{

	/**
	 * Display a MooCMS page
	 *
	 * @param string page title
	 * @return ActionReturn::DISPLAY
	 */
	public function processShowPage(){
		$main = new CopixTpl();
		$main->assign('TITLE_PAGE',CopixRequest::get('title'));
		$main->assign('MAIN',_ioClass('moopage')->getPage(CopixRequest::get('title')));
		return _arDisplay($main);
	}
}
