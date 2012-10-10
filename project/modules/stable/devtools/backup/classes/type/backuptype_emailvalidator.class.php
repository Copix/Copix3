<?php
/**
 * Valide les données du type de sauvegarde par email
 */
class BackupTypeEMailValidator extends CopixAbstractValidator {
	/**
	 * Valide les données
	 *
	 * @param BackupTypeEMail $pValidate A valider
	 */
	protected function _validate ($pValidate) {
		$errors = array ();

		try {
			$mails = explode (',', $pValidate->getTo ());
			foreach ($mails as $mail) {
				CopixFormatter::getMail ($mail);
			}
		} catch (Exception $e) {
			$errors[] = 'L\'adresse e-mail du destinataire n\'est pas valide.';
		}

		if ($pValidate->getBCC () != null) {
			try {
				$mails = explode (',', $pValidate->getBCC ());
				foreach ($mails as $mail) {
					CopixFormatter::getMail ($mail);
				}
			} catch (Exception $e) {
				$errors[] = 'L\'adresse e-mail du destinataire caché n\'est pas valide.';
			}
		}

		if ($pValidate->getSubject () == null) {
			$errors[] = 'Vous devez indiquer le sujet du mail.';
		}

		return (count ($errors) == 0) ? true : $errors;
	}
}