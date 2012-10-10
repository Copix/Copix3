<?php
/**
 * Valide les données d'un profil
 */
class BackupProfileValidator extends CopixAbstractValidator {
	/**
	 * Valide les données d'un profil de sauvegarde
	 *
	 * @param BackupProfile $pProfile Profil à valider
	 */
	protected function _validate ($pProfile) {
		$errors = array ();

		if ($pProfile->getCaption () == null) {
			$errors[] = 'Vous devez indiquer le nom du profil.';
		}
		if ($pProfile->getFileName () == null) {
			$errors[] = 'Vous devez indiquer le nom de l\'archive qui contiendra la sauvegarde compressée.';
		}
		if (!in_array ($pProfile->getIdType (), array_keys (BackupTypeServices::getList ()))) {
			$errors[] = 'Vous devez indiquer le type de sauvegarde.';
		} else {
			$errorsType = $pProfile->getType ()->isValid ();
			if ($errorsType instanceof CopixErrorObject) {
				$errors = array_merge ($errors, $errorsType->asArray ());
			}
		}

		if (!in_array ($pProfile->getDbProfile (), CopixConfig::instance ()->copixdb_getProfiles ())) {
			$errors[] = 'Vous devez indiquer un profil de base de données valide.';
		}

		return (count ($errors) == 0) ? true : $errors;
	}
}