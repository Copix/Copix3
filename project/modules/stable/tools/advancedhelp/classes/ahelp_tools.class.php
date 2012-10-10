<?php
/**
 * @package tools
 * @subpackage advancedehelp
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Diverses méthodes pour le module advancedhelp
 *
 * @package tools
 * @subpackage advancedhelp
 */
class AHelpTools {
	public static function breadcrumb ($pIdFolder) {
		$breadcrumb = array (
			'admin||' => 'Administration',
			'advancedhelp|folders|' => 'AdvancedHelp'
		);
		if ($pIdFolder > 0) {
			$breadcrumb2 = array ();
			$folder = AHelpFoldersServices::get ($pIdFolder);
			$breadcrumb2[_url ('advancedhelp|folders|', array ('folder' => $folder->getId ()))] = $folder->getCaption ();
			while ($folder->getIdParent () > 0) {
				$folder = $folder->getParent ();
				$breadcrumb2[_url ('advancedhelp|folders|', array ('folder' => $folder->getId ()))] = $folder->getCaption ();
			}
			$breadcrumb2 = array_reverse ($breadcrumb2);
			$breadcrumb = array_merge ($breadcrumb, $breadcrumb2);
		}

		_notify ('breadcrumb', array ('path' => $breadcrumb));
	}
}