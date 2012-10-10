<?php
/**
 * Informations sur un fichier sauvegardé
 */
class BackupInfosFile {
	/**
	 * Identifiant (nouveau nom généré lors de la sauvegarde)
	 * 
	 * @var string
	 */
	private $_id = null;
	
	/**
	 * Véritable nom du fichier
	 * 
	 * @var string
	 */
	private $_name = null;
	
	/**
	 * Chemin de base d'où a été sauvegardé le fichier
	 * 
	 * @var string
	 */
	private $_path = null;
	
	/**
	 * Taille en bytes
	 * 
	 * @var int
	 */
	private $_size = null;
	
	/**
	 * Constructeur
	 * 
	 * @param string $pId Identifiant (nouveau nom généré lors de la sauvegarde)
	 * @param string $pName Véritable nom du fichier
	 * @param string $pPath Chemin de base d'où a été sauvegardé le fichier
	 * @param int $pSize Taille en bytes
	 */
	public function __construct ($pId, $pName, $pPath, $pSize) {
		$this->_id = $pId;
		$this->_name = $pName;
		$this->_path = $pPath;
		$this->_size = $pSize;
	}
	
	/**
	 * Retourne l'identifiant
	 * 
	 * @return string
	 */
	public function getId () {
		return $this->_id;
	}
	
	/**
	 * Retourne le nom
	 * 
	 * @return string
	 */
	public function getName () {
		return $this->_name;
	}
	
	/**
	 * Retourne le chemin
	 * 
	 * @return string
	 */
	public function getPath () {
		return $this->_path;
	}
	
	/**
	 * Retourne la taille en bytes
	 * 
	 * @return int
	 */
	public function getSize () {
		return $this->_size;
	}
}