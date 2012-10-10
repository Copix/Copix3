<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 */

/**
 * Fil d'ariane pour l'administration
 * 
 * @package cms
 * @subpackage heading
 */
class ZoneHeadingAdminBreadcrumb extends CopixZone {
	/**
	 * Rubrique que l'on peut ajouter aux favoris
	 *
	 * @var int
	 */
	private static $_heading = 0;

	/**
	 * Définit un identifiant de rubrique que l'on peut ajouter aux favoris
	 *
	 * @param int $pHeading
	 */
	public static function setHeading ($pHeading) {
		self::$_heading = $pHeading;
	}
	
	/**
	 * Création du contenu
	 *
	 * @param string $toReturn Contenu à retourner
	 * @return boolean
	 */
	protected function _createContent (& $toReturn) {
		$tpl = new CopixTpl ();
		$headingelementinformationservices = new HeadingElementInformationServices ();
		try {
			$path = explode ('-', $headingelementinformationservices->get (self::$_heading)->hierarchy_hei);
			//unset ($path[0]);
			$breadcrumb = array ();
			foreach ($path as $id => $value) {
				$breadcrumb[$value] = $headingelementinformationservices->get ($value)->caption_hei;
			}
			$tpl->assign ('breadcrumb', $breadcrumb);
			$toReturn = $tpl->fetch ('admin/headingadminbreadcrumb.php');
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
}