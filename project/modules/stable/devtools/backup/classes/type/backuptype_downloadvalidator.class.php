<?php
/**
 * Valide les données du type de sauvegarde par téléchargement
 */
class BackupTypeDownloadValidator extends CopixAbstractValidator {
	/**
	 * Valide les données
	 *
	 * @param BackupTypeEMail $pValidate A valider
	 */
	protected function _validate ($pValidate) {
		return true;
	}
}