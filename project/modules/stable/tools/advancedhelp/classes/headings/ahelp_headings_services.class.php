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
 * Gestion des rubriques
 *
 * @package tools
 * @subpackage advancedhelp
 */
class AHelpHeadingsServices {
	/**
	 * Retourne une rubrique depuis un record
	 *
	 * @param DAORecordAHelp_Headings $pRecord Record
	 * @return AHelpHeadingsHeading
	 */
	private static function _getFromRecord ($pRecord) {
		$toReturn = new AHelpHeadingsHeading ();
		$toReturn->setId ($pRecord->id_heading);
		$toReturn->setCaption ($pRecord->caption_heading);
		$toReturn->setDescription ($pRecord->description_heading);
		return $toReturn;
	}

	/**
	 * Retourne un record depuis une rubrique
	 *
	 * @param AHelpHeadingsHeading $pHeading Rubrique
	 * @return DAORecordAHelp_Headings
	 */
	private static function _getRecord ($pHeading) {
		$toReturn = new DAORecordAHelp_Headings ();
		$toReturn->id_heading = $pHeading->getId ();
		$toReturn->caption_heading = $pHeading->getCaption ();
		$toReturn->description_heading = $pHeading->getDescription ();
		return $toReturn;
	}

	/**
	 * Retourne la rubrique demandée
	 *
	 * @param int $pId Identifiant
	 * @return AHelpHeadingsHeading
	 */
	public static function get ($pId) {
		$record = DAOAHelp_Headings::instance ()->get ($pId);
		if ($record === false) {
			throw new AHelpException ('La rubrique "' . $pId . '" n\'existe pas.');
		}
		return self::_getFromRecord ($record);
	}

	/**
	 * Retourne la liste des rubriques
	 *
	 * @return AHelpHeadingsHeading[]
	 */
	public static function getList () {
		$toReturn = array ();
		foreach (DAOAHelp_Folders::instance ()->findAll () as $record) {
			$toReturn[] = self::_getFromRecord ($record);
		}
		return $toReturn;
	}

	/**
	 * Ajoute une rubrique dans la base de données
	 *
	 * @param AHelpHeadingsHeading $pHeading Rubrique à ajouter
	 */
	public static function insert ($pHeading) {
		$record = self::_getRecord ($pHeading);
		DAOAHelp_Headings::instance ()->insert ($record);
		$pHeading->setId ($record->id_heading);
	}

	/**
	 * Met à jour une rubrique dans la base de données
	 *
	 * @param AHelpHeadingsHeading $pHeading Rubrique à mettre à jour
	 */
	public static function update ($pHeading) {
		DAOAHelp_Headings::instance ()->update (self::_getRecord ($pHeading));
	}

	/**
	 * Retourne une rubrique vierge
	 *
	 * @return AHelpHeadingsHeading
	 */
	public static function create () {
		return new AHelpHeadingsHeading ();
	}
}