<?php
/**
 * Configuration spécifique à un type de sauvegarde
 */
abstract class BackupType {
	/**
	 * Identifiant du profil auquel est rattaché ce type
	 * 
	 * @var int
	 */
	private $_idProfile = null;

	/**
	 * Effectue le backup
	 *
	 * @param string $pZipPath Archive de la sauvegarde
	 */
	abstract public function backup ($pZipPath);

	/**
	 * Définit des propriétés depuis un tableau
	 *
	 * @param array $pArray Clef : nom, valeur : valeur
	 */
	abstract public function setFromArray ($pArray);
	
	/**
	 * Charge la configuration spécifique au profil
	 */
	abstract public function load ();
	
	/**
	 * Supprime la configuration spécifique au profil
	 */
	abstract public function delete ();
	
	/**
	 * Sauvegarde la configuration spécifique au profil
	 */
	abstract public function save ();

	/**
	 * Définit l'identifiant du profil auquel est rattaché ce type
	 *
	 * @param int $pId Identifiant du profil
	 */
	public function setIdProfile ($pId) {
		$this->_idProfile = $pId;
	}

	/**
	 * Retourne l'identifiant du profil auquel est rattaché ce type
	 *
	 * @return int
	 */
	public function getIdProfile () {
		return $this->_idProfile;
	}

	/**
	 * Retourne le profil auquel est rattaché ce type
	 *
	 * @return BackupProfile
	 */
	public function getProfile () {
		return BackupProfileServices::get ($this->_idProfile);
	}

	/**
	 * Retourne l'identifiant de type
	 *
	 * @return string
	 */
	public function getId () {
		return strtolower (substr (get_class ($this), 10));
	}

	/**
	 * Retuorne le nom du type
	 *
	 * @return string
	 */
	public function getCaption () {
		$list = BackupTypeServices::getList ();
		return $list[$this->getId ()];
	}

	/**
	 * Indique si le type est valide
	 *
	 * @return mixed
	 */
	public function isValid () {
		return _validator ('BackupType' . $this->getId () . 'Validator')->check ($this);
	}
}