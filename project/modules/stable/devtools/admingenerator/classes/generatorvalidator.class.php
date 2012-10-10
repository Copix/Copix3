<?php
/**
 * Validateur pour le générateur d'admin
 */
class GeneratorValidator extends CopixAbstractValidator {
	/**
	 * Validation des données
	 *
	 * @param array $pParams Paramètres
	 * @return mixed
	 */
	protected function _validate ($pParams) {
		$errors = array ();
		$path = CopixModule::getPath ($pParams['moduleName']);

		// traductions
		$i18nPath = $path . 'locales/' . $pParams['i18n'] . '.properties';
		if ($pParams['i18n'] != null && file_exists ($i18nPath) && !is_writable ($i18nPath)) {
			$errors['fr.properties'] = 'Le fichier "' . $i18nPath . '" n\'a pas les droits en écriture.';
		}

		// module.xml
		if (!is_writable ($path . 'module.xml')) {
			$errors['module.xml'] = 'Le fichier "' . $path . 'module.xml" n\'a pas les droits en écriture.';
		}

		// préfixe
		if ($pParams['prefix'] == null) {
			$errors['prefix'] = 'Vous devez indiquer le préfixe';
		}

		// fil d'ariane
		if ($pParams['breadcrumb'] == null) {
			$errors['breadcrumb'] = 'Vous devez indiquer le fil d\'ariane';
		}

		// service
		if ($pParams['service_class'] == null) {
			$errors['service_class'] = 'Vous devez indiquer le nom de la classe du service';
		}

		// classe d'infos
		if ($pParams['info_class'] == null) {
			$errors['info_class'] = 'Vous devez indiquer le nom de la classe d\'informations';
		}

		// validateur
		if ($pParams['validator_class'] == null) {
			$errors['validator_class'] = 'Vous devez indiquer le nom de la classe du validateur';
		}

		// exception
		if ($pParams['exception_class'] == null) {
			$errors['exception_class'] = 'Vous devez indiquer le nom de la classe d\'exception';
		}

		// actiongroup
		if ($pParams['actiongroup_class'] == null) {
			$errors['actiongroup_class'] = 'Vous devez indiquer le nom de la classe de l\'actiongroup';
		}

		// champ de libellé
		if (!isset ($pParams['field_caption']) || $pParams['field_caption'] == null) {
			$errors['field_caption'] = 'Vous devez choisir un champ qui fera office de libellé dans les interfaces d\'administration.';
		}

		// mots-clefs
		$keyWords = array (
			'de_l_element' => 'de l\'élément',
			'des_elements' => 'des éléments',
			'd_un_element' => 'd\'un élément',
			'un_element' => 'un élément',
			'l_element' => 'l\'élément',
			'aucun_element' => 'aucun élément',
			'd_elements' => 'd\'éléments'
		);
		foreach ($keyWords as $id => $caption) {
			if ($pParams[$id] == null) {
				$errors[$id] = 'Vous devez indiquer la traduction du mot-clef "' . $caption . '".';
			}
		}

		// vérification des champs
		$properties = array ();
		$methods = array ();
		$ids = array ();
		foreach ($pParams as $key => $value) {
			if (substr ($key, strlen ($key) - 9) == '_property') {
				// propriété
				if (in_array ($value, $properties)) {
					$errors[$key] = 'La propriété privée "' . $value . '" est utilisée plusieurs fois.';
				} else {
					$properties[] = $value;
				}

				// méthode
				$methodKey = str_replace ('_property', '_method', $key);
				if (in_array ($pParams[$methodKey], $methods)) {
					$errors[$methodKey] = 'Le suffixe de méthode "' . $pParams[$methodKey] . '" est utilisé plusieurs fois.';
				} else {
					$methods[] = $pParams[$methodKey];
				}

				// type
				$typeKey = str_replace ('_property', '_type', $key);
				if ($pParams[$typeKey] == 'id') {
					if ($pParams[$typeKey] == 'id' && in_array ($pParams[$typeKey], $ids)) {
						$errors[$methodKey] = 'Il ne peut y avoir qu\'un seul champ de type Identifiant.';
					} else {
						$ids[] = $pParams[$typeKey];
					}
				}
			}
		}

		return (count ($errors) == 0) ? true : $errors;
	}
}