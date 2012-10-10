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
 * Configuration
 * 
 * @package cms
 * @subpackage heading
 */
class ZoneHeadingAdminConfiguration extends CopixZone {
	/**
	 * Identifiant de l'onglet à ouvrir par défaut
	 *
	 * @var string
	 */
	private static $_defaultTab = null;

	/**
	 * Définit l'identifiant de l'onglet à ouvrir par défaut
	 *
	 * @param string $pId
	 */
	public static function setDefaultTab ($pId) {
		self::$_defaultTab = $pId;
	}

	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn Contenu à retourner
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$pToReturn = _tag ('copixzone', array (
			'process' => 'admin|UserPreferences',
			'caption' => 'Configuration',
			'width' => 650,
			'defaultTab' => self::$_defaultTab,
			'onlyDefined' => true,
			'tabs' => true,
			'modulePref' => array ('articles', 'cms_editor', 'document', 'form', 'heading', 'images', 'medias', 'portal', 'uploader', 'cms_mvtesting'),
			'ajaxSave' => false)
		);
		return true;
	}
}