<?php
/**
 * @package		menu
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

class MenusServices {
	/**
	 * Ajoute un menu
	 *
	 * @param record $menu
	 * @return bool
	 */
	public function add ($menu) {
		// verification des erreurs
		if (_ioDAO ('menus')->check ($menu) !== true) {
			return false;
		}

		// ajout du menu en base
		_ioDAO ('menus')->insert ($menu);

		return true;
	}

	/**
	 * Modifie un menu
	 * 
	 * @param record $menu
	 * @return bool
	 */
	public function edit ($menu) {
		// verification des erreurs
		if (_ioDAO ('menus')->check ($menu) !== true) {
			return false;
		}
		
		// modification du menu en base
		_ioDAO ('menus')->update ($menu);
		
		return true;
	}

	/**
	 * Supprime un menu
	 * 
	 * @param record $menu
	 * @return bool
	 */
	public function delete ($menu) {
		_ioDao('menus')->delete (_request ('id_menu'));
		_ioDao('menusitems')->deleteBy (_daoSp ()->addCondition ('id_menu', '=', _request('id_menu')));
		
		return _arRedirect (_url ('menu|adminmenus|'));
	}

}
?>