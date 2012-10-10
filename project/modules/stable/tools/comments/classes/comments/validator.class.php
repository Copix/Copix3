<?php
/**
 * Validateur pour CommentsComment
 */
class CommentsValidator extends CopixAbstractValidator {
	/**
	 * Validation des données
	 * 
	 * @param CommentsComment $pObject Comment
	 * @return mixed
	 */
	protected function _validate ($pObject) {
		$errors = array ();

		// Identifiant du groupe
		if ($pObject->getGroup ()->getId () === null) {
			$errors['idGroup'] = _i18n ('comments|comments.validator.idGroup.emptyValue');
		}

		// Site web
		if ($pObject->getWebsite () != null) {
			$validate = _validator ('CopixValidatorURL')->check ($pObject->getWebsite ());
			if ($validate instanceof CopixErrorObject) {
				$errors['website'] = implode (', ', $validate->asArray ());
			}
		}

		// E-mail
		if ($pObject->getEmail () != null) {
			$validate = _validator ('CopixValidatorEMail')->check ($pObject->getEmail ());
			if ($validate instanceof CopixErrorObject) {
				$errors['email'] = implode (', ', $validate->asArray ());
			}
		}

		// Commentaire
		if ($pObject->getComment () == null) {
			$errors['value'] = _i18n ('comments|comments.validator.value.emptyValue');
		}

		// Date et heure
		if ($pObject->getDate () == null) {
			$errors['date'] = _i18n ('comments|comments.validator.date.emptyValue');
		}
		$validate = _validator ('CopixValidatorDate', array ('format' => 'timestamp'))->check ($pObject->getDate ('U'));
		if ($validate instanceof CopixErrorObject) {
			$errors['date'] = implode (', ', $validate->asArray ());
		}

		return (count ($errors) == 0) ? true : $errors;
	}
}