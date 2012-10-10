<?php
/**
 * Gestion des logs
 */
class LogReaderServices {
	/**
	 * Retourne un objet avec les infos prises dans le record
	 * 
	 * @param DAORecordLogReader_Files $pRecord Record
	 * @return LogReaderFile
	 */
	private static function _getFromRecord ($pRecord) {
		$toReturn = self::create ($pRecord->id_file);
		$toReturn->setFilePath ($pRecord->path_file);
		$toReturn->setRotationFilePath ($pRecord->rotation_file);
		$toReturn->setType ($pRecord->type_file);
		$toReturn->setLastReadDate ($pRecord->lastread_file);
		$toReturn->setLastReadLine ($pRecord->lastline_file);
		$toReturn->setLastReadFirstLine ($pRecord->lastfirstline_file);
		return $toReturn;
	}

	/**
	 * Retourne un record avec les infos de l'objet
	 *
	 * @param LogReaderFile $pFile Infos sur le fichier de log
	 * @return DAORecordLogReader_Files
	 */
	private static function _getRecord ($pFile) {
		$toReturn = new DAORecordLogReader_Files ();
		$toReturn->id_file = $pFile->getId ();
		$toReturn->path_file = $pFile->getFilePath ();
		$toReturn->rotation_file = $pFile->getRotationFilePath ();
		$toReturn->type_file = $pFile->getType ();
		$toReturn->lastread_file = $pFile->getLastReadDate ();
		$toReturn->lastline_file = $pFile->getLastReadLine ();
		$toReturn->lastfirstline_file = $pFile->getLastReadFirstLine ();
		return $toReturn;
	}

	/**
	 * Retourne la liste des fichiers de log configurés
	 *
	 * @return LogReaderFile[]
	 */
	public static function getList () {
		$toReturn = array ();
		$records = _ioDAO ('logreader_files')->findAll ();
		foreach ($records as $record) {
			$logFile = self::_getFromRecord ($record);
			$toReturn[$logFile->getFileName ()] = clone ($logFile);
		}
		ksort ($toReturn);
		return $toReturn;
	}

	/**
	 * Retourne les infos sur le fichier de log demandé
	 *
	 * @param int $pId Identifiant du fichier configuré
	 * @return LogReaderFile
	 */
	public static function get ($pId) {
		$result = _ioDAO ('logreader_files')->get ($pId);
		if ($result === false) {
			throw new LogReaderException ('Le fichier de log "' . $pId . '" n\'a pu être trouvé.');
		}
		return self::_getFromRecord ($result);
	}

	/**
	 * Retourne les fichiers de rotation
	 *
	 * @param int $pId Identifiant du fichier configuré
	 * @return LogReaderFile[]
	 */
	public static function getRotations ($pId) {
		$file = self::get ($pId);
		$results = CopixFile::glob ($file->getRotationFilePath ());
		$add = self::create ($pId);
		$toReturn = array ();
		foreach ($results as $result) {
			$add->setFilePath ($result);
			$toReturn[] = clone ($add);
		}
		return $toReturn;
	}

	/**
	 * Retourne le fichier de rotation demandé
	 *
	 * @param int $pId Identifiant du fichier configuré
	 * @param string $pFileName Nom du fichier de rotation
	 */
	public static function getRotation ($pId, $pFileName) {
		$toReturn = self::get ($pId);
		$toReturn->setFilePath (CopixFile::extractFilePath ($toReturn->getRotationFilePath ()) . $pFileName);
		return $toReturn;
	}

	/**
	 * Supprime le fichier de log demandé
	 *
	 * @param int $pId Identifiant
	 */
	public static function delete ($pId) {
		_ioDAO ('logreader_files')->delete ($pId);
	}

	/**
	 * Retourne un objet vierge
	 *
	 * @param int $pId Identifiant, peut être null lors d'un ajout
	 * @return LogReaderFile
	 */
	public static function create ($pId = null) {
		return new LogReaderFile ($pId);
	}

	/**
	 * Ajoute le fichier de log
	 *
	 * @param LogReaderFile $pFile Infos sur le fichier de log
	 */
	public static function add ($pFile) {
		$record = self::_getRecord ($pFile);
		_ioDAO ('logreader_files')->insert ($record);
		return self::get ($record->id_file);
	}

	/**
	 * Modifie le fichier de log
	 *
	 * @param LogReaderFile $pFile Infos sur le fichier de log
	 */
	public static function update ($pFile) {
		$record = self::_getRecord ($pFile);
		_ioDAO ('logreader_files')->update ($record);
	}

	/**
	 * Retourne les types de logs gérés
	 *
	 * @return array
	 */
	public static function getTypes () {
		$files = CopixFile::glob (CopixModule::getPath ('logreader') . COPIX_CLASSES_DIR . 'types/logreader_type*.class.php');
		$toReturn = array ();
		foreach ($files as $file) {
			$posFileName = strpos ($file, 'logreader_type');

			// recherche du type
			$type = substr ($file, $posFileName + 14, strpos ($file, '.class.php') - ($posFileName + 14));

			// recherche du nom
			$class = substr ($file, $posFileName, -10);
			$caption = call_user_func (array (str_replace ('_', '', $class), 'getCaption'));

			$toReturn[$type] = $caption;
		}
		return $toReturn;
	}
}