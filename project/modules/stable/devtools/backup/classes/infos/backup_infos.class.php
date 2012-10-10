<?php
/**
 * Informations sur un backup
 */
class BackupInfos {
	/**
	 * Identifiant du profil
	 * 
	 * @var int
	 */
	private $_idProfile = null;

	/**
	 * Nom du profil de sauvegarde
	 *
	 * @var string
	 */
	private $_profile = null;

	/**
	 * Adresse appelée lors de la sauvegarde
	 *
	 * @var string
	 */
	private $_url = null;

	/**
	 * Login de l'utilisateur qui a effectué la sauvegarde
	 *
	 * @var string
	 */
	private $_user = null;

	/**
	 * Gestionnaire d'utilisateur de l'utilisateur qui a effectué la sauvegarde
	 *
	 * @var string
	 */
	private $_userHandler = null;

	/**
	 * Date de création du backup, format timestamp
	 * 
	 * @var int
	 */
	private $_date = null;

	/**
	 * Profile de connexion depuis lequel les tables ont été sauvegardées
	 *
	 * @var string
	 */
	private $_dbProfile = null;

	/**
	 * Driver de base de données
	 *
	 * @var string
	 */
	private $_dbDriver = null;

	/**
	 * Tables sauvegardées, clef = nom de la table, valeur = nombre d'enregistrements
	 * 
	 * @var array
	 */
	private $_tables = array ();
	
	/**
	 * Message de confirmation de sauvegarde
	 * 
	 * @var string
	 */
	private $_message = null;
	
	/**
	 * Fichiers sauvegardés
	 */
	private $_files = array ();
	
	/**
	 * Répertoire d'installation de Copix
	 * 
	 * @var string
	 */
	private $_filesPath = null;
	
	/**
	 * Constructeur
	 *
	 * @param string $pXML Fichier XML contenant les infos du backup
	 * @param string $pMessage Message de confirmation de sauvegarde
	 */
	public function __construct ($pXML, $pMessage = null) {
		if (!is_readable ($pXML)) {
			throw new BackupException ('Le fichier "' . $pXML . '" n\'existe pas ou n\'est pas lisible.');
		}
		$xml = simplexml_load_file ($pXML);
		
		$attributes = $xml->general->attributes ();
		$this->_date = (string)$attributes['date'];
		$this->_url = (string)$xml->general->url;

		$this->_user = (string)$xml->general->user;
		$attributes = $xml->general->user->attributes ();
		$this->_userHandler = (string)$attributes['userhandler'];

		$this->_profile = (string)$xml->general->profile;
		$attributes = $xml->general->profile->attributes ();
		$this->_idProfile = (string)$attributes['id'];

		if (isset ($xml->tables)) {
			$attributes = $xml->tables->attributes ();
			$this->_dbProfile = (string)$attributes['profile'];
			$this->_dbDriver = (string)$attributes['driver'];
			foreach ($xml->tables->table as $table) {
				$attributes = $table->attributes ();
				$this->_tables[(string)$table] = (string)$attributes['records'];
			}
		}
		
		if (isset ($xml->files)) {
			$attributes = $xml->files->attributes ();
			$this->_filesPath = (string)$attributes['path'];
			foreach ($xml->files->file as $file) {
				$attributes = $file->attributes ();
				$this->_files[] = new BackupInfosFile ((string)$attributes['id'], (string)$file, (string)$attributes['path'], (string)$attributes['size']);
			}
		}
		
		$this->_message = ($pMessage == null) ? 'Sauvegarde effectuée.' : $pMessage;
	}

	/**
	 * Retourne l'identifiant du profil
	 *
	 * @return int
	 */
	public function getIdProfile () {
		return $this->_idProfile;
	}

	/**
	 * Retourne le nom du profil
	 *
	 * @return string
	 */
	public function getProfile () {
		return $this->_profile;
	}

	/**
	 * Retourne le nom du profile de connexion depuis lequel les tables otn été sauvegardées
	 *
	 * @return string
	 */
	public function getDbProfile () {
		return $this->_dbProfile;
	}

	/**
	 * Retourne les tables sauvegardées, clef = nom de la table, valeur = nombre d'enregistrements
	 * 
	 * @return array
	 */
	public function getTables () {
		return $this->_tables;
	}

	/**
	 * Retourne le nombr ede table ssauvegardées
	 *
	 * @return int
	 */
	public function countTables () {
		return count ($this->_tables);
	}

	/**
	 * Retourne le driver de base de données
	 *
	 * @return string
	 */
	public function getDbDriver () {
		return $this->_dbDriver;
	}

	/**
	 * Retourne la date de création du backup
	 *
	 * @param string $pFormat Format, voir la fonction date () php
	 * @return string
	 */
	public function getDate ($pFormat = null) {
		return ($pFormat == null) ? date (CopixI18N::getDateTimeFormat (), $this->_date) : date ($pFormat, $this->_date);
	}

	/**
	 * Retourne l'adresse appelée pour effectuer le backup
	 *
	 * @return string
	 */
	public function getURL () {
		return $this->_url;
	}

	/**
	 * Retourne le login de l'utilisateur qui a effectué le backup
	 *
	 * @return string
	 */
	public function getUser () {
		return $this->_user;
	}

	/**
	 * Retourne le gestionnaire d'utilisateur de l'utilisateur qui a effectué la sauvegarde
	 *
	 * @return string
	 */
	public function getUserHandler () {
		return $this->_userHandler;
	}
	
	/**
	 * Retourne le message de confirmation de sauvegarde
	 * 
	 * @return string
	 */
	public function getMessage () {
		return $this->_message;
	}
	
	/**
	 * Retourne les informations sur les fichiers sauvegardés
	 * 
	 * @return BackupInfosFile[]
	 */
	public function getFiles () {
		return $this->_files;
	}
	
	/**
	 * Retourne le nombre de fichiers sauvegardés
	 * 
	 * @return int
	 */
	public function countFiles () {
		return count ($this->_files);
	}
	
	/**
	 * Retourne le répertoire d'installation de Copix
	 * 
	 * @return string
	 */
	public function getFilesPath () {
		return $this->_filesPath;
	}
}