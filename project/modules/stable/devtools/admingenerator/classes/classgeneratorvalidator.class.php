<?php
/**
 * Validateur pour la génération d'une classe
 */
class ClassGeneratorValidator extends CopixAbstractValidator {
	/**
	 * Validation des données
	 *
	 * @param GenerateClass $pObject Objet à valider
	 * @return mixed
	 */
	protected function _validate ($pObject) {
		$errors = array ();

		if (!in_array ($pObject->getModule (), CopixModule::getList ())) {
			$errors['module'] = 'Vous devez indiquer un module qui est installé.';
		}

		if ($pObject->getClassName () == null) {
			$errors['className'] = 'Vous devez indiquer le nom de la classe';
		}

		if (count ($pObject->getProperties ()) == 0) {
			$errors['properties'] = 'Vous devez indiquer au moins une propriété.';
		} else {
			foreach ($pObject->getProperties () as $name => $infos) {
				if ($name == null) {
					$name = 'X';
					$errors['property_' . $name] = 'Vous devez indiquer le nom de la propriété.';
				}
				if (!isset ($infos['comment'])) {
					$errors['property_' . $name . '_comment'] = 'Vous devez indiquer le commentaire de la propriété "' . $name . '".';
				}
				if (!isset ($infos['type'])) {
					$errors['property_' . $name . '_type'] = 'Vous devez indiquer de quel type est la propriété "' . $name . '" (int, string, etc).';
				}

			}
		}

		return (count ($errors) > 0) ? $errors : true;
	}
}