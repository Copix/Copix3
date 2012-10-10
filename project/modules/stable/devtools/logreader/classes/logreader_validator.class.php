<?php
/**
 * Validation des données
 */
class LogReaderValidator extends CopixAbstractValidator {
	/**
	 * Validation
	 *
	 * @param LogReaderFile $pValidate A valider
	 * @return mixed
	 */
	protected function _validate ($pValidate) {
		$errors = array ();

		if (!file_exists ($pValidate->getFilePath ())) {
			$errors['file'] = 'Le fichier "' . $pValidate->getFilePath () . '" n\'existe pas.';
		}

		return (count ($errors)) ? $errors : true;
	}
}