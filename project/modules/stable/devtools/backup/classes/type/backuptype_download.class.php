<?php
/**
 * Demande le téléchargement de la sauvegarde
 */
class BackupTypeDownload extends BackupType {
	/**
	 * Effectue la sauvegarde
	 *
	 * @param string $pZipPath Archive de la sauvegarde
	 */
	public function backup ($pZipPath) {
		CopixFile::createDir (COPIX_TEMP_PATH . 'backup/downloads/');
		$id = uniqid ('backup_');
		copy ($pZipPath, COPIX_TEMP_PATH . 'backup/downloads/' . $id . '.zip');
		
		$toReturn = 'Une archive ZIP a été générée avec tous les fichiers de votre sauvegarde.';
		$toReturn .= '<br /><a href="' . _url ('backup|download|', array ('id' => $id)) . '">Télécharger l\'archive ZIP</a>';
		return $toReturn;
	}

	/**
	 * Définit des propriétés depuis un tableau
	 *
	 * @param array $pArray Clef : nom, valeur : valeur
	 */
	public function setFromArray ($pArray) {}
	
	/**
	 * Supprime la configuration spécifique au profil
	 */
	public function delete () {}
	
	/**
	 * Sauvegarde la configuration spécifique au profil
	 */
	public function save () {}
	
	/**
	 * Charge la configuration spécifique au profil
	 */
	public function load () {}
}