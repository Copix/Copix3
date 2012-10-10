<?php
/**
 * Validateur pour RepositoriesRepository
 */
class RepositoriesValidator extends CopixAbstractValidator {
	/**
	 * Validation des données
	 * 
	 * @param RepositoriesRepository $pObject Repository
	 * @return mixed
	 */
	protected function _validate ($pObject) {
		$errors = array ();

		// Libellé
		if ($pObject->getCaption () == null) {
			$errors['caption'] = _i18n ('mysvn|repositories.validator.caption.emptyValue');
		}

		// Adresse
		if ($pObject->getUrl () == null) {
			$errors['url'] = _i18n ('mysvn|repositories.validator.url.emptyValue');
		}
		$validate = _validator ('CopixValidatorURL')->check ($pObject->getUrl ());
		if ($validate instanceof CopixErrorObject) {
			$errors['url'] = implode (', ', $validate->asArray ());
		}

		return (count ($errors) == 0) ? true : $errors;
	}
}