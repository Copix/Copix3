<?php
/**
 * Effectue une sauvegarde
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Appelée avant toute action
	 *
	 * @param string $pAction Nom de l'action
	 */
	protected function _beforeAction ($pAction) {
		_currentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Effectue un backup
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$ppo = BackupTools::setPage (BackupTools::PAGE_BACKUP);
		$profile = BackupProfileServices::get (_request ('profile'));
		$ppo->backupinfos = $profile->backup ();
		return _arPPO ($ppo, 'backup|backups/admin.backup.php');
	}

	/**
	 * Formulaire pour passer les fichiers pour remonter une sauvegarde
	 *
	 * @return CopixActionReturn
	 */
	public function processRestore () {
		$ppo = BackupTools::setPage (BackupTools::PAGE_RESTORE);
		switch (_request ('error')) {
			case 'ext' : $ppo->errors = 'Seules les archives au format ZIP sont gérées.'; break;
			case 'zip' : $ppo->errors = 'Vous devez envoyer une archive ZIP de maximum ' . ini_get ('upload_max_filesize'); break;
			case 'read' : $ppo->errors = 'Le répertoire ou fichier indiqué n\'est pas valide ou pas lisible.'; break;
			case 'backupxml' : $ppo->errors = 'Le fichier "backup.xml" n\'a pu être trouvé ou n\'est pas lisible.'; break;
		}
		$ppo->path = _request ('path');

		return _arPPO ($ppo, 'backups/admin.restore.php');
	}

	/**
	 * Informations sur une restauration
	 *
	 * @return CopixActionReturn
	 */
	public function processRestoreInfos () {
		$ppo = BackupTools::setPage (BackupTools::PAGE_RESTORE_INFOS);
		$backupPath = COPIX_TEMP_PATH . 'backup/restores/' . uniqid () . '/';
		
		// mode envoi de l'archive zip
		if (_request ('type') == 'upload') {
			$file = CopixUploadedFile::get ('zip');
			// pas de fichier uploadé
			if ($file === false) {
				return _arRedirect (_url ('backup||Restore', array ('error' => 'zip')));
			}
			// extension non gérée
			if (CopixFile::extractFileExt ($file->getName ()) != '.zip') {
				return _arRedirect (_url ('backup||Restore', array ('error' => 'ext')));
			}
			// déplacement du fichier uploadé
			$file->move ($backupPath, 'backup.zip');
			$zip = new CopixZip ();
			$zip->open ($backupPath . 'backup.zip');
			$zip->extractTo ($backupPath);
			
		// mode fichiers sur le serveur
		} else {
			$path = _request ('path');
			if (!is_readable ($path)) {
				return _arRedirect (_url ('backup||Restore', array ('error' => 'read', 'path' => $path)));
			}

			// on a passé une archive zip
			if (is_file ($path)) {
				$file = CopixFile::extractFileName ($path);

				// extension non gérée
				if (CopixFile::extractFileExt ($file) != '.zip') {
					return _arRedirect (_url ('backup||Restore', array ('error' => 'ext', 'path' => $path)));
				}

				$zip = new CopixZip ();
				$zip->open ($path);
				$zip->extractTo ($backupPath);

			// on a passé un répertoire
			} else {
				if (!is_dir ($path) || !is_readable ($path . 'backup.xml')) {
					return _arRedirect (_url ('backup||Restore', array ('error' => 'backupxml', 'path' => $path)));
				}
				$backupPath = $path;
			}
		}

		$ppo->backupxml = $backupPath . 'backup.xml';
		$ppo->backupFilesPath = $backupPath;

		return _arPPO ($ppo, 'backup|backups/admin.restoreinfos.php');
	}

	/**
	 * Effectue la restauration
	 *
	 * @return CopixActionReturn
	 */
	public function processDoRestore () {
		$ppo = BackupTools::setPage (BackupTools::PAGE_DO_RESTORE);
		$backupFilesPath = _request ('backupFilesPath');
		$infos = new BackupInfos ($backupFilesPath . 'backup.xml');
		try {
			$dbProfile = CopixDb::getConnection (_request ('dbProfile'));
		} catch (Exception $e) {
			throw new BackupException ('Vous devez indiquer le profil de connexion à la base de données à utiliser.');
		}
		if (count ($infos->getFiles ()) > 0 && _request ('filesPath') == null) {
			throw new BackupException ('Vous devez indiquer dans quel répertoire eest installé Copix, ce qui servira de chemin relatif pour la copie des fichiers.');
		}
		$ppo->filesPath = _request ('filesPath');

		// restauration des tables
		$ppo->tables = array ();
		if (_request ('restoreTables') != null) {
			$infosTables = $infos->getTables ();
			foreach (CopixRequest::asArray () as $key => $value) {
				if (substr ($key, 0, 6) == 'table_') {
					$table = substr ($key, 6);
					$ppo->tables[$table] = array ('error' => '', 'count' => 0);

					// fichier non trouvé ou non lisible
					$sqlPath = $backupFilesPath . 'tables/' . $table . '.sql';
					if (!is_readable ($backupFilesPath . 'tables/' . $table . '.sql')) {
						$ppo->tables[$table]['error'] = 'Le fichier "' . $sqlPath . '" n\'existe pas ou n\'est pas lisible.';
					}

					// execution du script
					$ppo->tables[$table]['records'] = $infosTables[$table];
					$dbProfile->doSQLScript ($sqlPath);
					try {
						$result = $dbProfile->doQuery ('SELECT COUNT(*) "COUNT" FROM ' . $table);
						$ppo->tables[$table]['count'] = $result[0]->COUNT;
						// nombre d'enregistrements réel différent du nombre théorique
						if ($ppo->tables[$table]['records'] != $ppo->tables[$table]['count']) {
							$ppo->tables[$table]['error'] = 'La restauration aurait du insérer ' . $ppo->tables[$table]['records'] . ' enregistrements, mais la table en compte ' . $ppo->tables[$table]['count'] . '.';
						}
					} catch (Exception $e) {
						$ppo->tables[$table]['error'] = $e->getMessage ();
					}
				}
			}
		}
		
		// restauration des fichiers
		$ppo->files = array ();
		foreach ($infos->getFiles () as $file) {
			CopixFile::createDir ($ppo->filesPath . $file->getPath ());
			copy ($backupFilesPath . 'files/' . $file->getId (), $ppo->filesPath . $file->getPath () . $file->getName ());
			$ppo->files[] = $file;
		}

		return _arPPO ($ppo, 'backup|backups/admin.dorestore.php');
	}
}