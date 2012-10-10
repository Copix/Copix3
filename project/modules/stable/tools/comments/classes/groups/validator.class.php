<?php
/**
 * Validateur pour CommentsGroupsGroup
 */
class CommentsGroupsValidator extends CopixAbstractValidator {
	/**
	 * Validation des données
	 * 
	 * @param CommentsGroupsGroup $pObject Group
	 * @return mixed
	 */
	protected function _validate ($pObject) {
		$errors = array ();

		// id
		if ($pObject->getId () == null) {
			$errors['id'] = 'Vous devez indiquer la valeur de "Identifiant".';
		}

		// Libellé
		if ($pObject->getCaption () == null) {
			$errors['caption'] = _i18n ('comments|commentsgroups.validator.caption.emptyValue');
		}

		return (count ($errors) == 0) ? true : $errors;
	}
}