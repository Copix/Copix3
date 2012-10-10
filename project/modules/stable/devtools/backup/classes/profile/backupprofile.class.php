<?php
/**
 * Profil de sauvegarde
 */
class BackupProfile {
	/**
	 * Identifiant, null lors d'un ajout
	 *
	 * @var int
	 */
	private $_id = null;

	/**
	 * Nom
	 *
	 * @var string
	 */
	private $_caption = null;

	/**
	 * Identifiant du type
	 *
	 * @var string
	 */
	private $_idType = null;

	/**
	 * Type de sauvegarde
	 *
	 * @var BackupType
	 */
	private $_type = null;

	/**
	 * Nom de fichier
	 *
	 * @var string
	 */
	private $_fileName = 'backup.zip';

	/**
	 * Profil de connexion à utiliser pour la sauvegarde des tables
	 *
	 * @var string
	 */
	private $_dbProfile = null;

	/**
	 * Tables à sauvegarder
	 *
	 * @var array
	 */
	private $_tables = array ();
	
	/**
	 * Fichiers à sauvegarder
	 * 
	 * @var array
	 */
	private $_files = array ();

	/**
	 * Indique si on doit sauvegarder toutes les tables, ou uniquement celles demandées
	 *
	 * @var boolean
	 */
	private $_saveAllTables = false;
	
	/**
	 * Répertoire d'installation de Copix
	 * 
	 * @var type 
	 */
	private $_filesPath = null;

	/**
	 * Constructeur
	 *
	 * @param string $pId Identifiant, null lors d'un ajout
	 */
	public function __construct ($pId = null) {
		$this->_id = $pId;
		$this->_dbProfile = CopixConfig::instance ()->copixdb_getDefaultProfileName ();
	}

	/**
	 * Retourne l'identifiant
	 *
	 * @return int
	 */
	public function getId () {
		return $this->_id;
	}

	/**
	 * Définit le nom du profil de connexion à utiliser pour la sauvegarde des tables
	 *
	 * @param string $pName Nom du profil
	 */
	public function setDbProfile ($pName) {
		$this->_dbProfile = $pName;
	}

	/**
	 * Retourne le nom du profil de connexion à utiliser pour la sauvegarde des tables
	 *
	 * @return string
	 */
	public function getDbProfile () {
		return $this->_dbProfile;
	}

	/**
	 * Définit si on doit sauvegarder toutes les tables, ou uniquement les tables demandées
	 *
	 * @param boolean $pSaveAll
	 */
	public function setSaveAllTables ($pSaveAll) {
		$this->_saveAllTables = $pSaveAll;
	}

	/**
	 * Indique si on doit sauvegarder toutes les tables, ou uniquement les tables demandées
	 *
	 * @return boolean
	 */
	public function saveAllTables () {
		return $this->_saveAllTables;
	}

	/**
	 * Définit le nom
	 *
	 * @param string $pCaption Nom
	 */
	public function setCaption ($pCaption) {
		$this->_caption = $pCaption;
	}

	/**
	 * Retourne le nom
	 *
	 * @return string
	 */
	public function getCaption () {
		return $this->_caption;
	}

	/**
	 * Retourne le type
	 *
	 * @return BackupType
	 */
	public function getType () {
		return $this->_type;
	}

	/**
	 * Définit l'identifiant du type
	 *
	 * @param string $pType Identifiant du type
	 */
	public function setIdType ($pType) {
		if (!in_array ($pType, array_keys (BackupTypeServices::getList ()))) {
			throw new BackupException ('Le type "' . $pType . '" est inconnu.');
		}
		$this->_idType = $pType;
		$this->_type = BackupTypeServices::get ($pType, $this->_id);
	}

	/**
	 * Retourne l'identifiant du type
	 *
	 * @return string
	 */
	public function getIdType () {
		return $this->_idType;
	}

	/**
	 * Définit le nom du fichier
	 *
	 * @param string $pFileName Nom du fichier uniquement
	 */
	public function setFileName ($pFileName) {
		$this->_fileName = $pFileName;
	}

	/**
	 * Retourne le nom du fichier
	 *
	 * @return string
	 */
	public function getFileName () {
		return $this->_fileName;
	}

	/**
	 * Ajoute une table à sauvegarder
	 *
	 * @param string $pName Nom de la table
	 */
	public function addTable ($pName) {
		$this->_tables[$pName] = true;
	}

	/**
	 * Retourne la liste des table sà sauvegarder
	 *
	 * @return array
	 */
	public function getTables () {
		return array_keys ($this->_tables);
	}

	/**
	 * Supprime toutes les tables à sauvegarder
	 */
	public function clearTables () {
		$this->_tables = array ();
	}

