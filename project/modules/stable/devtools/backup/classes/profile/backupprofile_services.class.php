<?php
/**
 * Gestion des profils de sauvegarde
 */
class BackupProfileServices {
	/**
	 * Retourne un profil depuis un record
	 *
	 * @param DAORecordBackup_profile $pRecord Record de base
	 * @return BackupProfile
	 */
	private static function _getFromRecord ($pRecord) {
		$toReturn = new BackupProfile ($pRecord->id_profile);
		$toReturn->setIdType ($pRecord->id_type);
		$toReturn->setCaption ($pRecord->caption_profile);
		$toReturn->setFileName ($pRecord->filename_profile);
		$toReturn->setDbProfile ($pRecord->dbprofile_profile);
		$toReturn->setSaveAllTables ($pRecord->savealltables_profile == 1);
		$toReturn->setFilesPath ($pRecord->filesPath_profile);

		// recherche des tables à sauvegarder
		$records = DAObackup_profiles_tables::instance ()->findBy (_daoSP ()->addCondition ('id_profile', '=', $pRecord->id_profile));
		foreach ($records as $record) {
			$toReturn->addTable ($record->name_table);
		}
		
		// recherche des fichiers à sauvegarder
		$records = DAObackup_profiles_files::instance ()->findBy (_daoSP ()->addCondition ('id_profile', '=', $pRecord->id_profile));
		foreach ($records as $record) {
			$toReturn->addFile ($record->path_file, false);
		}

		return $toReturn;
	}

	/**
	 * Retourne un record depuis un objet
	 *
	 * @param BackupProfile $pProfile Profil dont on veut le record
	 * @return DAORecordBackup_profiles
	 */
	private static function _getRecord ($pProfile) {
		$toReturn = new DAORecordBackup_profiles ();
		$toReturn->id_profile = $pProfile->getId ();
		$toReturn->id_type = $pProfile->getIdType ();
		$toReturn->caption_profile = $pProfile->getCaption ();
		$toReturn->filename_profile = $pProfile->getFileName ();
		$toReturn->dbprofile_profile = $pProfile->getDbProfile ();
		$toReturn->savealltables_profile = ($pProfile->saveAllTables ()) ? 1 : 0;
		$toReturn->filesPath_profile = $pProfile->getFilesPath ();
		return $toReturn;
	}

	/**
	 * Retourne la liste des profils
	 *
	 * @return BackupProfile[]
	 */
	public static function getList () {
		$toReturn = array ();
		$records = DAObackup_profiles::instance ()->findBy (_daoSP ()->orderBy ('caption_profile'));
		foreach ($records as $record) {
			$toReturn[$record->id_profile] = self::_getFromRecord ($record);
		}
		return $toReturn;
	}

	/**
	 * Retourne le profil demandé
	 *
	 * @param int $pId Identifiant
	 * @return BackupProfile
	 */
	public static function get ($pId) {
		$record = DAObackup_profiles::instance ()->get ($pId);
		if ($record === false) {
			throw new BackupException ('Le profil de sauvegarde "' . $pId . '" n\'existe pas.');
		}
		return self::_getFromRecord ($record);
	}

	/**
	 * Retourne un profil vierge
	 *
	 * @return BackupProfile
	 */
	public static function create () {
		return new BackupProfile ();
	}

	/**
	 * Sauvegarde le profil
	 *
	 * @param BackupProfile $pProfile Profil à sauvegarder
	 * @return BackupProfile
	 */
	public static function save ($pProfile) {
		// informations de base
		$record = self::_getRecord ($pProfile);
		if ($record->id_profile == null) {
			DAObackup_profiles::instance ()->insert ($record);
		} else {
			DAObackup_profiles::instance ()->update ($record);
		}

		// tables à sauvegarder
		$daoTables = DAObackup_profiles_tables::instance ();
		$daoTables->deleteBy (_daoSP ()->addCondition ('id_profile', '=', $record->id_profile));
		$recordTable = new DAORecordBackup_profiles_tables;
		$recordTable->id_profile = $record->id_profile;
		foreach ($pProfile->getTables () as $table) {
			$recordTable->name_table = $table;
			$daoTables->insert ($recordTable);
		}
		
		// fichiers à sauvegarder
		$daoFiles = DAObackup_profiles_files::instance ();
		$daoFiles->deleteBy (_daoSP ()->addCondition ('id_profile', '=', $record->id_profile));
		$recordFile = new DAORecordBackup_profiles_files;
		$recordFile->id_profile = $record->id_profile;
		foreach ($pProfile->getFiles () as $file) {
			$recordFile->path_file = $file;
			$daoFiles->insert ($recordFile);
		}

		// type de sauvegarde
		BackupTypeServices::save ($pProfile);

		return self::get ($record->id_profile);
	}

	/**
	 * Supprime le profil demandé
	 *
	 * @param int $pId Identifiant
	 */
	public static function delete ($pId) {
		DAObackup_profiles::instance ()->delete ($pId);
		DAObackup_profiles_tables::instance ()->deleteBy (_daoSP ()->addCondition ('id_profile', '=', $pId));
		BackupTypeServices::delete ($pId);
	}
}
