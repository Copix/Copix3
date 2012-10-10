<?php
/**
 * Gestion d'un fichier de log
 */
class LogReaderFile {
	/**
	 * Identifiant, peut être null lors d'un ajout
	 * 
	 * @var int
	 */
	private $_id = null;

	/**
	 * Chemin vers le fichier de log
	 * 
	 * @var string
	 */
	private $_filePath = null;

	/**
	 * Nom du fichier uniquement
	 *
	 * @var string
	 */
	private $_fileName = null;

	/**
	 * Chemin et nom des fichiers de rotation, utiliser * pour l'index de rotation
	 *
	 * @var string
	 */
	private $_rotationFilePath = null;

	/**
	 * Type de log
	 *
	 * @var string
	 */
	private $_type = null;

	/**
	 * Cache des lignes du log
	 *
	 * @var array
	 */
	private $_lines = null;

	/**
	 * Date de la dernière lecture du fichier pour les logs, format timestamp
	 *
	 * @var int
	 */
	private $_lastReadDate = null;

	/**
	 * numéro de la dernière ligne lue
	 *
	 * @var int
	 */
	private $_lastReadLine = null;

	/**
	 * Texte de la première ligne du fichier, lors de la dernière lecture (pour savoir si le fichier a été recréé)
	 *
	 * @var string
	 */
	private $_lastReadFirstLine = null;

	/**
	 * Constructeur
	 *
	 * @param string $pFilePath Chemin du fichier de log
	 * @param int $pId Identifiant, peut être null lors d'un ajout
	 */
	public function __construct ($pId = null) {
		$this->_id = $pId;
	}

	/**
	 * Retourne l'identifiant, peut être null lors d'un ajout
	 *
	 * @return int
	 */
	public function getId () {
		return $this->_id;
	}

	/**
	 * Définit le chemin vers le fichier de log
	 *
	 * @param string $pPath Chemin vers le fichier de log
	 */
	public function setFilePath ($pPath) {
		$this->_filePath = $pPath;
		$this->_fileName = CopixFile::extractFileName ($pPath);
	}

	/**
	 * Retourne le chemin vers le fichier de log
	 *
	 * @return string
	 */
	public function getFilePath () {
		return $this->_filePath;
	}

	/**
	 * Retourne le nom du fichier uniquement
	 *
	 * @return string
	 */
	public function getFileName () {
		return $this->_fileName;
	}

	/**
	 * Définit le chemin des fichiers de rotation, utiliser * pour l'index de la rotation
	 *
	 * @param string $pFilePath Chemin et nom de fichier
	 */
	public function setRotationFilePath ($pFilePath) {
		$this->_rotationFilePath = $pFilePath;
	}

	/**
	 * Retourne le chemin et le nom des fichiers de rotation
	 *
	 * @return string
	 */
	public function getRotationFilePath () {
		return $this->_rotationFilePath;
	}

	/**
	 * Retourne la taille de tous les fichiers de log
	 *
	 * @param boolean $pFormat Indique si on veut formater le retour
	 * @return mixed
	 */
	public function getSize ($pFormat = false) {
		$size = filesize ($this->getFilePath ());
		if ($pFormat) {
			if ($size >= 1024) {
				if ($size >= 1024 * 1024) {
					return round ($size / (1024 * 1024), 1) . ' Mo';
				}
				return round ($size / (1024), 1) . ' Ko';
			}
			return $size . ' o';
		}
		return $size;
	}

	/**
	 * Retourne les lignes demandées
	 *
	 * @param int $pStart Numéro de la 1ère ligne à retourner, null pour partir de la dernière
	 * @param int $pCount Nombre de lignes à retourner, null pour toutes les lignes
	 */
	public function getLines ($pStart, $pCount = null) {
		if ($pCount == null) {
			$pCount = 1000000;
		}
		$lines = $this->_getLines ();
		$linesCount = count ($lines);

		// recherche des infos dans les lignes
		$toReturn = array ();
		$min = max (0, $pStart - 1);
		for ($x = $min; $x < min ($min + $pCount, count ($lines)); $x++) {
			$toReturn[] = call_user_func_array ('LogReaderType' . $this->getType () . '::parse', array ($x + 1, $lines[$x]));
		}
		$toReturn = array_reverse ($toReturn);

		return $toReturn;
	}

	/**
	 * Retourne le nombre de lignes du fichier de log
	 *
	 * @return int
	 */
	public function linesCount () {
		return count ($this->_getLines ());
	}

