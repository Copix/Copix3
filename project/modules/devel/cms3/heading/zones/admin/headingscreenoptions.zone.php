<?php
/**
 * @package cms
 * @subpackage heading
 * @copyrigh tCopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 */

/**
 * Options d'affichage
 *
 * @package cms
 * @subpackage heading
 */
class ZoneHeadingScreenOptions extends CopixZone {
	/**
	 * Zone à appeler pour le formulaire d'options
	 *
	 * @var string
	 */
	private static $_zone = null;

	/**
	 * Définit la zone à appeler
	 *
	 * @param int $pHeading
	 */
	public static function setZone ($pZone) {
		self::$_zone = $pZone;
	}

	/**
	 * Retourne la zone à appeler
	 *
	 * @return string
	 */
	public static function getZone () {
		return self::$_zone;
	}

	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		if (self::getZone () == null) {
			return true;
		}
		$tpl = new CopixTPL ();
		$tpl->assign ('zone', CopixZone::process (self::getZone ()));
		$pToReturn = $tpl->fetch ('heading|admin/headingscreenoptions.php');
		return true;
	}
}