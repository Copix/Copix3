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
 * Gestion des répertoires
 *
 * @package tools
 * @subpackage advancedhelp
 */
class AHelpFoldersServices {
	/**
	 * Retourne un dossier depuis un record
	 *
	 * @param DAORecordAHelp_Folders $pRecord Record
	 * @return AHelpFoldersFolder
	 */
	private static function _getFromRecord ($pRecord) {
		$toReturn = new AHelpFoldersFolder ();
		$toReturn->setId ($pRecord->id_folder);
		$toReturn->setIdParent ($pRecord->id_parent);
		$toReturn->setCaption ($pRecord->caption_folder);
		return $toReturn;
	}

	/**
	 * Retourne un record depuis un dossier
	 *
	 * @param AHelpFoldersFolder $pFolder Dossier
	 * @return DAORecordAHelp_Folders
	 */
	private static function _getRecord ($pFolder) {
		$toReturn = new DAORecordAHelp_Folders ();
		$toReturn->id_folder = $pFolder->getId ();
		$toReturn->id_parent = $pFolder->getIdParent ();
		$toReturn->caption_folder = $pFolder->getCaption ();
		return $toReturn;
	}

	/**
	 * Retourne le dossier demandé
	 *
	 * @param int $pId Identifiant
	 * @return AHelpFoldersFolder
	 */
	public static function get ($pId) {
		$record = DAOAHelp_Folders::instance ()->get ($pId);
		if ($record === false) {
			throw new AHelpException ('Le dossier "' . $pId . '" n\'existe pas.');
		}
		return self::_getFromRecord ($record);
	}

	/**
	 * Retourne la liste des dossiers
	 *
	 * @param int $pIdParent Identifiant du dossier parent, null pour la racine
	 * @return AHelpFoldersFolder[]
	 */
	public static function getList ($pIdParent = null) {
		$toReturn = array ();
		foreach (DAOAHelp_Folders::instance ()->findBy (_daoSP ()->addCondition ('id_parent', '=', $pIdParent)) as $record) {
			$toReturn[] = self::_getFromRecord ($record);
		}
		return $toReturn;
	}

	/**
	 * Ajoute un dossier dans la base de données
	 *
	 * @param AHelpFoldersFolder $pFolder Dossier à ajouter
	 */
	public static function insert ($pFolder) {
		$record = self::_getRecord ($pFolder);
		DAOAHelp_Folders::instance ()->insert ($record);
		$pFolder->setId ($record->id_folder);
	}

	/**
	 * Met à jour un dossier dans la base de données
	 *
	 * @param AHelpFoldersFolder $pFolder Dossier à mettre à jour
	 */
	public static function update ($pFolder) {
		$record = self::_getRecord ($pFolder);
		DAOAHelp_Folders::instance ()->update ($record);
	}
}