	/**
	 * Retourne les lignes du fichier de log
	 *
	 * @return array
	 */
	private function _getLines () {
		if ($this->_lines === null) {

			// décompression si besoin
			$filePath = $this->getFilePath ();
			$extractedPath = COPIX_TEMP_PATH . 'modules/logreader/extracted_logs/';
			/**
			.tar.gz tar -xzf fichier
			.tar tar -xf fichier
			.tar.bz2 tar -xjf fichier
			.zip unzip fichier
			.gz gunzip fichier
			 */
			if (substr ($filePath, -3) == '.gz') {
				CopixFile::createDir ($extractedPath);
				$extractedFilePath = $extractedPath . substr ($this->getFileName (), 0, -3);
				exec ('gunzip ' . $filePath . ' -c > ' . $extractedPath . substr ($this->getFileName (), 0, -3));
				$filePath = $extractedFilePath;
			}

			$hwnd = fopen ($filePath, 'r');
			$this->_lines = array ();
			// recherche des lignes
			while (!feof ($hwnd)) {
				$content = fread ($hwnd, 1024);
				$contentExploded = explode ("\n", str_replace ("\r", null, $content));
				if (count ($this->_lines) > 0) {
					$this->_lines[count ($this->_lines) - 1] .= array_shift ($contentExploded);
				}
				$this->_lines = array_merge ($this->_lines, $contentExploded);
			}
			// explode \n nous fait une ligne vide à la fin du tableau
			array_pop ($this->_lines);
		}
		return $this->_lines;
	}

	/**
	 * Définit le type du fichier de log
	 *
	 * @param string $pType Type
	 */
	public function setType ($pType) {
		$this->_type = $pType;
	}

	/**
	 * Retourne le type du fichier de log
	 *
	 * @return string
	 */
	public function getType () {
		return $this->_type;
	}

	/**
	 * Définit la date de dernière lecture
	 *
	 * @param int $pTimestamp Date au format timestamp
	 */
	public function setLastReadDate ($pTimestamp) {
		$this->_lastReadDate = $pTimestamp;
	}

	/**
	 * Retourne la date de dernière lecture
	 *
	 * @param string $pFormat Format de la date, voir la fonction date php, null pour le timestamp
	 * @return mixed
	 */
	public function getLastReadDate ($pFormat = null) {
		return ($pFormat == null) ? $this->_lastReadDate : date ($pFormat, $this->_lastReadDate);
	}

	/**
	 * Définit le numéro de la dernière ligne lue
	 *
	 * @param int $pIndex Numéro de ligne
	 */
	public function setLastReadLine ($pIndex) {
		$this->_lastReadLine = $pIndex;
	}

	/**
	 * Retourne le numéro de la dernière ligne lue
	 *
	 * @return int
	 */
	public function getLastReadLine () {
		return $this->_lastReadLine;
	}

	/**
	 * Définit le texte de la première ligne du fichier, lors de la dernière lecture
	 *
	 * @param string $pLine Texte
	 */
	public function setLastReadFirstLine ($pLine) {
		$this->_lastReadFirstLine = $pLine;
	}

	/**
	 * Retourne le texte de la première ligne du fichier, lors de la dernière lecture
	 *
	 * @return string
	 */
	public function getLastReadFirstLine () {
		return $this->_lastReadFirstLine;
	}

	/**
	 * Retourne les nouvelles lignes depuis la dernière lecture
	 *
	 * @return LogReaderLine[]
	 */
	public function getNewLines () {
		$toReturn = array ();
		
		// la date de modification du fichier a changé
		if (file_exists ($this->getFilePath ()) && filemtime ($this->getFilePath ()) != $this->getLastReadDate ()) {
			$lines = $this->_getLines ();

			// le fichier a été recréé
			if ($this->getLastReadFirstLine () == null || ($this->getLastReadFirstLine () != null && (!isset ($lines[0]) || $lines[0] != $this->getLastReadFirstLine ()))) {
				$toReturn = $this->getLines (1);
			// le fichier a seulement été modifié
			} else {
				$toReturn = $this->getLines ($this->getLastReadLine () + 1);
			}
		}

		return $toReturn;
	}

	/**
	 * Indique si le fichier est valide
	 *
	 * @return mixed
	 */
	public function isValid () {
		return _validator ('LogReaderValidator')->check ($this);
	}
}
