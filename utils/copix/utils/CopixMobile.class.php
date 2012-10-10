<?php
/**
 * @package copix
 * @subpackage utils
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */
class CopixMobile {
	/**
	 * Cache de la recherche du useragent
	 *
	 * @var boolean
	 */
	private static $_isMobileAgent = null;

	/**
	 * Indique si le navigateur utilisé est un téléphone
	 *
	 * @return boolean
	 */
	public static function isMobileAgent () {
		if (self::$_isMobileAgent === null) {
			self::$_isMobileAgent = (isset ($_SERVER['HTTP_USER_AGENT']) && strpos ($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false);
		}
		return self::$_isMobileAgent;
	}
}