	/**
	 * Effectue la sauvegarde, et retourne des infos sur le backup
	 *
	 * @return BackupInfos
	 */
	public function backup () {
		if ($this->isValid () !== true) {
			throw new BackupException ('Le profil de sauvegarde n\'est pas valide.');
		}

		$backupDir = COPIX_TEMP_PATH . 'backup/backups/' . uniqid () . '/';
		$backupFiles = array ();
		$justFiles = array ();

		$dom = new DomImplementation ();
		$type = $dom->createDocumentType ('backup');
		$doc = $dom->createDocument ('', '', $type);
 		$doc->encoding = 'UTF-8';
		$mainElement = $doc->createElement ('backup');
		$node = $doc->appendChild ($mainElement);

		$element = $doc->createElement ('general');
		$element->setAttribute ('date', time ());
		$nodeGeneral = $node->appendChild ($element);

		$element = $doc->createElement ('url', CopixURL::getRequestedUrl (true));
		$nodeGeneral->appendChild ($element);
		$element = $doc->createElement ('user', _currentUser ()->getLogin ());
		$node = $nodeGeneral->appendChild ($element);
		$node->setAttribute ('userhandler', _currentUser ()->getHandler ());
		$element = $doc->createElement ('profile', $this->getCaption ());
		$node = $nodeGeneral->appendChild ($element);
		$node->setAttribute ('id', $this->getId ());

		// sauvegarde de la base
		if ($this->saveAllTables () || count ($this->getTables ()) > 0) {
			$connection = CopixDb::getConnection ($this->getDbProfile ());
			$dbProfile = CopixConfig::instance ()->copixdb_getProfile ($this->getDbProfile ());

			$elementTables = $doc->createElement ('tables');
			$nodeTables = $mainElement->appendChild ($elementTables);
			$nodeTables->setAttribute ('profile', $this->getDbProfile ());
			$nodeTables->setAttribute ('driver', $dbProfile->getDriverName ());
			
			$dbBackupDir = $backupDir . 'tables/';
			CopixFile::createDir ($dbBackupDir);

			if ($dbProfile->getDatabase () != 'mysql') {
				throw new BackupException ('Le type de base de données "' . $dbProfile->getDatabase () . '" n\'est pas géré.');
			}

			$tables = ($this->saveAllTables ()) ? $connection->getTableList () : $this->getTables ();
			foreach ($tables as $table) {
				$dao = _ioDAO ($table, $this->getDbProfile ());
				$handle = fopen ($dbBackupDir . $table . '.sql', 'w');
				$backupFiles[] = $dbBackupDir . $table . '.sql';

				// création de la table
				$query = 'SHOW CREATE TABLE ' . $table;
				$result = $connection->doQuery ($query);
				fwrite ($handle, '# ----------------------------------------------------------------------- ' . "\n");
				fwrite ($handle, '# CREATION DE LA TABLE ' . $table . "\n");
				fwrite ($handle, '# ----------------------------------------------------------------------- ' . "\n\n");
				fwrite ($handle, 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n");
				
				// les contraintes d'intégrité ne sont pas gérées
				$lines = array ();
				foreach (explode ("\n", $result[0]->{'Create Table'}) as $line) {
					if (substr (ltrim ($line), 0, 11) == 'CONSTRAINT ') {
						// suppression de la virgule de la ligne avant la contrainte
						if (substr ($lines[count ($lines) - 1], -1) == ',') {
							$lines[count ($lines) - 1] = substr ($lines[count ($lines) - 1], 0, -1);
						}
					} else {
						$lines[] = $line;
					}
				}
				fwrite ($handle, implode ("\n", $lines) . ';' . "\n\n\n");

				// contenu de la table
				$count = $dao->countBy (_daoSP ());
				fwrite ($handle, '# ----------------------------------------------------------------------- ' . "\n");
				fwrite ($handle, '# CONTENU DE LA TABLE ' . $table . ' (' . $count . ' enregistrements)' . "\n");
				fwrite ($handle, '# ----------------------------------------------------------------------- ');
				
				for ($x = 0; $x < $count / 100; $x++) {
					$values = array ();
					$results = $dao->findBy (_daoSP ()->setLimit ($x * 100, 100));
					foreach ($results as $result) {
						$thisValue = array ();
						foreach ($result as $value) {
							if ($value === null) {
								$thisValue[] = 'NULL';
							} else {
								$thisValue[] = "'" . mysql_real_escape_string ($value) . "'";
							}
						}
						$values[] = '(' . implode (', ', $thisValue) . ')';
					}
					fwrite ($handle, "\n\n" . '# Enregistrements ' . ($x * 100 + 1) . ' à ' . (min ($count, ($x + 1) * 100)) . "\n");
					fwrite ($handle, 'INSERT INTO `' . $table . '` VALUES' . "\n  " . implode (",\n  ", $values) . ';');
				}

				fclose ($handle);

				$elementTable = $doc->createElement ('table', $table);
				$nodeTable = $nodeTables->appendChild ($elementTable);
				$nodeTable->setAttribute ('records', $count);
			}
		}
		
		// sauvegarde des fichiers
		if (count ($this->getFiles ()) > 0) {
			$elementFiles = $doc->createElement ('files');
			$nodeFiles = $mainElement->appendChild ($elementFiles);
			$nodeFiles->setAttribute ('path', $this->getFilesPath ());
			
			foreach ($this->getFiles () as $file) {
				$filePath = $this->getFilesPath () . $file;
				if (is_dir ($filePath)) {
					foreach (CopixFile::search ('*', $filePath) as $globFile) {
						if (is_file ($globFile)) {
							if (($id = $this->_saveFile ($globFile, $doc, $nodeFiles)) !== false) {
								$backupFiles[] = $globFile;
								$justFiles[$id] = $globFile;
							}
						}
					}
				} else {
					if (($id = $this->_saveFile ($filePath, $doc, $nodeFiles)) !== false) {
						$backupFiles[] = $filePath;
						$justFiles[$id] = $filePath;
					}
				}
			}
		}

		if (!CopixFile::write ($backupDir . 'backup.xml', $doc->saveXML ())) {
			throw new BackupException ('Erreur lors de la création du fichier "' . $backupDir . 'backup.xml".');
		}
		$backupFiles[] = $backupDir . 'backup.xml';
		
		// compression de la sauvegarde en ZIP		
		$zip = new CopixZip ($backupDir . 'backup.zip');
		if ($this->saveAllTables () || count ($this->getTables ()) > 0) {
			if (!$zip->addDirectory ($backupDir . 'tables/')) {
				throw new BackupException ('Erreur lors de la compression en ZIP.');
			}
		}
		foreach ($justFiles as $id => $file) {
			$zip->addFile ($file, 'files/' . $id);
		}
		$zip->addFile ($backupDir . 'backup.xml', 'backup.xml');
		$zip->close ();

		$message = $this->getType ()->backup ($backupDir . 'backup.zip');

		$toReturn = new BackupInfos ($backupDir . 'backup.xml', $message);
		CopixFile::removeDir ($backupDir);
		return $toReturn;
	}
	
	/**
	 * Effectue la sauvegarde d'un fichier
	 * 
	 * @param string $pFile Chemin complet + nom du fichier
	 * @param DocDocument $pDoc Document XML backup.Xml
	 * @param DomNode $pNode Node dans laquelle rajouter les infos sur le fichier
	 * @return int
	 */
	private function _saveFile ($pFile, $pDoc, $pNode) {
		if (!file_exists ($pFile)) {
			return false;
		}
		$elementTable = $pDoc->createElement ('file', CopixFile::extractFileName ($pFile));
		$nodeFile = $pNode->appendChild ($elementTable);
		$nodeFile->setAttribute ('path', CopixFile::extractFilePath (substr ($pFile, strlen ($this->getFilesPath ()))));
		$nodeFile->setAttribute ('size', filesize ($pFile));
		$id = uniqid ('file_');
		$nodeFile->setAttribute ('id', $id);
		return $id;
	}
	
	/**
	 * Définit le répertoire d'installation de Copix
	 * 
	 * @param string $pPath 
	 */
	public function setFilesPath ($pPath) {
		$this->_filesPath = CopixFile::getRealPath ($pPath);
	}
	
	/**
	 * Retourne le répertoire d'installation de Copix
	 * 
	 * @return string
	 */
	public function getFilesPath () {
		return $this->_filesPath;
	}
	
	/**
	 * Ajoute un fichier à sauvegarder
	 * 
	 * @param string $pPath 
	 * @param boolean $pParse Indique si on doit trouver le chemin relatif à filesPath, ou si c'est déja fait dans $pPath
	 */
	public function addFile ($pPath, $pParse = true) {
		if ($pParse && $this->_filesPath == substr ($pPath, 0, strlen ($this->_filesPath))) {
			$this->_files[] = substr ($pPath, strlen ($this->_filesPath));
		} else {
			$this->_files[] = $pPath;
		}
	}
	
	/**
	 * Supprime tous les fichiers à sauvegarder
	 */
	public function clearFiles () {
		$this->_files = array ();
	}
	
	/**
	 * Retourne la liste des fichiers à sauvegarder
	 * 
	 * @return array
	 */
	public function getFiles () {
		return $this->_files;
	}

	/**
	 * Indique si le profil est valide
	 *
	 * @return mixed
	 */
	public function isValid () {
		return _validator ('BackupProfileValidator')->check ($this);
	}
}