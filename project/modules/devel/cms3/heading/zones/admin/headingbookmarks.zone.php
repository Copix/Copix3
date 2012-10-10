<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain VUIDART
 */

/**
 * Favoris pour les elementChooser
 * 
 * @package     cms
 * @subpackage  heading
 */
class ZoneHeadingBookmarks extends CopixZone {
	/**
	 * Rubrique que l'on peut ajouter aux favoris
	 *
	 * @var int
	 */
	private static $_heading = null;

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
	 * @param string $pToReturn
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$bookmarks = CopixUserPreferences::get('heading|bookmark', '');
		$arElements = array();
		if ($bookmarks != ''){
			$arBookmarks = explode (';', $bookmarks);
			$newBookmarks = $arBookmarks;
			$saveBookmarks = false;
			
			if (!empty($arBookmarks)){
				foreach ($arBookmarks as $index => $public_id){
					try {
						$arElements[] = _ioClass('heading|headingelementinformationservices')->get($public_id);
					} catch (Exception $e) {
						// si l'élément n'existe plus on le delete des préférences
						unset ($newBookmarks[$index]);
						$saveBookmarks = true;
					}
				}
			}

			if ($saveBookmarks) {
				CopixUserPreferences::set ('heading|bookmark', implode (';', $newBookmarks));
			}
		}
		$tpl = new CopixTpl ();
		$tpl->assign ('treeId', $this->getParam ('treeId', false));
		$tpl->assign ('filters', $this->getParam ('filters', array()));
		$tpl->assign ('heading', self::$_heading);
		$tpl->assign ('arElements', $arElements);
		$tpl->assign ('caption', $this->getParam ('caption', 'Favoris'));
		$tpl->assign ('show', $this->getParam ('show', true));
		$pToReturn = $tpl->fetch ($this->getParam ('template', 'heading|admin/headingbookmarks.php'));
		return true;
	}
}