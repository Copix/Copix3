<?php
/**
 * @package tools
 * @subpackage menus
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Gestion des menus
 *
 * @package tools
 * @subpackage menus
 */
class MenusService {
	/**
	 * Menus
	 *
	 * @var MenusMenu[]
	 */
	private static $_menus = array ();

	/**
	 * Retourne le menu demandé
	 *
	 * @param string $pId
	 * @return MenusMenu
	 */
	public static function get ($pId) {
		if (!isset (self::$_menus[$pId])) {
			throw new MenusException ('Le menu "' . $pId . '" n\'existe pas.');
		}
		return self::$_menus[$pId];
	}

	/**
	 * Indique si le menu demandé existe
	 *
	 * @param string $pId
	 * @return boolean
	 */
	public static function exists ($pId) {
		return isset (self::$_menus[$pId]);
	}

	/**
	 * Retourne un nouveau menu
	 *
	 * @param string $pId
	 */
	public static function create ($pId) {
		if (isset (self::$_menus[$pId])) {
			throw new MenusException ('Le menu "' . $pId . '" existe déja.');
		}
		return self::$_menus[$pId] = new MenusMenu ();
	}
}