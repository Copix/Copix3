<?php
/**
 * Validateur d'un mv testing
 */
class MVTestingValidator extends CopixAbstractValidator {
	/**
	 * Valide les données d'un mv testing
	 *
	 * @param stdClass $pMVTesting MV testing à valider
	 */
	protected function _validate ($pMVTesting) {
		$errors = array ();

		if ($pMVTesting->caption_hei == null) {
			$errors['caption_hei'] = 'Vous devez indiquer le nom du MV Testing.';
		}

		if (count ($pMVTesting->elements) < 2) {
			$errors['elements'] = 'Vous devez indiquer au moins 2 éléments à visualiser.';
		}
		$hasPercentErrors = false;
		$percents = 0;
		foreach ($pMVTesting->elements as $index => $element) {
			if ($element->type_element == MVTestingServices::TYPE_CMS && $element->value_element <= 0) {
				$errors['element' . $index . '_cms'] = '[Elément #' . ($index + 1) . '] Vous devez indiquer quel élément du CMS est à visualiser.';
			}
			if ($element->type_element == MVTestingServices::TYPE_MODULE && $element->value_element == null) {
				$errors['element' . $index . '_module'] = '[Elément #' . ($index + 1) . '] Vous devez indiquer le trigramme Copix du module à visualiser.';
			}
			if ($pMVTesting->choice_mvt == MVTestingServices::CHOICE_PERCENT) {
				if (!is_numeric ($element->percent_element)) {
					$errors['element' . $index . '_percent'] = '[Elément #' . ($index + 1) . '] Le pourcentage de visualisation doit être un chiffre valide.';
					$hasPercentErrors = true;
				}
				$percents += $element->percent_element;
			}
		}
		if ($pMVTesting->choice_mvt == MVTestingServices::CHOICE_PERCENT && !$hasPercentErrors && $percents != 100) {
			$errors['elements_percents'] = 'Le pourcentage total des élément pour la visualisation ne fait pas 100.';
		}

		return (count ($errors) == 0) ? true : $errors;
	}
}