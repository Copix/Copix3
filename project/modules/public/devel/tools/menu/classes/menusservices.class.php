<?php
/**
 * @package		tools
 * @subpackage 	menu
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Services sur les menus
 * @package tools
 * @subpackage menu 
 */
class MenusServices {
	/**
	 * Supprime un menu
	 * 
	 * @param record $menu
	 * @return bool
	 */
	public function delete ($menu) {
		_ioDao('menusitems')->deleteBy (_daoSp ()->addCondition ('id_menu', '=', _request('id_menu')));
		_ioDao('menus')->delete (_request ('id_menu'));

		return _arRedirect (_url ('menu|adminmenus|'));
	}
}
?>