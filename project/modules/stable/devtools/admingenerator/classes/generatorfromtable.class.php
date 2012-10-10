<?php
class GeneratorFromTable {
	/**
	 * Diverses informations stockées pour faciliter leur accès dans les méthodes
	 */
	private $_params = array ();
	private $_path = null;
	private $_prefix = null;
	private $_prefixLower = null;
	private $_infoClass = null;
	private $_infoClassLower = null;
	private $_fullClass = null;
	private $_fullService = null;
	private $_table = null;
	private $_profile = null;
	private $_fields = array ();
	private $_actiongroup = null;
	private $_actiongroupLower = null;
	private $_module = null;
	private $_trigramme = null;
	private $_fullException = null;
	private $_searchClass = null;
	private $_searchClassLower = null;
	private $_fieldId = null;
	private $_fieldCaption = null;
	private $_fieldPosition = null;
	private $_fieldStatus = null;
	private $_fieldsSearch = array ();
	private $_dao = null;
	private $_countEditable = 0;
	private $_keyWords = array ();
	private $_linksButtons = false;

	/**
	 * Retourne les champs d'une table, avec des infos supplémentaires pré remplies
	 *
	 * @param string $pProfile Profil de connexion
	 * @param string $pTable Nom de la table
	 * @return array
	 */
	public static function getFields ($pProfile, $pTable) {
		$toReturn = CopixDB::getConnection ($pProfile)->getFieldList ($pTable);
		foreach ($toReturn as $field) {
			$field->__COPIX__EXTRAS__ = new stdClass ();

			// clef primaire
			if (isset ($field->pk) && $field->pk) {
				$field->__COPIX__EXTRAS__->isEditable = false;
				$field->__COPIX__EXTRAS__->type = 'id';
			// champ standard
			} else {
				$field->__COPIX__EXTRAS__->isEditable = true;
			}

			// propriété
			$field->__COPIX__EXTRAS__->property = (strpos ($field->name, '_') === false) ? strtolower ($field->name) : strtolower (substr ($field->name, 0, strrpos ($field->name, '_')));

			// identifiant
			if ($field->__COPIX__EXTRAS__->property == 'id') {
				$field->caption = 'Identifiant';
				$field->__COPIX__EXTRAS__->type = 'id';

			// libellé
			} else if ($field->__COPIX__EXTRAS__->property == 'caption') {
				$field->__COPIX__EXTRAS__->type = 'varchar';
				$field->caption = 'Libellé';

			// status
			} else if ($field->__COPIX__EXTRAS__->property == 'enabled') {
				$field->__COPIX__EXTRAS__->type = 'status';
				$field->caption = 'Statut';

			// position
			} else if (in_array ($field->__COPIX__EXTRAS__->property, array ('order', 'position'))) {
				$field->__COPIX__EXTRAS__->type = 'position';
				$field->__COPIX__EXTRAS__->isEditable = false;
				$field->caption = 'Ordre de tri';

			// description
			} else if ($field->__COPIX__EXTRAS__->property == 'description') {
				$field->__COPIX__EXTRAS__->type = 'string';
				$field->caption = 'Description';

			// url
			} else if ($field->__COPIX__EXTRAS__->property == 'url' || $field->__COPIX__EXTRAS__->property == 'site' || $field->__COPIX__EXTRAS__->property == 'website') {
				$field->__COPIX__EXTRAS__->type = 'url';
				$field->caption = 'Adresse';

			// email
			} else if ($field->__COPIX__EXTRAS__->property == 'email' || $field->__COPIX__EXTRAS__->property == 'mail') {
				$field->__COPIX__EXTRAS__->type = 'email';
				$field->caption = 'E-mail';

			// login
			} else if ($field->__COPIX__EXTRAS__->property == 'login') {
				$field->__COPIX__EXTRAS__->type = 'string';
				$field->caption = 'Identifiant';

			// mot de passe
			} else if ($field->__COPIX__EXTRAS__->property == 'password') {
				$field->__COPIX__EXTRAS__->type = 'string';
				$field->caption = 'Mot de passe';

			// état
			} else if ($field->__COPIX__EXTRAS__->property == 'status' || $field->__COPIX__EXTRAS__->property == 'state') {
				$field->__COPIX__EXTRAS__->type = 'int';
				$field->caption = 'Etat';

			// date
			} else if ($field->type == 'date') {
				$field->__COPIX__EXTRAS__->type = 'date';
				$field->caption = 'Date';

			// datetime
			} else if ($field->type == 'datetime') {
				$field->__COPIX__EXTRAS__->type = 'datetime';
				$field->caption = 'Date et heure';

			// time
			} else if ($field->type == 'time') {
				$field->__COPIX__EXTRAS__->type = 'time';
				$field->caption = 'Heure';

			// string
			} else if ($field->type == 'string') {
				$field->__COPIX__EXTRAS__->type = 'string';

			// int
			} else if ($field->type == 'int') {
				$field->__COPIX__EXTRAS__->type = 'int';

			// autres
			} else {
				$field->__COPIX__EXTRAS__->type = 'varchar';
			}

			if ($field->name == $field->caption) {
				$field->caption = null;
			}

			$field->__COPIX__EXTRAS__->method = str_replace (' ', null, ucwords (str_replace ('_', ' ', $field->__COPIX__EXTRAS__->property)));
		}
		return $toReturn;
	}

	/**
	 * Constructeur
	 *
	 * @param $pParams Tous les paramètres de génération
	 */
	public function __construct ($pParams) {
		$this->_params = $pParams;
		$this->_path = CopixModule::getPath ($pParams['moduleName']);
		$this->_prefix = ucfirst ($pParams['prefix']);
		$this->_prefixLower = strtolower ($this->_prefix);
		$this->_infoClass = $pParams['info_class'];
		$this->_infoClassLower = strtolower ($this->_infoClass);
		$this->_fullClass = $this->_prefix . $this->_infoClass;
		$this->_fullService = $this->_prefix . $pParams['service_class'];
		$this->_table = $pParams['table'];
		$this->_profile = $pParams['profile'];
		$this->_fields = CopixDB::getConnection ($this->_profile)->getFieldList ($this->_table);
		$this->_actiongroup = $pParams['actiongroup_class'];
		$this->_actiongroupLower = strtolower ($this->_actiongroup);
		$this->_module = $pParams['moduleName'];
		$this->_trigramme = $this->_module . '|' . $this->_actiongroupLower . '|';
		$this->_dao = 'DAO' . $this->_table . '::instance (self::$_dbProfile)';
		$this->_fullException = $this->_prefix . $this->_params['exception_class'];
		$this->_searchClass = $pParams['search_class'];
		$this->_searchClassLower = strtolower ($this->_searchClass);
		$this->_menus = ($pParams['menus'] == 'yes');
		$this->_linksButtons = ($pParams['linksButtons'] == 'yes');

		// mots-clefs
		$this->_keyWords['de_l_element'] = $pParams['de_l_element'];
		$this->_keyWords['des_elements'] = $pParams['des_elements'];
		$this->_keyWords['d_un_element'] = $pParams['d_un_element'];
		$this->_keyWords['un_element'] = $pParams['un_element'];
		$this->_keyWords['l_element'] = $pParams['l_element'];
		$this->_keyWords['aucun_element'] = $pParams['aucun_element'];
		$this->_keyWords['d_elements'] = $pParams['d_elements'];

		// création des extras
		$phpTypesInt = array ('autoincrement', 'bigautoincrement', 'int', 'numeric');
		$phpTypesFloat = array ('float');
		$this->_fieldId = null;
		$this->_fieldCaption = null;
		$this->_fieldPosition = null;
		$this->_fieldStatus = null;
		foreach ($this->_fields as $field) {
			// informations spécifiques
			$field->__COPIX__EXTRAS__ = new stdClass ();
			$field->__COPIX__EXTRAS__->property = (isset ($pParams['field_' . $field->name . '_property'])) ? $pParams['field_' . $field->name . '_property'] : null;
			$field->__COPIX__EXTRAS__->type = $pParams['field_' . $field->name . '_type'];
			if (isset ($pParams['field_' . $field->name . '_editable'])) {
				$field->__COPIX__EXTRAS__->isEditable = true;
				$this->_countEditable++;
			} else {
				$field->__COPIX__EXTRAS__->isEditable = false;
			}
			$field->__COPIX__EXTRAS__->list = (isset ($pParams['field_' . $field->name . '_list']));
			$field->__COPIX__EXTRAS__->caption = $pParams['field_' . $field->name . '_caption'];
			$field->__COPIX__EXTRAS__->isDate = in_array ($field->__COPIX__EXTRAS__->type, array ('date', 'time', 'datetime'));
			if (isset ($pParams['field_' . $field->name . '_searchable']) || $field->__COPIX__EXTRAS__->type == 'status') {
				$this->_fieldsSearch[] = $field;
			}

			// recherche des champs spéciaux
			if ($field->__COPIX__EXTRAS__->type == 'id') {
				$this->_fieldId = $field;
			}
			if ($pParams['field_caption'] == $field->name) {
				$this->_fieldCaption = $field;
			}
			if ($field->__COPIX__EXTRAS__->type == 'position') {
				$this->_fieldPosition = $field;
			}
			if ($field->__COPIX__EXTRAS__->type == 'status') {
				$this->_fieldStatus = $field;
			}

			// recherche du type PHP de la propriété
			if (in_array ($field->type, $phpTypesInt)) {
				$field->__COPIX__EXTRAS__->phpType = 'int';
			} else if (in_array ($field->type, $phpTypesFloat)) {
				$field->__COPIX__EXTRAS__->phpType = 'float';
			} else {
				$field->__COPIX__EXTRAS__->phpType = 'string';
			}
			if ($field->__COPIX__EXTRAS__->type == 'boolean' || $field->__COPIX__EXTRAS__->type == 'status') {
				$field->__COPIX__EXTRAS__->phpType = 'boolean';
			}

			// type status
			if ($field->__COPIX__EXTRAS__->type == 'status') {
				$field->__COPIX__EXTRAS__->settor = 'setIsEnabled';
				$field->__COPIX__EXTRAS__->gettor = 'isEnabled';
				$field->__COPIX__EXTRAS__->property = 'isEnabled';

			// type position
			} else if ($field->__COPIX__EXTRAS__->type == 'position') {
				$field->__COPIX__EXTRAS__->settor = 'setPosition';
				$field->__COPIX__EXTRAS__->gettor = 'getPosition';
				$field->__COPIX__EXTRAS__->property = 'position';

			// type boolean
			} else if ($field->__COPIX__EXTRAS__->phpType == 'boolean') {
				$field->__COPIX__EXTRAS__->settor = 'setIs' . $pParams['field_' . $field->name . '_method'];
				$field->__COPIX__EXTRAS__->gettor = 'is' . $pParams['field_' . $field->name . '_method'];

			// tous les autres types
			} else {
				$field->__COPIX__EXTRAS__->settor = 'set' . $pParams['field_' . $field->name . '_method'];
				$field->__COPIX__EXTRAS__->gettor = 'get' . $pParams['field_' . $field->name . '_method'];
			}
		}
	}

	/**
	 * Retourne le chemin des classes
	 *
	 * @return string
	 */
	private function _getClassesPath () {
		return $this->_path . 'classes/' . $this->_prefixLower . '/';
	}

	/**
	 * Retourne le chemin des actiongroups
	 *
	 * @return string
	 */
	private function _getActiongroupsPath () {
		return $this->_path . 'actiongroups/';
	}

	/**
	 * Retourne le chemin des templates
	 *
	 * @return string
	 */
	private function _getTemplatesPath () {
		return $this->_path . 'templates/' . $this->_prefixLower . '/';
	}

	/**
	 * Retourne le chemin des zones
	 *
	 * @return string
	 */
	private function _getZonesPath () {
		return $this->_path . 'zones/';
	}

	/**
	 * Retourne le code PHP pour un setteur
	 *
	 * @param stdClass $pField Informations sur le champ
	 * @param string $pProperty Nom de la propriété
	 * @param string $pMethod Nom de la méthode
	 * @param boolean $pReturnThis Indique si on doit retourner $this ou rien
	 * @return string
	 */
	private function _getPHP4Settor ($pField, $pProperty, $pMethod, $pReturnThis = false) {
		$php = new CopixPHPGenerator ();

		// boolean
		if ($pField->__COPIX__EXTRAS__->phpType == 'boolean') {
			$param = '$pValue';
			$comment = $php->getPHPDoc (array ('Définit la valeur de ' . $pField->__COPIX__EXTRAS__->caption, null, '@param ' . $pField->__COPIX__EXTRAS__->phpType . ' ' . $param . ' Valeur'), 1);
			$set = $php->getLine ('if (' . $param . ' === null || ' . $param . ' == \'\') {', 2);
			$set .= $php->getLine ('$this->_' . $pProperty . ' = null;', 3);
			$set .= $php->getLine ('} else {', 2);
			$set .= $php->getLine ('$this->_' . $pProperty . ' = _filter (\'boolean\')->get (' . $param . ');', 3);
			$set .= $php->getLine ('}', 2);

		// date
		} else if ($pField->__COPIX__EXTRAS__->type == 'date') {
			$param = '$pDate';
			$set = $php->getLine ('$this->_' . $pProperty . ' = CopixDateTime::yyyymmddToTimestamp (' . $param . ');', 2);
			$comment = $php->getPHPDoc (array ('Définit la valeur de ' . $pField->__COPIX__EXTRAS__->caption, null, '@param ' . $pField->__COPIX__EXTRAS__->phpType . ' ' . $param . ' Date au format yyyymmdd'), 1);

		// time
		} else if ($pField->__COPIX__EXTRAS__->type == 'time') {
			$param = '$pTime';
			$comment = $php->getPHPDoc (array ('Définit la valeur de ' . $pField->__COPIX__EXTRAS__->caption, null, '@param ' . $pField->__COPIX__EXTRAS__->phpType . ' ' . $param . ' Heure au format hhiiss'), 1);
			$set = $php->getLine ('$this->_' . $pProperty . ' = CopixDateTime::hhiissToTimestamp (' . $param . ');', 2);

		// datetime
		} else if ($pField->__COPIX__EXTRAS__->type == 'datetime') {
			$param = '$pDateTime';
			$comment = $php->getPHPDoc (array ('Définit la valeur de ' . $pField->__COPIX__EXTRAS__->caption, null, '@param ' . $pField->__COPIX__EXTRAS__->phpType . ' ' . $param . ' Date et heure au format yyyymmddhhiiss'), 1);
			$set = $php->getLine ('$this->_' . $pProperty . ' = CopixDateTime::yyyymmddhhiissToTimestamp (' . $param . ');', 2);

		// float
		} else if ($pField->__COPIX__EXTRAS__->phpType == 'float') {
			$param = '$pFloat';
			$comment = $php->getPHPDoc (array ('Définit la valeur de ' . $pField->__COPIX__EXTRAS__->caption, null, '@param ' . $pField->__COPIX__EXTRAS__->phpType . ' ' . $param . ' Valeur'), 1);
			$set = $php->getLine ('$this->_' . $pProperty . ' = (' . $param . ' === null) ? null : floatval (str_replace (\',\', \'.\', ' . $param . '));', 2);

		// autres
		} else {
			$param = '$pValue';
			$comment = $php->getPHPDoc (array ('Définit la valeur de ' . $pField->__COPIX__EXTRAS__->caption, null, '@param ' . $pField->__COPIX__EXTRAS__->phpType . ' ' . $param . ' Valeur'), 1);
			$set = $php->getLine ('$this->_' . $pProperty . ' = ' . $param . ';', 2);
		}


		$toReturn = $comment;
		$toReturn .= $php->getLine ('public function ' . $pMethod . ' (' . $param . ') {', 1);
		$toReturn .= $set;
		if ($pReturnThis) {
			$toReturn .= $php->getLine ('return $this;', 2);
		}
		$toReturn .= $php->getLine ('}', 1, 2);

		return $toReturn;
	}

	/**
	 * Retourne le code PHP pour un getteur
	 *
	 * @param stdClass $pField Informations sur le champ
	 * @param string $pProperty Nom de la propriété
	 * @param string $pMethod Nom de la méthode
	 * @return string
	 */
	private function _getPHP4Gettor ($pField, $pProperty, $pMethod) {
		$php = new CopixPHPGenerator ();

		// date
		if ($pField->__COPIX__EXTRAS__->isDate) {
			$toReturn = $php->getPHPDoc (array ('Retourne la valeur de ' . $pField->__COPIX__EXTRAS__->caption, null, '@param string $pFormat Format de retour, null pour le format de la langue courante', '@return ' . $pField->__COPIX__EXTRAS__->phpType), 1);
			$toReturn .= $php->getLine ('public function ' . $pMethod . ' ($pFormat = null) {', 1);
			$toReturn .= $php->getLine ('if ($this->_' . $pProperty . ' == null) {', 2);
			$toReturn .= $php->getLine ('return null;', 3);
			$toReturn .= $php->getLine ('}', 2);
			$toReturn .= $php->getLine ('if ($pFormat == null) {', 2);
			if ($pField->__COPIX__EXTRAS__->type == 'date') {
				$toReturn .= $php->getLine ('$pFormat = CopixI18N::getDateFormat ();', 3);
			} else if ($pField->__COPIX__EXTRAS__->type == 'datetime') {
				$toReturn .= $php->getLine ('$pFormat = CopixI18N::getDateTimeFormat ();', 3);
			} else {
				$toReturn .= $php->getLine ('$pFormat = \'H:i:s\';', 3);
			}
			$toReturn .= $php->getLine ('}', 2);
			$toReturn .= $php->getLine ('return date ($pFormat, $this->_' . $pProperty . ');', 2);
			$toReturn .= $php->getLine ('}', 1, 2);

		// int
		} else if ($pField->__COPIX__EXTRAS__->phpType == 'int') {
			$toReturn = $php->getPHPDoc (array ('Retourne la valeur de ' . $pField->__COPIX__EXTRAS__->caption, null, '@return ' . $pField->__COPIX__EXTRAS__->phpType), 1);
			$toReturn .= $php->getLine ('public function ' . $pMethod . ' () {', 1);
			$toReturn .= $php->getLine ('return $this->_' . $pProperty . ';', 2);
			$toReturn .= $php->getLine ('}', 1, 2);
			 
		// float
		} else if ($pField->__COPIX__EXTRAS__->phpType == 'float') {
			$toReturn = $php->getPHPDoc (array ('Retourne la valeur de ' . $pField->__COPIX__EXTRAS__->caption, null, '@param boolean $pFormat Indique si on doit formatter le retour', '@return ' . $pField->__COPIX__EXTRAS__->phpType), 1);
			$toReturn .= $php->getLine ('public function ' . $pMethod . ' ($pFormat = false) {', 1);
			$toReturn .= $php->getLine ('if ($pFormat) {', 2);
			$toReturn .= $php->getLine ('$decimals = strlen (substr ($this->_' . $pProperty . ', strpos ($this->_' . $pProperty . ', \'.\') + 1));', 3);
			$toReturn .= $php->getLine ('return number_format ($this->_' . $pProperty . ', $decimals, \',\', \' \');', 3);
			$toReturn .= $php->getLine ('} else {', 2);
			$toReturn .= $php->getLine ('return $this->_' . $pProperty . ';', 3);
			$toReturn .= $php->getLine ('}', 2);
			$toReturn .= $php->getLine ('}', 1, 2);

		// autres
		} else {
			$toReturn = $php->getPHPDoc (array ('Retourne la valeur de ' . $pField->__COPIX__EXTRAS__->caption, null, '@return ' . $pField->__COPIX__EXTRAS__->phpType), 1);
			$toReturn .= $php->getLine ('public function ' . $pMethod . ' () {', 1);
			$toReturn .= $php->getLine ('return $this->_' . $pProperty . ';', 2);
			$toReturn .= $php->getLine ('}', 1, 2);
		}

		return $toReturn;
	}

	/**
	 * Retourne le code PHP pour générer un input
	 *
	 * @param stdClass $pField Informations sur le champ
	 * @param int $pTabs Nombre de tabulations avant le code PHP
	 * @param stdClass $pElement Accès à l'objet pour remplir les contenus
	 * @param boolean $pIsSearch Si on est dans la partie recherche
	 * @return string
	 */
	private function _getPHP4Input ($pField, $pInputId, $pInputName, $pGettor, $pTabs, $pIsSearch) {
		$toReturn = null;
		$php = new CopixPHPGenerator ();
		if ($pInputId !== null) {
			$realInputNamePrefix = $pInputId . ' . \'' . $pInputName;
			$realInputName = $realInputNamePrefix . '\'';
			$realInputNameHTML = '<?php echo ' . $realInputName . ' ?>';
		} else {
			$realInputNamePrefix = '\'' . $pInputName;
			$realInputName = $realInputNamePrefix . '\'';
			$realInputNameHTML = $pInputName;
		}

		// type statut
		if ($pField->__COPIX__EXTRAS__->type == 'status') {
			$values = '\'true\' => ' . $this->_i18n ('template.adminEdit.enabled', 'Activé') . ', \'false\' => ' . $this->_i18n ('template.adminEdit.disabled', 'Désactivé');
			if ($pIsSearch) {
				$values .= ', \'\' => ' . $this->_i18n ('template.adminEdit.allStatus', 'Les deux');
				$selected = '(' . $pGettor . ' () === null ? \'\' : (' . $pGettor . ' () ? \'true\' : \'false\'))';
			} else {
				$selected = '(' . $pGettor . ' () ? \'true\' : \'false\')';
			}
			$html = '<?php _eTag (\'radiobutton\', array (\'name\' => ' . $realInputName . ', ';
			$html .= '\'values\' => array (' . $values . '), ';
			$html .= '\'selected\' => ' . $selected . ')) ?>';
			$toReturn .= $php->getLine ($html, $pTabs);

		// type stars
		} else if ($pField->__COPIX__EXTRAS__->type == 'stars') {
			$html = '<?php _eTag (\'stars\', array (\'name\' => ' . $realInputName . ', \'max\' => 5, \'value\' => ' . $pGettor . ' ())) ?>';
			$toReturn .= $php->getLine ($html, $pTabs);

		// type tinymce
		} else if ($pField->__COPIX__EXTRAS__->type == 'tinymce') {
			$toReturn .= $php->getLine ('<textarea id="' . $pInputName . '" name="' . $realInputNameHTML . '" style="width:99%"><?php echo ' . $pGettor . ' () ?></textarea>', $pTabs);
			$toReturn .= $php->getLine ('<?php', $pTabs);
			$toReturn .= $php->getLine ('$inputName = ' . $realInputName, $pTabs);
			$toReturn .= $php->getLine ('$js = <<<JS', $pTabs);
			$toReturn .= $php->getLine ('tinyMCE.init ({', $pTabs);
			$toReturn .= $php->getLine ('mode : "exact",', $pTabs + 1);
			$toReturn .= $php->getLine ('elements : "$inputName",', $pTabs + 1);
			$toReturn .= $php->getLine ('theme : "advanced",', $pTabs + 1);
			$toReturn .= $php->getLine ('theme_advanced_toolbar_location : "top",', $pTabs + 1);
			$toReturn .= $php->getLine ('theme_advanced_toolbar_align : "left",', $pTabs + 1);
			$toReturn .= $php->getLine ('theme_advanced_statusbar_location : "bottom",', $pTabs + 1);
			$toReturn .= $php->getLine ('theme_advanced_resizing : true,', $pTabs + 1);
			$toReturn .= $php->getLine ('});', $pTabs);
			$toReturn .= $php->getLine ('JS;');
			$toReturn .= $php->getLine ('CopixHTMLHeader::addJSCode ($js);', $pTabs);
			$toReturn .= $php->getLine ('?>', $pTabs);

		// type theme
		} else if ($pField->__COPIX__EXTRAS__->type == 'theme') {
			$html = '<?php _eTag (\'select\', array (\'name\' => ' . $realInputName . ', \'emptyShow\' => false, ';
			$html .= '\'values\' => CopixTheme::getList (true), \'selected\' => ' . $pGettor . ' (), ';
			$html .= '\'error\' => isset ($ppo->errors[\'' . $pInputName .'\']))) ?>';
			$toReturn .= $php->getLine ($html, $pTabs);

		// type elementchooser
		} else if ($pField->__COPIX__EXTRAS__->type == 'cms_page' || $pField->__COPIX__EXTRAS__->type == 'cms_heading') {
			$arTypes = ($pField->__COPIX__EXTRAS__->type == 'cms_heading') ? 'array (\'heading\')' : 'array (\'page\')';
			$html = '<?php echo CopixZone::process (\'heading|HeadingElementChooser\', array (\'inputElement\' => ' . $realInputName . ', \'linkOnHeading\' => true, ';
			$html .= '\'selectedIndex\' => ' . $pGettor . ' (), \'arTypes\' => ' . $arTypes . ', ';
			$html .= '\'error\' => isset ($ppo->errors[\'' . $pInputName .'\']))) ?>';
			$toReturn .= $php->getLine ($html, $pTabs);

		// type boolean
		} else if ($pField->__COPIX__EXTRAS__->phpType == 'boolean') {
			$values = '\'true\' => _i18n (\'copix:common.buttons.yes\'), \'false\' => _i18n (\'copix:common.buttons.no\')';
			if ($pIsSearch) {
				$values .= ', \'\' => \'Non défini\'';
				$selected = '(' . $pGettor . ' () === null ? \'\' : (' . $pGettor . ' () ? \'true\' : \'false\'))';
			} else {
				$selected = '(' . $pGettor . ' () ? \'true\' : \'false\')';
			}
			$html = '<?php _eTag (\'radiobutton\', array (\'name\' => ' . $realInputName . ', ';
			$html .= '\'values\' => array (' . $values . '), ';
			$html .= '\'selected\' => ' . $selected . ')) ?>';
			$toReturn .= $php->getLine ($html, $pTabs);

		// type int
		} else if ($pField->__COPIX__EXTRAS__->phpType == 'int' || $pField->__COPIX__EXTRAS__->phpType == 'float') {
			$html = '<?php _eTag (\'inputtext\', array (\'name\' => ' . $realInputName . ', \'value\' => ' . $pGettor . ' (), ';
			$html .= '\'size\' => 5, \'error\' => isset ($ppo->errors[\'' . $pInputName .'\']))) ?>';
			$toReturn .= $php->getLine ($html, $pTabs);

		// type varchar
		} else if ($pField->type == 'varchar') {
			$html = '<?php _eTag (\'inputtext\', array (\'name\' => ' . $realInputName . ', ';
			$html .= '\'value\' => ' . $pGettor . ' (), \'style\' => \'width: 99%\', ';
			$html .= '\'error\' => isset ($ppo->errors[\'' . $pInputName .'\']))) ?>';
			$toReturn .= $php->getLine ($html, $pTabs);

		// type date
		} else if ($pField->__COPIX__EXTRAS__->isDate) {
			$html = null;
			if ($pField->__COPIX__EXTRAS__->type == 'date' || $pField->__COPIX__EXTRAS__->type == 'datetime') {
				$html .= '<?php _eTag (\'calendar2\', array (\'name\' => ' . $realInputNamePrefix . '_date\', ';
				$html .= '\'value\' => ' . $pGettor . ' (\'d/m/Y\'), ';
				$html .= '\'error\' => isset ($ppo->errors[\'' . $pInputName .'\']))) ?>';
				$toReturn .= $php->getLine ($html, $pTabs);
			}
			if ($pField->__COPIX__EXTRAS__->type == 'datetime' || $pField->__COPIX__EXTRAS__->type == 'time') {
				$html = null;
				if ($pField->__COPIX__EXTRAS__->type == 'datetime') {
					$html .= 'à ';
				}
				$html .= '<?php _eTag (\'inputtext\', array (\'name\' => ' . $realInputNamePrefix . '_hour\', ';
				$html .= '\'value\' => ' . $pGettor . ' (\'H\'), \'style\' => \'width: 30px\', ';
				$html .= '\'error\' => isset ($ppo->errors[\'' . $pInputName .'\']))) ?>';
				$toReturn .= $php->getLine ($html, $pTabs);
				$html = 'h ';
				$html .= '<?php _eTag (\'inputtext\', array (\'name\' => ' . $realInputNamePrefix . '_min\', ';
				$html .= '\'value\' => ' . $pGettor . ' (\'i\'), \'style\' => \'width: 30px\', ';
				$html .= '\'error\' => isset ($ppo->errors[\'' . $pInputName .'\']))) ?>';
				$toReturn .= $php->getLine ($html, $pTabs);
			}

		// type string
		} else if ($pField->type == 'string') {
			$html = '<?php _eTag (\'textarea\', array (\'name\' => ' . $realInputName . ', ';
			$html .= '\'value\' => ' . $pGettor . ' (), \'style\' => \'width: 99%; height: 80px\', ';
			$html .= '\'error\' => isset ($ppo->errors[\'' . $pInputName .'\']))) ?>';
			$toReturn .= $php->getLine ($html, $pTabs);
		}

		return $toReturn;
	}

	/**
	 * Ajoute la clef dans fr.properties si elle n'existe pas et retourne l'appel à _i18n
	 *
	 * @param string $pKey Clef i18n
	 * @param string $pText Texte
	 * @param array $pArgs Paramètres à passer à _i18n
	 * @param boolean $pIsInPHP Indique si le code à retourner est dans du PHP ou pas
	 * @return string
	 */
	private function _i18n ($pKey, $pText, $pArgs = array (), $pIsInPHP = true) {
		// on ne peut pas utiliser var_export à cause des passages de variable (ex : array ($pId) retourne array ( 0 => '$pId' ))
		$argsPHP = array ();
		if (!is_array ($pArgs)) {
			$pArgs = array ($pArgs);
		}
		foreach ($pArgs as $value) {
			if (is_numeric ($value) || substr ($value, 0, 1) == '$') {
				$argsPHP[] = $value;
			} else {
				$argsPHP[] = "'" . $value . "'";
			}
		}

		// textes directs
		if ($this->_params['i18n'] == 'none') {
			if (count ($pArgs) == 0) {
				return ($pIsInPHP) ? "'" . str_replace ("'", "\'", $pText) . "'" : $pText;
			} else {
				if (count ($argsPHP) == 1) {
					return 'sprintf (\'' . str_replace ("'", "\'", $pText) . '\', ' . $argsPHP[0] . ')';
				} else {
					return 'sprintf (\'' . str_replace ("'", "\'", $pText) . '\', array (' . implode (', ', $argsPHP) . '))';
				}
			}

		// textes via i18n
		} else {
			$bundle = new CopixI18NBundle ($this->_module, $this->_params['i18n'], null);
			$keyPrefixed = ($this->_prefixLower !== null) ? $this->_prefixLower . '.' . $pKey : $pKey;
			$fullKey = $this->_module . '|' . $keyPrefixed;

			// création de la clef dans le fichier i18n si elle n'existe pas
			if (!array_key_exists ($keyPrefixed, $bundle->getKeys (null))) {
				if (!is_dir ($this->_path . 'locales/')) {
					CopixFile::createDir ($this->_path . 'locales/');
				}
				$file = fopen ($this->_path . 'locales/' . $this->_params['i18n'] . '.properties', 'a+');
				fwrite ($file, "\n" . $keyPrefixed . ' = ' . $pText);
				fclose ($file);
			}

			// création de l'appel à _i18n
			if (count ($pArgs) == 0) {
				return '_i18n (\'' . $fullKey . '\')';
			} else {
				if (count ($argsPHP) == 1) {
					return '_i18n (\'' . $fullKey . '\', ' . $argsPHP[0] . ')';
				} else {
					return '_i18n (\'' . $fullKey . '\', array (' . implode (', ', $argsPHP) . '))';
				}
			}
		}
	}

	/**
	 * Ecrite un fichier PHP
	 *
	 * @param string $pPath Chemin du fichier
	 * @param string $pFileName Nom du fichier
	 * @param string $pContent Contenu du fichier
	 * @param boolean $pAddPrefix Ajoute le prefix au nom du fichier
	 * @param boolean $pAddPHPTags Ajoute des tags PHP autour du contenu
	 */
	private function _write ($pPath, $pFileName, $pContent, $pAddPHPTags = true) {
		if ($pAddPHPTags) {
			$php = new CopixPHPGenerator ();
			$pContent = $php->getPHPTags ($pContent, false, 0, false);
		}
		CopixFile::write ($pPath . $pFileName, $pContent);
		chmod ($pPath . $pFileName, 0777);
	}

	/**
	 * Génère le code pour la classe d'exception
	 */
	public function generateException () {
		$php = new CopixPHPGenerator ();
		$content = $php->getPHPDoc ('Exception pour la gestion ' . $this->_keyWords['des_elements']);
		$content .= $php->getLine ('class ' . $this->_prefix . $this->_params['exception_class'] . ' extends CopixException {');
		$content .= $php->getPHPDoc (array ('Erreur lors de la validation ' . $this->_keyWords['de_l_element']), 1);

		// constantes validateur
		$content .= $php->getLine ('const VALIDATOR_ERRORS = 10;', 1);

		// constantes position
		if ($this->_fieldPosition != null) {
			$content .= $php->getLine (null, 0, 1);
			$content .= $php->getPHPDoc (array ('Position ' . $this->_keyWords['de_l_element'] . ' introuvable'), 1);
			$content .= $php->getLine ('const POSITION_UNKNOW = 20;', 1, 2);
			$content .= $php->getPHPDoc (array ('Position demandée invalide'), 1);
			$content .= $php->getLine ('const POSITION_INVALID = 21;', 1);
			$content .= $php->getPHPDoc (array ('Changement de position ' . $this->_keyWords['d_un_element'] . ' désactivé impossible'), 1);
			$content .= $php->getLine ('const POSITION_DISABLED = 22;', 1);
		}

		// constructeur
		$content .= $php->getLine ();
		$content .= $php->getPHPDoc (array ('Constructeur', null, '@param string $pMessage Texte de l\'exception', '@param int $pCode Code d\'erreur', '@param array $pExtras Informations supplémentaires', '@param array $pErrors Erreur(s) au format array pour les récupérer plus facilement'), 1);
		$content .= $php->getLine ('public function __construct ($pMessage, $pCode = 0, $pExtras = array (), $pErrors = array ()) {', 1);
		$content .= $php->getLine ('// recherche des erreurs, soit dans $pErrors, soit dans $pMessage', 2);
		$content .= $php->getLine ('if (is_array ($pErrors)) {', 2);
		$content .= $php->getLine ('if (count ($pErrors) > 0) {', 3);
		$content .= $php->getLine ('$pExtras[\'errors\'] = $pErrors;', 4);
		$content .= $php->getLine ('} else {', 3);
		$content .= $php->getLine ('$pExtras[\'errors\'] = array ($pMessage);', 4);
		$content .= $php->getLine ('}', 3);
		$content .= $php->getLine ('} else if (strlen ($pErrors) > 0) {', 2);
		$content .= $php->getLine ('$pExtras[\'errors\'] = array ($pErrors);', 3);
		$content .= $php->getLine ('} else {', 2);
		$content .= $php->getLine ('$pExtras[\'errors\'] = array ($pMessage);', 3);
		$content .= $php->getLine ('}', 2, 2);
		$content .= $php->getLine ('parent::__construct ($pMessage, $pCode, $pExtras);', 2);
		$content .= $php->getLine ('}', 1);

		// getErrors
		$content .= $php->getLine ();
		$content .= $php->getPHPDoc (array ('Retourne les erreurs', null, '@return array'), 1);
		$content .= $php->getLine ('public function getErrors () {', 1);
		$content .= $php->getLine ('return $this->getExtra (\'errors\');', 2);
		$content .= $php->getLine ('}', 1);

		$content .= $php->getLine ('}', 0, 0);
		$this->_write ($this->_getClassesPath (),  strtolower ($this->_params['exception_class']) . '.class.php', $content);

	}

	/**
	 * Génère le code pour le validateur
	 */
	public function generateValidator () {
		$php = new CopixPHPGenerator ();
		$content = $php->getPHPDoc ('Validateur pour ' . $this->_fullClass);
		$content .= $php->getLine ('class ' . $this->_prefix . $this->_params['validator_class'] . ' extends CopixAbstractValidator {');
		$content .= $php->getPHPDoc (array ('Validation des données', null, '@param ' . $this->_fullClass . ' $pObject ' . $this->_infoClass, '@return mixed'), 1);
		$content .= $php->getLine ('protected function _validate ($pObject) {', 1);
		$content .= $php->getLine ('$errors = array ();', 2, 2);
		foreach ($this->_fields as $field) {
			if (!in_array ($field->__COPIX__EXTRAS__->type, array ('id', 'boolean', 'status'))) {
				$contentErrors = null;

				// saisie vide interdite
				if ($field->required == 'yes') {
					$contentErrors .= $php->getLine ('if ($pObject->' . $field->__COPIX__EXTRAS__->gettor . ' () == null) {', 2);
					$i18n = $this->_i18n ('validator.' . $field->__COPIX__EXTRAS__->property . '.emptyValue', 'Vous devez indiquer la valeur de "' . $field->__COPIX__EXTRAS__->caption . '".');
					$contentErrors .= $php->getLine ('$errors[\'' . $field->__COPIX__EXTRAS__->property . '\'] = ' . $i18n . ';', 3);
					$contentErrors .= $php->getLine ('}', 2);
				}

				// champ de tri
				if ($field->__COPIX__EXTRAS__->type == 'position') {
					$tabs = 2;
					if ($this->_fieldStatus != null) {
						$contentErrors .= $php->getLine ('if ($pObject->' . $this->_fieldStatus->__COPIX__EXTRAS__->gettor . ' ()) {', 2);
						$tabs++;
					}
					$contentErrors .= $php->getLine ('if (!is_numeric ($pObject->' . $field->__COPIX__EXTRAS__->gettor . ' ()) || $pObject->' . $field->__COPIX__EXTRAS__->gettor . ' () <= 0) {', $tabs);
					$i18n = $this->_i18n ('validator.' . $field->__COPIX__EXTRAS__->property . '.higher0', 'La valeur de "' . $field->__COPIX__EXTRAS__->caption . '" doit être supérieure à 0.');
					$contentErrors .= $php->getLine ('$errors[\'' . $field->__COPIX__EXTRAS__->property . '\'] = ' . $i18n . ';', $tabs + 1);
					$contentErrors .= $php->getLine ('}', $tabs);
					if ($this->_fieldStatus != null) {
						$contentErrors .= $php->getLine ('} else {', 2);
						$contentErrors .= $php->getLine ('if ($pObject->' . $field->__COPIX__EXTRAS__->gettor . ' () != null) {', 3);
						$i18n = $this->_i18n ('validator.' . $field->__COPIX__EXTRAS__->property . '.mustBeNull', 'La valeur de "' . $field->__COPIX__EXTRAS__->caption . '" doit être nulle quand ' . $this->_keyWords['l_element'] . 'est désactivé.');
						$contentErrors .= $php->getLine ('$errors[\'' . $field->__COPIX__EXTRAS__->property . '\'] = ' . $i18n . ';', 4);
						$contentErrors .= $php->getLine ('}', 3);
						$contentErrors .= $php->getLine ('}', 2);
					}
				}

				// thème
				if ($field->__COPIX__EXTRAS__->type == 'theme') {
					$contentErrors .= $php->getLine ('if (!in_array ($pObject->' . $field->__COPIX__EXTRAS__->gettor . ' (), array_keys (CopixTheme::getList (true)))) {', 2);
					$i18n = $this->_i18n ('validator.' . $field->__COPIX__EXTRAS__->property . '.invalidTheme', 'Le thème sélectionné est invalide.');
					$contentErrors .= $php->getLine ('$errors[\'' . $field->__COPIX__EXTRAS__->property . '\'] = ' . $i18n . ';', 3);
					$contentErrors .= $php->getLine ('}', 2);
				}

				// URL
				if ($field->__COPIX__EXTRAS__->type == 'url') {
					$contentErrors .= $php->getLine ('if ($pObject->' . $field->__COPIX__EXTRAS__->gettor . ' () != null) {', 2);
					$contentErrors .= $php->getLine ('$validate = _validator (\'CopixValidatorURL\')->check ($pObject->' . $field->__COPIX__EXTRAS__->gettor . ' ());', 3);
					$contentErrors .= $php->getLine ('if ($validate instanceof CopixErrorObject) {', 3);
					$contentErrors .= $php->getLine ('$errors[\'' . $field->__COPIX__EXTRAS__->property . '\'] = implode (\', \', $validate->asArray ());', 4);
					$contentErrors .= $php->getLine ('}', 3);
					$contentErrors .= $php->getLine ('}', 2);
				}

				// email
				if ($field->__COPIX__EXTRAS__->type == 'email') {
					$contentErrors .= $php->getLine ('if ($pObject->' . $field->__COPIX__EXTRAS__->gettor . ' () != null) {', 2);
					$contentErrors .= $php->getLine ('$validate = _validator (\'CopixValidatorEMail\')->check ($pObject->' . $field->__COPIX__EXTRAS__->gettor . ' ());', 3);
					$contentErrors .= $php->getLine ('if ($validate instanceof CopixErrorObject) {', 3);
					$contentErrors .= $php->getLine ('$errors[\'' . $field->__COPIX__EXTRAS__->property . '\'] = implode (\', \', $validate->asArray ());', 4);
					$contentErrors .= $php->getLine ('}', 3);
					$contentErrors .= $php->getLine ('}', 2);
				}

				// int
				if ($field->__COPIX__EXTRAS__->phpType == 'int') {
					$contentErrors .= $php->getLine ('$validate = _validator (\'CopixValidatorNumeric\')->check ($pObject->' . $field->__COPIX__EXTRAS__->gettor . ' ());', 2);
					$contentErrors .= $php->getLine ('if ($validate instanceof CopixErrorObject) {', 2);
					$contentErrors .= $php->getLine ('$errors[\'' . $field->__COPIX__EXTRAS__->property . '\'] = implode (\', \', $validate->asArray ());', 3);
					$contentErrors .= $php->getLine ('}', 2);
				}

				// date, datetime, time
				if ($field->__COPIX__EXTRAS__->isDate) {
					$contentErrors .= $php->getLine ('$validate = _validator (\'CopixValidatorDate\', array (\'format\' => \'timestamp\'))->check ($pObject->' . $field->__COPIX__EXTRAS__->gettor . ' (\'U\'));', 2);
					$contentErrors .= $php->getLine ('if ($validate instanceof CopixErrorObject) {', 2);
					$contentErrors .= $php->getLine ('$errors[\'' . $field->__COPIX__EXTRAS__->property . '\'] = implode (\', \', $validate->asArray ());', 3);
					$contentErrors .= $php->getLine ('}', 2);
				}

				if ($contentErrors) {
					$content .= $php->getLine ('// ' . $field->__COPIX__EXTRAS__->caption, 2);
					$content .= $php->getLine ($contentErrors, 0, 1);
				}
			}
		}
		$content .= $php->getLine ('return (count ($errors) == 0) ? true : $errors;', 2);
		$content .= $php->getLine ('}', 1);
		$content .= $php->getLine ('}', 0, 0);
		$this->_write ($this->_getClassesPath (), strtolower ($this->_params['validator_class']) . '.class.php', $content);
	}

	/**
	 * Génère le code pour le service
	 */
	public function generateService () {
		$php = new CopixPHPGenerator ();
		$content = $php->getPHPDoc ('Gestion ' . $this->_keyWords['des_elements']);
		$content .= $php->getLine ('class ' . $this->_fullService . ' {');

		// profil de connexion
		$content .= $php->getPHPDoc (array ('Profil de connexion à utiliser, null pour le profil par défaut', null, '@var string'), 1);
		$content .= $php->getLine ('private static $_dbProfile = ' . ($this->_params['addDAOProfile'] == 'true' ? '\'' . $this->_profile . '\';' : 'null;'), 1, 2);

		// _getRecord
		$content .= $php->getPHPDoc (array ('Retourne un record avec un objet', null, '@param ' . $this->_fullClass . ' $pObject ' . $this->_infoClass, '@return DAORecord' . $this->_table), 1);
		$content .= $php->getLine ('private static function _getRecord ($pObject) {', 1);
		$content .= $php->getLine ('$toReturn = new DAORecord' . $this->_table . ' ();', 2);
		foreach ($this->_fields as $field) {
			if ($field->__COPIX__EXTRAS__->phpType == 'boolean') {
				$content .= $php->getLine ('$toReturn->' . $field->name . ' = ($pObject->' . $field->__COPIX__EXTRAS__->gettor . ' () ? 1 : 0);', 2);
			} else if ($field->__COPIX__EXTRAS__->isDate) {
				switch ($field->__COPIX__EXTRAS__->type) {
					case 'date' : $format = 'Ymd'; break;
					case 'datetime' : $format = 'YmdHis'; break;
					case 'time' : $format = 'His'; break;
				}
				$content .= $php->getLine ('$toReturn->' . $field->name . ' = ($pObject->' . $field->__COPIX__EXTRAS__->gettor  . ' (\'U\') > 0) ? $pObject->' . $field->__COPIX__EXTRAS__->gettor  . ' (\'' . $format . '\') : null;', 2);
			} else {
				$content .= $php->getLine ('$toReturn->' . $field->name . ' = $pObject->' . $field->__COPIX__EXTRAS__->gettor . ' ();', 2);
			}

		}
		$content .= $php->getLine ('return $toReturn;', 2);
		$content .= $php->getLine ('}', 1, 2);

		// _getObject
		$content .= $php->getPHPDoc (array ('Retourne un objet avec un record', null, '@param DAORecord' . $this->_table . ' $pRecord Record', '@return ' . $this->_fullClass), 1);
		$content .= $php->getLine ('private static function _getObject ($pRecord) {', 1);
		$content .= $php->getLine ('$toReturn = new ' . $this->_fullClass . ' ();', 2);
		foreach ($this->_fields as $field) {
			$content .= $php->getLine ('$toReturn->' . $field->__COPIX__EXTRAS__->settor . ' ($pRecord->' . $field->name . ');', 2);
		}
		$content .= $php->getLine ('return $toReturn;', 2);
		$content .= $php->getLine ('}', 1, 2);

		// _getSearchSP
		$content .= $php->getPHPDoc (array ('Retourne un objet de recherche version DAO', null, '@param ' . $this->_prefix . $this->_searchClass . ' Informations de recherche', '@return CopixDAOSearchParams'), 1);
		$content .= $php->getLine ('private static function _getSearchSP ($pSearch) {', 1);
		$content .= $php->getLine ('$toReturn = _daoSP ();', 2, 2);
		$content .= $php->getLine ('if (!$pSearch instanceof ' . $this->_prefix . $this->_searchClass . ') {', 2);
		$content .= $php->getLine ('return $toReturn;', 3);
		$content .= $php->getLine ('}', 2, 2);
		$content .= $php->getLine ('if ($pSearch->getOffset () > 0) {', 2);
		$content .= $php->getLine ('$toReturn->setOffset ($pSearch->getOffset ());', 3);
		$content .= $php->getLine ('}', 2, 2);
		$content .= $php->getLine ('if ($pSearch->getCount () > 0) {', 2);
		$content .= $php->getLine ('$toReturn->setCount ($pSearch->getCount ());', 3);
		$content .= $php->getLine ('}', 2, 2);
		foreach ($this->_fieldsSearch as $field) {
			// status, boolean
			if ($field->__COPIX__EXTRAS__->type == 'status' || $field->__COPIX__EXTRAS__->type == 'boolean') {
				$content .= $php->getLine ('if ($pSearch->' . $field->__COPIX__EXTRAS__->gettor . ' () === true) {', 2);
				$content .= $php->getLine ('$toReturn->addCondition (\'' . $field->name . '\', \'=\', 1);', 3);
				$content .= $php->getLine ('} else if ($pSearch->' . $field->__COPIX__EXTRAS__->gettor . ' () === false) {', 2);
				$content .= $php->getLine ('$toReturn->addCondition (\'' . $field->name . '\', \'=\', 0);', 3);
				$content .= $php->getLine ('}', 2, 2);

			// stars, date
			} else if ($field->__COPIX__EXTRAS__->type == 'stars' || $field->__COPIX__EXTRAS__->isDate) {
				switch ($field->__COPIX__EXTRAS__->type) {
					case 'date' : $format = '\'Ymd\''; break;
					case 'datetime' : $format = '\'YmdHis\''; break;
					case 'time' : $format = '\'His\''; break;
					default : $format = null;
				}
				$content .= $php->getLine ('if ($pSearch->' . $field->__COPIX__EXTRAS__->gettor . 'From () !== null) {', 2);
				$content .= $php->getLine ('$toReturn->addCondition (\'' . $field->name . '\', \'>=\', $pSearch->' . $field->__COPIX__EXTRAS__->gettor . 'From (' . $format . '));', 3);
				$content .= $php->getLine ('}', 2);
				$content .= $php->getLine ('if ($pSearch->' . $field->__COPIX__EXTRAS__->gettor . 'To () !== null) {', 2);
				$content .= $php->getLine ('$toReturn->addCondition (\'' . $field->name . '\', \'<=\', $pSearch->' . $field->__COPIX__EXTRAS__->gettor . 'To (' . $format . '));', 3);
				$content .= $php->getLine ('}', 2, 2);

			// tous les autres champs
			} else {
				if (in_array ($field->__COPIX__EXTRAS__->type, array ('email', 'string', 'url', 'tinymce', 'varchar'))) {
					$comparator = 'LIKE';
					$value = '\'%\' . $pSearch->' . $field->__COPIX__EXTRAS__->gettor . ' () . \'%\'';
				} else {
					$comparator = '=';
					$value = '$pSearch->' . $field->__COPIX__EXTRAS__->gettor . ' ()';
				}
				$content .= $php->getLine ('if ($pSearch->' . $field->__COPIX__EXTRAS__->gettor . ' () !== null) {', 2);
				$content .= $php->getLine ('$toReturn->addCondition (\'' . $field->name . '\', \'' . $comparator . '\', ' . $value . ');',3);
				$content .= $php->getLine ('}', 2, 2);
			}
		}
		$content .= $php->getLine ('if (count ($pSearch->getOrderBy ()) == 0) {', 2);
		$content .= $php->getLine ('$toReturn->orderBy (\'' . $this->_fieldCaption->name . '\');', 3);
		$content .= $php->getLine ('} else {', 2);
		$content .= $php->getLine ('foreach ($pSearch->getOrderBy () as $name => $kind) {', 3);
		$content .= $php->getLine ('$toReturn->orderBy (array ($name, $kind));', 4);
		$content .= $php->getLine ('}', 3);
		$content .= $php->getLine ('}', 2, 2);
		$content .= $php->getLine ('return $toReturn;', 2);
		$content .= $php->getLine ('}', 1, 2);

		// create
		$content .= $php->getPHPDoc (array ('Retourne un objet vierge', null, '@return ' . $this->_fullClass), 1);
		$content .= $php->getLine ('public static function create () {', 1);
		$content .= $php->getLine ('return new ' . $this->_fullClass . ' ();', 2);
		$content .= $php->getLine ('}', 1, 2);

		// createSearch
		$content .= $php->getPHPDoc (array ('Retourne un objet de recherche vierge', null, '@return ' . $this->_prefix . $this->_searchClass), 1);
		$content .= $php->getLine ('public static function createSearch () {', 1);
		$content .= $php->getLine ('return new ' . $this->_prefix . $this->_searchClass . ' ();', 2);
		$content .= $php->getLine ('}', 1, 2);

		// count
		$content .= $php->getPHPDoc (array ('Retourne le nombre ' . $this->_keyWords['d_elements'], null, '@param ' . $this->_prefix . $this->_searchClass . ' $pSearch Informations de recherche', '@return int'), 1);
		$content .= $php->getLine ('public static function count ($pSearch = null) {', 1);
		$content .= $php->getLine ('return ' . $this->_dao . '->countBy (self::_getSearchSP ($pSearch));', 2);
		$content .= $php->getLine ('}', 1, 2);

		// getList
		$content .= $php->getPHPDoc (array (
			'Retourne la liste ' . $this->_keyWords['des_elements'],
			null,
			'@param ' . $this->_prefix . $this->_searchClass . ' $pSearch Informations de recherche',
			'@return ' . $this->_fullClass . '[]'
		), 1);
		$content .= $php->getLine ('public static function getList ($pSearch = null) {', 1);

		$content .= $php->getLine ('$toReturn = array ();', 2);
		$content .= $php->getLine ('$sp = self::_getSearchSP ($pSearch);', 2);
		if ($this->_fieldPosition != null) {
			$content .= $php->getLine ('$sp->orderBy (\'' . $this->_fieldPosition->name . '\');', 2);
		}
		$content .= $php->getLine ('foreach (' . $this->_dao . '->findBy ($sp) as $record) {', 2);
		$content .= $php->getLine ('$toReturn[$record->' . $this->_fieldId->name . '] = self::_getObject ($record);', 3);
		$content .= $php->getLine ('}', 2);
		$content .= $php->getLine ('return $toReturn;', 2);
		$content .= $php->getLine ('}', 1, 2);

		// order
		if ($this->_fieldPosition !== null) {
			$content .= $php->getPHPDoc (array ('Retourne la liste ' . $this->_keyWords['des_elements'] . ' demandés triés', null, '@param array $pIds Identifiants', '@return array'), 1);
			$content .= $php->getLine ('public static function order ($pIds) {', 1);
			$content .= $php->getLine ('$sp = _daoSP ();', 2);
			$content .= $php->getLine ('$sp->addCondition (\'' . $this->_fieldId->name . '\', \'=\', $pIds);', 2);
			$content .= $php->getLine ('$sp->orderBy (\'' . $this->_fieldPosition->name . '\');', 2);
			$content .= $php->getLine ('$results = ' . $this->_dao . '->findBy ($sp);', 2);
			$content .= $php->getLine ('$toReturn = array ();', 2);
			$content .= $php->getLine ('foreach ($results as $result) {', 2);
			$content .= $php->getLine ('$toReturn[] = $result->' . $this->_fieldId->name . ';', 3);
			$content .= $php->getLine ('}', 2);
			$content .= $php->getLine ('return $toReturn;', 2);
			$content .= $php->getLine ('}', 1);
		}

		// get
		$content .= $php->getPHPDoc (array ('Retourne ' . $this->_keyWords['l_element'] . 'demandé', null, '@param ' . $this->_fieldId->__COPIX__EXTRAS__->phpType . ' $pId Identifiant', '@return ' . $this->_fullClass), 1);
		$content .= $php->getLine ('public static function get ($pId) {', 1);
		$content .= $php->getLine ('$record = ' . $this->_dao . '->get ($pId);', 2);
		$content .= $php->getLine ('if ($record === false) {', 2);
		$content .= $php->getLine ('throw new ' . $this->_fullException . ' (' . $this->_i18n ('service.error.notFound', $this->_keyWords['l_element'] . ' "%1$s" n\'existe pas.', '$pId') . ');', 3);
		$content .= $php->getLine ('}', 2);
		$content .= $php->getLine ('return self::_getObject ($record);', 2);
		$content .= $php->getLine ('}', 1, 2);

		// exists
		$content .= $php->getPHPDoc (array ('Indique si ' . $this->_keyWords['l_element'] . ' demandé existe', null, '@param ' . $this->_fieldId->__COPIX__EXTRAS__->phpType . ' $pId Identifiant', '@return boolean'), 1);
		$content .= $php->getLine ('public static function exists ($pId) {', 1);
		$content .= $php->getLine ('try {', 2);
		$content .= $php->getLine ('self::get ($pId);', 3);
		$content .= $php->getLine ('return true;', 3);
		$content .= $php->getLine ('} catch (Exception $e) {', 2);
		$content .= $php->getLine ('return false;', 3);
		$content .= $php->getLine ('}', 2);
		$content .= $php->getLine ('}', 1, 2);

		// insert
		$content .= $php->getPHPDoc (array ('Ajoute ' . $this->_keyWords['l_element'] . ' en base', null, '@param ' . $this->_fullClass . ' $pObject ' . $this->_infoClass), 1);
		$content .= $php->getLine ('public static function insert ($pObject) {', 1);

		if ($this->_params['credentials'] != null) {
			$content .= $php->getLine ('_currentUser ()->assertCredential (\'' . $this->_params['credentials'] . '\');', 2, 2);
		}

		if ($this->_fieldPosition != null) {
			$content .= $php->getLine ('// gestion de la position', 2);
			$tabs = 2;
			if ($this->_fieldStatus != null) {
				$content .= $php->getLine ('if ($pObject->' . $this->_fieldStatus->__COPIX__EXTRAS__->gettor . ' ()) {', 2);
				$tabs++;
			}
			$content .= $php->getLine ('$query = \'SELECT MAX(' . $this->_fieldPosition->name . ') position FROM ' . $this->_table . '\';', $tabs);
			$content .= $php->getLine ('$lastPosition = _doQuery ($query, array (), self::$_dbProfile);', $tabs);
			$content .= $php->getLine ('if (count ($lastPosition) != 1) {', $tabs);
			$content .= $php->getLine (
				'throw new ' . $this->_fullException . ' (' . $this->_i18n ('service.error.searchNewPosition', 'La recherche de la nouvelle position n\'a pu être effectuée.') . ', ' .
				$this->_fullException . '::POSITION_UNKNOW, ' .
				'array (\'query\' => $query, \'query_result\' => $lastPosition));',
				$tabs + 1
			);
			$content .= $php->getLine ('}', $tabs);
			$content .= $php->getLine ('$newPosition = ($lastPosition[0]->position == null) ? 1 : $lastPosition[0]->position + 1;', $tabs);
			$content .= $php->getLine ('$pObject->' . $this->_fieldPosition->__COPIX__EXTRAS__->settor . ' ($newPosition);', $tabs);
			if ($this->_fieldStatus != null) {
				$content .= $php->getLine ('} else {', 2);
				$content .= $php->getLine ('$pObject->' . $this->_fieldPosition->__COPIX__EXTRAS__->settor . ' (null);', 3);
				$content .= $php->getLine ('}', 2);
			}
			$content .= $php->getLine (null);
		}
		$content .= $php->getLine ('// vérification de la validité des données', 2);
		$content .= $php->getLine ('$errors = $pObject->isValid ();', 2);
		$content .= $php->getLine ('if ($errors instanceof CopixErrorObject) {', 2);
		$content .= $php->getLine (
			'throw new ' . $this->_fullException . ' (' . $this->_i18n ('service.error.insertInvalidObject', 'Erreurs lors de l\'insertion ' . $this->_keyWords['de_l_element'] . '.') . ', ' .
			$this->_fullException . '::VALIDATOR_ERRORS, ' .
			'array (), ' .
			'$errors->asArray ());',
			3
		);
		$content .= $php->getLine ('}', 2, 2);
		$content .= $php->getLine ('// insertion en base', 2);
		$content .= $php->getLine ('$record = self::_getRecord ($pObject);', 2);
		if ($this->_fieldId->isAutoIncrement) {
			$content .= $php->getLine ('$record->' . $this->_fieldId->name . ' = null;', 2);
		}
		$content .= $php->getLine ($this->_dao . '->insert ($record);', 2);
		$content .= $php->getLine ('$pObject->' . $this->_fieldId->__COPIX__EXTRAS__->settor . ' ($record->' . $this->_fieldId->name . ');', 2);
		$content .= $php->getLine ('}', 1, 2);

		// update
		$content .= $php->getPHPDoc (array ('Modifie ' . $this->_keyWords['l_element'] . ' en base', null, '@param ' . $this->_fullClass . ' $pObject ' . $this->_infoClass), 1);
		$content .= $php->getLine ('public static function update ($pObject) {', 1);

		if ($this->_params['credentials'] != null) {
			$content .= $php->getLine ('_currentUser ()->assertCredential (\'' . $this->_params['credentials'] . '\');', 2, 2);
		}

		if ($this->_fieldPosition != null) {
			$content .= $php->getLine ('// vérification de la position', 2);
			$content .= $php->getLine ('$element = self::get ($pObject->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' ());', 2);
			$content .= $php->getLine ('if ($element->' . $this->_fieldPosition->__COPIX__EXTRAS__->gettor . ' () != $pObject->' . $this->_fieldPosition->__COPIX__EXTRAS__->gettor . ' ()) {', 2);
			$content .= $php->getLine ('$params = array (\'element_id\' => $pObject->getId (), \'element_position\' => $pObject->' . $this->_fieldPosition->__COPIX__EXTRAS__->gettor . ' (), \'element_position_db\' => $element->' . $this->_fieldPosition->__COPIX__EXTRAS__->gettor . ' ());', 3);
			$content .= $php->getLine (
				'throw new ' . $this->_fullException . ' (' . $this->_i18n ('service.error.positionIsNotSame', 'La position est différente de celle en base de données. Pour changer la position, vous devez utiliser la méthode ' . $this->_fullService . '::' . $this->_fieldPosition->__COPIX__EXTRAS__->settor . ' ().') . ', ' .
				$this->_fullException . '::POSITION_INVALID, ' .
				'$params);',
				3
			);
			$content .= $php->getLine ('}', 2, 2);

			if ($this->_fieldStatus != null) {
				$content .= $php->getLine ('// si ' . $this->_keyWords['l_element'] . ' est désactivé, on supprime l\'information de position', 2);
				$content .= $php->getLine ('if (!$pObject->' . $this->_fieldStatus->__COPIX__EXTRAS__->gettor . ' ()) {', 2);
				$content .= $php->getLine ('$position = $pObject->' . $this->_fieldPosition->__COPIX__EXTRAS__->gettor . ' ();', 3);
				$content .= $php->getLine ('$pObject->' . $this->_fieldPosition->__COPIX__EXTRAS__->settor . ' (null);', 3);
				$content .= $php->getLine ('// si on active ' . $this->_keyWords['l_element'] . ', on le place à la fin de la liste', 2);
				$content .= $php->getLine ('} else if ($pObject->' . $this->_fieldPosition->__COPIX__EXTRAS__->gettor . ' () == null) {', 2);
				$content .= $php->getLine ('$pObject->' . $this->_fieldPosition->__COPIX__EXTRAS__->settor . ' (self::count (self::createSearch ()->' . $this->_fieldStatus->__COPIX__EXTRAS__->settor . ' (true)) + 1);', 3);
				$content .= $php->getLine ('}', 2, 2);
			}
		}
		$content .= $php->getLine ('// vérification de la validité des données', 2);
		$content .= $php->getLine ('$errors = $pObject->isValid ();', 2);
		$content .= $php->getLine ('if ($errors instanceof CopixErrorObject) {', 2);
		$content .= $php->getLine ('throw new ' . $this->_fullException . ' (' . $this->_i18n ('service.error.updateInvalidObject', 'Erreurs lors de la mise à jour de ' . $this->_keyWords['l_element'] . '.') . ', ' . $this->_fullException . '::VALIDATOR_ERRORS, array (), $errors->asArray ());', 3);
		$content .= $php->getLine ('}', 2, 2);
		$content .= $php->getLine ('// modification en base', 2);
		$content .= $php->getLine ($this->_dao . '->update (self::_getRecord ($pObject));', 2);
		if ($this->_fieldPosition != null && $this->_fieldStatus != null) {
			$content .= $php->getLine (null);
			$content .= $php->getLine ('// déplacement ' . $this->_keyWords['des_elements'] . ' suivants lorsqu\'on désactive ' . $this->_keyWords['l_element'], 2);
			$content .= $php->getLine ('if (!$pObject->' . $this->_fieldStatus->__COPIX__EXTRAS__->gettor . ' () && $position > 0) {', 2);
			$content .= $php->getLine ('$query = \'UPDATE ' . $this->_table . ' SET ' . $this->_fieldPosition->name . ' = ' . $this->_fieldPosition->name . ' - 1 WHERE ' . $this->_fieldPosition->name . ' > :position\';', 3);
			$content .= $php->getLine ('$params = array (\':position\' => $position);', 3);
			$content .= $php->getLine ('_doQuery ($query, $params, self::$_dbProfile);', 3);
			$content .= $php->getLine ('}', 2, 2);
		}
		$content .= $php->getLine ('}', 1, 2);

		// setPosition
		if ($this->_fieldPosition != null) {
			$content .= $php->getPHPDoc (array ('Modifie la position ' . $this->_keyWords['de_l_element'] . ' en base', null, '@param ' . $this->_fullClass . ' $pObject ' . $this->_infoClass, '@param int $pPosition Nouvelle position'), 1);
			$content .= $php->getLine ('public static function ' . $this->_fieldPosition->__COPIX__EXTRAS__->settor . ' ($pObject, $pPosition) {', 1);

			if ($this->_params['credentials'] != null) {
				$content .= $php->getLine ('_currentUser ()->assertCredential (\'' . $this->_params['credentials'] . '\');', 2, 2);
			}

			if ($this->_fieldStatus != null) {
				$content .= $php->getLine ('// désactivé', 2);
				$content .= $php->getLine ('if (!$pObject->' . $this->_fieldStatus->__COPIX__EXTRAS__->gettor . ' ()) {', 2);
				$content .= $php->getLine ('throw new ' . $this->_fullException . ' (' . $this->_i18n ('service.error.positionForDisabled', 'Impossible de changer la position ' . $this->_keyWords['d_un_element'] . ' désactivé.') . ', ' . $this->_fullException . '::POSITION_DISABLED);', 3);
				$content .= $php->getLine ('}', 2, 2);
			}

			$content .= $php->getLine ('// position inchangée', 2);
			$content .= $php->getLine ('if ($pObject->' . $this->_fieldPosition->__COPIX__EXTRAS__->gettor . ' () == $pPosition) {', 2);
			$content .= $php->getLine ('return null;', 3);
			$content .= $php->getLine ('}', 2, 2);

			$content .= $php->getLine ('// position <= 0 interdite', 2);
			$content .= $php->getLine ('if ($pPosition <= 0) {', 2);
			$content .= $php->getLine ('throw new ' . $this->_fullException . ' (' . $this->_i18n ('service.error.positionIsTooSmall', 'La position minimum est 1, "%1$s" n\'est pas une position valide.', '$pPosition') . ', ' . $this->_fullException . '::POSITION_INVALID);', 3);
			$content .= $php->getLine ('}', 2, 2);

			$content .= $php->getLine ('// position > max interdite', 2);
			$content .= $php->getLine ('$query = \'SELECT MAX(' . $this->_fieldPosition->name . ') position FROM ' . $this->_table . '\';', 2);
			$content .= $php->getLine ('$maxPosition = _doQuery ($query, array (), self::$_dbProfile);', 2);
			$content .= $php->getLine ('if (count ($maxPosition) != 1 || !isset ($maxPosition[0]->position)) {', 2);
			$content .= $php->getLine ('throw new ' . $this->_fullException . ' (' . $this->_i18n ('service.error.searchMaxPosition', 'La recherche de la position maximale n\'a pu être effectuée.') . ', ' . $this->_fullException . '::POSITION_UNKNOW, array (\'query\' => $query, \'query_result\' => $maxPosition));', 3);
			$content .= $php->getLine ('}', 2);
			$content .= $php->getLine ('if ($pPosition > $maxPosition[0]->position) {', 2);
			$content .= $php->getLine ('throw new ' . $this->_fullException . ' (' . $this->_i18n ('service.error.positionIsTooBig', 'La position maximale est %1$s, "%2$s" n\'est pas une position valide.', array ('$maxPosition[0]->position', '$pPosition')) . ', ' . $this->_fullException . '::POSITION_INVALID);', 3);
			$content .= $php->getLine ('}', 2, 2);

			$content .= $php->getLine ('CopixDB::begin ();', 2);
			$content .= $php->getLine ('try {', 2);
			$content .= $php->getLine ('if ($pPosition > $pObject->' . $this->_fieldPosition->__COPIX__EXTRAS__->gettor . ' ()) {', 3);
			$content .= $php->getLine ('$query = \'UPDATE ' . $this->_table . ' SET ' . $this->_fieldPosition->name . ' = ' . $this->_fieldPosition->name . ' - 1 WHERE ' . $this->_fieldPosition->name . ' > :minPosition AND ' . $this->_fieldPosition->name . ' <= :maxPosition\';', 4);
			$content .= $php->getLine ('$params = array (\':minPosition\' => $pObject->' . $this->_fieldPosition->__COPIX__EXTRAS__->gettor . ' (), \':maxPosition\' => $pPosition);', 4);
			$content .= $php->getLine ('} else {', 3);
			$content .= $php->getLine ('$query = \'UPDATE ' . $this->_table . ' SET ' . $this->_fieldPosition->name . ' = ' . $this->_fieldPosition->name . ' + 1 WHERE ' . $this->_fieldPosition->name . ' >= :minPosition AND ' . $this->_fieldPosition->name . ' < :maxPosition\';', 4);
			$content .= $php->getLine ('$params = array (\':minPosition\' => $pPosition, \':maxPosition\' => $pObject->' . $this->_fieldPosition->__COPIX__EXTRAS__->gettor . ' ());', 4);
			$content .= $php->getLine ('}', 3);
			$content .= $php->getLine ('_doQuery ($query, $params, self::$_dbProfile);', 3, 2);
			$content .= $php->getLine ('$query = \'UPDATE ' . $this->_table . ' SET ' . $this->_fieldPosition->name . ' = :position WHERE ' . $this->_fieldId->name . ' = :id\';', 3);
			$content .= $php->getLine ('$params = array (\':position\' => $pPosition, \':id\' => $pObject->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' ());', 3);
			$content .= $php->getLine ('_doQuery ($query, $params, self::$_dbProfile);', 3);
			$content .= $php->getLine ('} catch (Exception $e) {', 2);
			$content .= $php->getLine ('CopixDB::rollback ();', 3);
			$content .= $php->getLine ('throw $e;', 3);
			$content .= $php->getLine ('}', 2);
			$content .= $php->getLine ('CopixDB::commit ();', 2);
			$content .= $php->getLine ('}', 1, 2);
		}

		// delete
		$content .= $php->getPHPDoc (array ('Supprime ' . $this->_keyWords['l_element'] . ' en base', null, '@param ' . $this->_fieldId->__COPIX__EXTRAS__->phpType . ' $pId Identifiant'), 1);
		$content .= $php->getLine ('public static function delete ($pId) {', 1);

		if ($this->_params['credentials'] != null) {
			$content .= $php->getLine ('_currentUser ()->assertCredential (\'' . $this->_params['credentials'] . '\');', 2, 2);
		}

		$content .= $php->getLine ('// permet de vérifier l\'existance ' . $this->_keyWords['de_l_element'], 2);
		$content .= $php->getLine ('$element = self::get ($pId);', 2, 2);
		if ($this->_fieldPosition == null) {
			$content .= $php->getLine ($this->_dao . '->delete ($pId);', 2);
		} else {
			$content .= $php->getLine ('CopixDB::begin ();', 2);
			$content .= $php->getLine ('try {', 2);

			$content .= $php->getLine ('// suppression ' . $this->_keyWords['de_l_element'], 3);
			$content .= $php->getLine ($this->_dao . '->delete ($pId);', 3, 2);

			$content .= $php->getLine ('// changement des positions ' . $this->_keyWords['des_elements'] . ' suivants', 3);
			$tabs = 3;
			if ($this->_fieldStatus != null) {
				$content .= $php->getLine ('if ($element->' . $this->_fieldStatus->__COPIX__EXTRAS__->gettor . ' ()) {', 3);
				$tabs++;
			}
			$content .= $php->getLine ('$query = \'UPDATE ' . $this->_table . ' SET ' . $this->_fieldPosition->name . ' = ' . $this->_fieldPosition->name . ' - 1 WHERE ' . $this->_fieldPosition->name . ' > :position\';', $tabs);
			$content .= $php->getLine ('$params = array (\':position\' => $element->' . $this->_fieldPosition->__COPIX__EXTRAS__->gettor . ' ());', $tabs);
			$content .= $php->getLine ('_doQuery ($query, $params, self::$_dbProfile);', $tabs);
			if ($this->_fieldStatus != null) {
				$content .= $php->getLine ('}', 3);
			}

			$content .= $php->getLine ('} catch (Exception $e) {', 2);
			$content .= $php->getLine ('CopixDB::rollback ();', 3);
			$content .= $php->getLine ('throw $e;', 3);
			$content .= $php->getLine ('}', 2);
			$content .= $php->getLine ('CopixDB::commit ();', 2);
		}
		$content .= $php->getLine ('}', 1);

		$content .= $php->getLine ('}', 0, 0);
		$this->_write ($this->_getClassesPath (), strtolower ($this->_params['service_class']) . '.class.php', $content);
	}

	/**
	 * Génère le code pour la classe principale
	 */
	public function generateMainClass () {
		$php = new CopixPHPGenerator ();
		$content = $php->getPHPDoc ('Informations sur ' . $this->_keyWords['un_element']);
		$content .= $php->getLine ('class ' . $this->_prefix . $this->_infoClass . ' {');

		// propriétés privées
		foreach ($this->_fields as $field) {
			$content .= $php->getPHPDoc (array ($field->__COPIX__EXTRAS__->caption, null, '@var ' . $field->__COPIX__EXTRAS__->phpType), 1);
			$defaultValue = ($field->__COPIX__EXTRAS__->phpType == 'boolean') ? 'true' : 'null';
			$content .= $php->getLine ('private $_' . $field->__COPIX__EXTRAS__->property . ' = ' . $defaultValue . ';', 1, 2);
		}

		// setteurs / getteurs
		foreach ($this->_fields as $field) {
			$content .= $this->_getPHP4Settor ($field, $field->__COPIX__EXTRAS__->property, $field->__COPIX__EXTRAS__->settor);
			$content .= $this->_getPHP4Gettor ($field, $field->__COPIX__EXTRAS__->property, $field->__COPIX__EXTRAS__->gettor);
		}

		// setFromArray
		$doc = array (
			'Définit les valeurs de l\'objet selon les données du tableau',
			null,
			'@param array $pDatas Données à définir dans l\'objet',
			'@param string $pPrefix Préfixe des clefs de $pDatas',
			'@return mixed'
		);
		$content .= $php->getPHPDoc ($doc, 1);
		$content .= $php->getLine ('public function setFromArray ($pDatas, $pPrefix = null) {', 1);
		foreach ($this->_fields as $field) {
			if ($field->__COPIX__EXTRAS__->isEditable) {
				if ($field->__COPIX__EXTRAS__->isDate) {
					$suffixe = ($field->__COPIX__EXTRAS__->type == 'date' || $field->__COPIX__EXTRAS__->type == 'datetime') ? '_date' : '_hour';
					$content .= $php->getLine ('if (array_key_exists ($pPrefix . \'' . $field->__COPIX__EXTRAS__->property . $suffixe . '\', $pDatas) && $pDatas[$pPrefix . \'' . $field->__COPIX__EXTRAS__->property . $suffixe . '\'] !== null) {', 2);
					$content .= $php->getLine ('$date = null;', 3);
					if ($field->__COPIX__EXTRAS__->type == 'date' || $field->__COPIX__EXTRAS__->type == 'datetime') {
						$content .= $php->getLine ('list ($day, $month, $year) = explode (\'/\', $pDatas[$pPrefix . \'' . $field->__COPIX__EXTRAS__->property . '_date\']);', 3);
						$content .= $php->getLine ('$date .= $year . $month . $day;', 3);
					}
					if ($field->__COPIX__EXTRAS__->type == 'datetime' || $field->__COPIX__EXTRAS__->type == 'time') {
						$content .= $php->getLine ('$date .= $pDatas[$pPrefix . \'' . $field->__COPIX__EXTRAS__->property . '_hour\'] . $pDatas[$pPrefix . \'' . $field->__COPIX__EXTRAS__->property . '_min\'] . \'00\';', 3);
					}
					$content .= $php->getLine ('$this->' . $field->__COPIX__EXTRAS__->settor . ' ($date);', 3);
					$content .= $php->getLine ('}', 2);
				} else {
					if ($field == $this->_fieldId) {
						$content .= $php->getLine ('if ($mode == \'add\' && array_key_exists ($pPrefix . \'' . $field->__COPIX__EXTRAS__->property . '\', $pDatas)) {', 2);
						$content .= $php->getLine ('$this->' . $field->__COPIX__EXTRAS__->settor . ' ($pDatas[$pPrefix . \'' . $field->__COPIX__EXTRAS__->property . '\']);', 3);
						$content .= $php->getLine ('}', 2);
					} else {
						$content .= $php->getLine ('if (array_key_exists ($pPrefix . \'' . $field->__COPIX__EXTRAS__->property . '\', $pDatas)) {', 2);
						$content .= $php->getLine ('$this->' . $field->__COPIX__EXTRAS__->settor . ' ($pDatas[$pPrefix . \'' . $field->__COPIX__EXTRAS__->property . '\']);', 3);
						$content .= $php->getLine ('}', 2);
					}
				}
			}
		}
		$content .= $php->getLine ('}', 1, 2);

		// isValid
		$content .= $php->getPHPDoc (array ('Indique si l\'objet est valide', null, '@return mixed'), 1);
		$content .= $php->getLine ('public function isValid () {', 1);
		$content .= $php->getLine ('return _validator (\'' . $this->_module . '|' . $this->_prefix . $this->_params['validator_class'] . '\')->check ($this);', 2);
		$content .= $php->getLine ('}', 1);

		$content .= $php->getLine ('}', 0, 0);
		$this->_write ($this->_getClassesPath (), $this->_infoClassLower . '.class.php', $content);
	}

	/**
	 * Génère le code pour l'actiongroup d'administration et ses templates
	 */
	public function generateActiongroupAdmin () {
		$php = new CopixPHPGenerator ();

		// -------------------------------------
		// -------- actiongroup d'admin --------
		// -------------------------------------

		$content = $php->getPHPDoc ('Administration ' . $this->_keyWords['des_elements']);
		$content .= $php->getLine ('class ActionGroup' . $this->_actiongroup . ' extends CopixActionGroup {');

		// _beforeAction
		$content .= $php->getPHPDoc (array ('Appelée avant toute action', null, '@param string $pAction Nom de l\'action'), 1);
		$content .= $php->getLine ('protected function _beforeAction ($pAction) {', 1);
		$content .= $php->getLine ('CopixPage::add ()->setIsAdmin (true);', 2);
		$content .= $php->getLine ('_notify (\'breadcrumb\', array (\'path\' => array (_url (\'' . $this->_trigramme . '\', array (\'page\' => _request (\'page\'), \'searchId\' => _request (\'searchId\'))) => ' . $this->_i18n ('admin.breadcrumb', $this->_params['breadcrumb']) . ')));', 2);
		if ($this->_params['credentials'] != null) {
			$content .= $php->getLine ('_currentUser ()->assertCredential (\'' . $this->_params['credentials'] . '\');', 2);
		}
		$content .= $php->getLine ('}', 1, 2);

		// _setPage
		if ($this->_menus) {
			$content .= $php->getPHPDoc (array (
				'Définit des informations sur la page',
				null,
				'@param string $pId Identifiant de la page',
				'@param string $pTitle Titre',
				'@param array $pBreadcrumb Fil d\'ariane',
				'@param string $pSelectedMenu Identifiant du menu sélectionné',
				'@return CopixPPO'
			), 1);
			$content .= $php->getLine ('private function _setPage ($pId, $pTitle, $pBreadcrumb = array (), $pSelectedMenu = null) {', 1);
		} else {
			$content .= $php->getPHPDoc (array (
				'Définit des informations sur la page',
				null,
				'@param string $pId Identifiant de la page',
				'@param string $pTitle Titre',
				'@param array $pBreadcrumb Fil d\'ariane',
				'@return CopixPPO'
			), 1);
			$content .= $php->getLine ('private function _setPage ($pId, $pTitle, $pBreadcrumb = array ()) {', 1);
		}

		$content .= $php->getLine ('$page = CopixPage::get ();', 2);
		$content .= $php->getLine ('$page->setId (\'' . $this->_trigramme . '\' . $pId);', 2);
		$content .= $php->getLine ('$page->setTitle ($pTitle);', 2, 2);
		$content .= $php->getLine ('// fil d\'ariane', 2);
		$content .= $php->getLine ('if (count ($pBreadcrumb) > 0) {', 2);
		$content .= $php->getLine ('_notify (\'breadcrumb\', array (\'path\' => $pBreadcrumb));', 3);
		$content .= $php->getLine ('}', 2, 2);
		if ($this->_menus) {
			$content .= $php->getLine ('// menus', 2);
			$content .= $php->getLine ('$page = _request (\'page\');', 2);
			$content .= $php->getLine ('$searchId = _request (\'searchId\');', 2);
			$content .= $php->getLine ('$items = array (array (\'caption\' => ' . $this->_i18n ('admin.menus.title', 'Eléments') . ', \'children\' => array (', 2);
			$content .= $php->getLine ('0 => array (', 3);
			$content .= $php->getLine ('\'id\' => \'list\',', 4);
			$content .= $php->getLine ('\'icon\' => \'img/tools/list.png\',', 4);
			$content .= $php->getLine ('\'caption\' => \'Liste\',', 4);
			$content .= $php->getLine ('\'url\' => _url (\'' . $this->_trigramme . '\', array (\'page\' => $page, \'searchId\' => $searchId))', 4);
			$content .= $php->getLine ('),', 3);
			$content .= $php->getLine ('1 => array (', 3);
			$content .= $php->getLine ('\'id\' => \'add\',', 4);
			$content .= $php->getLine ('\'icon\' => \'img/tools/add.png\',', 4);
			$content .= $php->getLine ('\'caption\' => \'Ajouter\',', 4);
			$content .= $php->getLine ('\'url\' => _url (\'' . $this->_trigramme . 'edit\', array (\'page\' => $page, \'searchId\' => $searchId))', 4);
			$content .= $php->getLine ('),', 3);
			$content .= $php->getLine (')));', 2);
			$content .= $php->getLine ('_notify (\'menu\', array (\'id\' => \'navigation\', \'items\' => $items, \'selected\' => $pSelectedMenu));', 2, 2);
		}
		$content .= $php->getLine ('return new CopixPPO (array (\'TITLE_PAGE\' => $pTitle));', 2);
		$content .= $php->getLine ('}', 1, 2);

		// _setElementMenu
		if ($this->_menus) {
			$content .= $php->getPHPDoc (array ('Ajoute un menu pour la gestion ' . $this->_keyWords['de_l_element'] . ' sélectionné', null, '@param ' . $this->_infoClass . ' $pElement Elément sélectionné', '@param string $pSelected Identifiant du menu sélectionné', '@return CopixActionReturn'), 1);
			$content .= $php->getLine ('private function _setElementMenu ($pElement, $pSelected) {', 1);
			$content .= $php->getLine ('$page = _request (\'page\');', 2);
			$content .= $php->getLine ('$searchId = _request (\'searchId\');', 2);
			$content .= $php->getLine ('$items = array (array (\'caption\' => $pElement->' . $this->_fieldCaption->__COPIX__EXTRAS__->gettor . ' (), \'children\' => array (', 2);
			$content .= $php->getLine ('0 => array (', 3);
			$content .= $php->getLine ('\'id\' => \'edit\',', 4);
			$content .= $php->getLine ('\'icon\' => \'img/tools/update.png\',', 4);
			$content .= $php->getLine ('\'caption\' => \'Modifier\',', 4);
			$content .= $php->getLine ('\'url\' => _url (\'' . $this->_trigramme . 'edit\', array (\'id\' => $pElement->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'page\' => $page, \'searchId\' => $searchId))', 4);
			$content .= $php->getLine ('),', 3);
			$content .= $php->getLine ('1 => array (', 3);
			$content .= $php->getLine ('\'id\' => \'delete\',', 4);
			$content .= $php->getLine ('\'icon\' => \'img/tools/delete.png\',', 4);
			$content .= $php->getLine ('\'caption\' => \'Supprimer\',', 4);
			$content .= $php->getLine ('\'url\' => _url (\'' . $this->_trigramme . 'delete\', array (\'id\' => $pElement->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'page\' => $page, \'searchId\' => $searchId))', 4);
			$content .= $php->getLine ('),', 3);
			$content .= $php->getLine (')));', 2);
			$content .= $php->getLine ('_notify (\'menu\', array (\'id\' => \'navigation\', \'items\' => $items, \'selected\' => $pSelected));', 2);
			$content .= $php->getLine ('}', 1, 2);
		}

		// processDefault
		$content .= $php->getPHPDoc (array ('Liste ' . $this->_keyWords['des_elements'], null, '@return CopixActionReturn'), 1);
		$content .= $php->getLine ('public function processDefault () {', 1);
		if ($this->_menus) {
			$content .= $php->getLine ('$ppo = $this->_setPage (\'default\', ' . $this->_i18n ('admin.list.title', 'Liste ' . $this->_keyWords['des_elements']) . ', array (), \'list\');', 2);
		} else {
			$content .= $php->getLine ('$ppo = $this->_setPage (\'default\', ' . $this->_i18n ('admin.list.title', 'Liste ' . $this->_keyWords['des_elements']) . ');', 2);
		}
		$content .= $php->getLine ('$ppo->highlight = _request (\'highlight\');', 2);
		$content .= $php->getLine ('$ppo->countPerPage = 20;', 2);
		$content .= $php->getLine ('$ppo->page = _request (\'page\', 1);', 2);
		$content .= $php->getLine ('$ppo->searchId = _request (\'searchId\', uniqid ());', 2);
		$content .= $php->getLine ('$ppo->search = CopixSession::get ($ppo->searchId, \'' . $this->_trigramme . 'search\', ' . $this->_fullService . '::createSearch ());', 2);
		$content .= $php->getLine ('$ppo->search->setOffset (($ppo->page - 1) * $ppo->countPerPage);', 2);
		$content .= $php->getLine ('$ppo->search->setCount ($ppo->countPerPage);', 2);
		$content .= $php->getLine ('$ppo->elements = ' . $this->_fullService . '::getList ($ppo->search);', 2);
		$content .= $php->getLine ('$ppo->countElements = ' . $this->_fullService . '::count ();', 2);
		$content .= $php->getLine ('return _arPPO ($ppo, \'' . $this->_prefixLower . '/admin.list.php\');', 2);
		$content .= $php->getLine ('}', 1, 2);

		// processSetSearch
		$content .= $php->getPHPDoc (array ('Définit les paramètres de recherche', null, '@return CopixActionReturn'), 1);
		$content .= $php->getLine ('public function processSetSearch () {', 1);
		$content .= $php->getLine ('$searchId = _request (\'searchId\', uniqid ());', 2);
		$content .= $php->getLine ('$search = CopixSession::get ($searchId, \'' . $this->_trigramme . 'search\', ' . $this->_fullService . '::createSearch ());', 2);
		foreach ($this->_fieldsSearch as $field) {
			if ($field->__COPIX__EXTRAS__->isDate) {
				foreach (array ('From', 'To') as $toAdd) {
					$suffixe = ($field->__COPIX__EXTRAS__->type == 'date' || $field->__COPIX__EXTRAS__->type == 'datetime') ? $toAdd . '_date' : $toAdd . '_hour';
					$content .= $php->getLine ('if (_request (\'' . $field->__COPIX__EXTRAS__->property . $suffixe . '\') != null) {', 2);
					$content .= $php->getLine ('$date = null;', 3);
					if ($field->__COPIX__EXTRAS__->type == 'date' || $field->__COPIX__EXTRAS__->type == 'datetime') {
						$content .= $php->getLine ('list ($day, $month, $year) = explode (\'/\', _request (\'' . $field->__COPIX__EXTRAS__->property . $toAdd . '_date\'));', 3);
						$content .= $php->getLine ('$date .= sprintf (\'%1$04d\', $year) . sprintf (\'%1$02d\', $month) . sprintf (\'%1$02d\', $day);', 3);
					}
					if ($field->__COPIX__EXTRAS__->type == 'datetime' || $field->__COPIX__EXTRAS__->type == 'time') {
						$content .= $php->getLine ('$date .= sprintf (\'%1$02d\', _request (\'' . $field->__COPIX__EXTRAS__->property . $toAdd . '_hour\')) . sprintf (\'%1$02d\', _request (\'' . $field->__COPIX__EXTRAS__->property . $toAdd . '_min\')) . \'00\';', 3);
					}
					$content .= $php->getLine ('$search->' . $field->__COPIX__EXTRAS__->settor . $toAdd . ' ($date);', 3);
					$content .= $php->getLine ('}', 2);
				}
			} else {
				$content .= $php->getLine ('$search->' . $field->__COPIX__EXTRAS__->settor . ' (_request (\'' . $field->__COPIX__EXTRAS__->property . '\'));', 2);
			}
		}
		$content .= $php->getLine ('CopixSession::set ($searchId, $search, \'' . $this->_trigramme . 'search\');', 2);
		$content .= $php->getLine ('return _arRedirect (_url (\'' . $this->_trigramme . '\', array (\'page\' => _request (\'page\'), \'searchId\' => $searchId)));', 2);
		$content .= $php->getLine ('}', 1, 2);

		// processEdit
		$content .= $php->getPHPDoc (array ('Edition ' . $this->_keyWords['d_un_element'], null, '@return CopixActionReturn'), 1);
		$content .= $php->getLine ('public function processEdit () {', 1);
		$content .= $php->getLine ('$id = _request (\'id\', null);', 2, 1);
		$content .= $php->getLine ('$ppo = new CopixPPO ();', 2, 2);

		$content .= $php->getLine ('// objet passé via la session pour affichage d\'erreurs', 2);
		$content .= $php->getLine ('if (_request (\'errors\') == \'true\') {', 2);
		$content .= $php->getLine ('$ppo->element = CopixSession::get (_request (\'sessionObject\') . \'_element\', \'' . $this->_module . '|' . $this->_actiongroupLower . '\');', 3);
		$content .= $php->getLine ('$ppo->errors = CopixSession::get (_request (\'sessionObject\') . \'_errors\', \'' . $this->_module . '|' . $this->_actiongroupLower . '\');', 3);
		$content .= $php->getLine ('$id = $ppo->element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' ();', 3);
		$content .= $php->getLine ('}', 2, 2);

		$content .= $php->getLine ('// ajout ' . $this->_keyWords['d_un_element'], 2);
		$content .= $php->getLine ('if ($id == null) {', 2);
		$content .= $php->getLine ('$breadcrumb = array (\'' . $this->_trigramme . 'edit\' => ' . $this->_i18n ('admin.add.breadcrumb', 'Ajouter') . ');', 3);
		$content .= $php->getLine ('$ppo->mode = \'add\';', 3);
		$content .= $php->getLine ('$ppo->TITLE_PAGE = ' . $this->_i18n ('admin.add.title', 'Ajouter ' . $this->_keyWords['un_element']) . ';', 3);
		$content .= $php->getLine ('if ($ppo->element == null) {', 3);
		$content .= $php->getLine ('$ppo->element = ' . $this->_fullService . '::create ();', 4);
		$content .= $php->getLine ('}', 3, 2);

		$content .= $php->getLine ('// modification ' . $this->_keyWords['d_un_element'], 2);
		$content .= $php->getLine ('} else {', 2);
		$content .= $php->getLine ('$ppo->mode = \'edit\';', 3);
		$content .= $php->getLine ('$ppo->TITLE_PAGE = ' . $this->_i18n ('admin.edit.title', 'Modifier ' . $this->_keyWords['un_element']) . ';', 3);
		$content .= $php->getLine ('if ($ppo->element == null) {', 3);
		$content .= $php->getLine ('$ppo->element = ' . $this->_fullService . '::get ($id);', 4);
		$content .= $php->getLine ('}', 3);
		$content .= $php->getLine ('$breadcrumb = array (_url (\'' . $this->_trigramme . 'edit\', array (\'id\' => $id)) => $ppo->element->' . $this->_fieldCaption->__COPIX__EXTRAS__->gettor . ' ());', 3);
		$content .= $php->getLine ('}', 2, 2);

		if ($this->_menus) {
			$content .= $php->getLine ('$this->_setPage ($ppo->mode, $ppo->TITLE_PAGE, $breadcrumb, ($ppo->mode == \'add\' ? \'add\' : null));', 2);
			$content .= $php->getLine ('if ($ppo->mode == \'edit\') {', 2);
			$content .= $php->getLine ('$this->_setElementMenu ($ppo->element, \'edit\');', 3);
			$content .= $php->getLine ('}', 2);
		} else {
			$content .= $php->getLine ('$this->_setPage ($ppo->mode, $ppo->TITLE_PAGE, $breadcrumb);', 2);
		}

		$content .= $php->getLine ('$ppo->page = _request (\'page\');', 2);
		$content .= $php->getLine ('$ppo->searchId = _request (\'searchId\');', 2);
		$content .= $php->getLine ('return _arPPO ($ppo, \'' . $this->_prefixLower . '/admin.edit.php\');', 2);
		$content .= $php->getLine ('}', 1, 2);

		// processDoEdit
		$content .= $php->getPHPDoc (array ('Effectue l\'édition ' . $this->_keyWords['d_un_element'], null, '@return CopixActionReturn'), 1);
		$content .= $php->getLine ('public function processDoEdit () {', 1);
		$content .= $php->getLine ('$mode = _request (\'mode\');', 2);
		$content .= $php->getLine ('if ($mode == \'add\') {', 2);
		$content .= $php->getLine ('$element = ' . $this->_fullService . '::create ();', 3);
		$content .= $php->getLine ('$breadcrumb = array (\'' . $this->_trigramme . 'edit\' => ' . $this->_i18n ('admin.add.breadcrumb', 'Ajouter') . ');', 3);
		$content .= $php->getLine ('} else {', 2);
		$content .= $php->getLine ('$element = ' . $this->_fullService . '::get (_request (\'' . $this->_fieldId->__COPIX__EXTRAS__->property . '\'));', 3);
		$content .= $php->getLine ('$breadcrumb = array (_url (\'' . $this->_trigramme . 'edit\', array (\'id\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' ())) => $element->' . $this->_fieldCaption->__COPIX__EXTRAS__->gettor . ' ());', 3);
		$content .= $php->getLine ('}', 2);
		if ($this->_menus) {
			$content .= $php->getLine ('if ($mode == \'add\') {', 2);
			$content .= $php->getLine ('$this->_setPage (\'do\' . strtoupper ($mode), ' . $this->_i18n ('admin.doEdit.title', 'Effectue l\'édition ' . $this->_keyWords['d_un_element']) . ', $breadcrumb, \'add\');', 3);
			$content .= $php->getLine ('} else {', 2);
			$content .= $php->getLine ('$this->_setPage (\'do\' . strtoupper ($mode), ' . $this->_i18n ('admin.doEdit.title', 'Effectue l\'édition ' . $this->_keyWords['d_un_element']) . ', $breadcrumb);', 3);
			$content .= $php->getLine ('$this->_setElementMenu ($element, \'edit\');', 3);
			$content .= $php->getLine ('}', 2, 2);
		} else {
			$content .= $php->getLine ('$this->_setPage (\'do\' . strtoupper ($mode), ' . $this->_i18n ('admin.doEdit.title', 'Effectue l\'édition ' . $this->_keyWords['d_un_element']) . ', $breadcrumb);', 2, 2);
		}

		$content .= $php->getLine ('$element->setFromArray (CopixRequest::asArray ());', 2);
		$content .= $php->getLine (null);
		$content .= $php->getLine ('try {', 2);
		$content .= $php->getLine ('if ($mode == \'add\') {', 3);
		$content .= $php->getLine ($this->_fullService . '::insert ($element);', 4);
		$content .= $php->getLine ('} else {', 3);
		$content .= $php->getLine ($this->_fullService . '::update ($element);', 4);
		$content .= $php->getLine ('}', 3);
		$content .= $php->getLine ('} catch (' . $this->_fullException . ' $e) {', 2);
		$content .= $php->getLine ('$sessionId = uniqid (\'element\');', 3);
		$content .= $php->getLine ('CopixSession::set ($sessionId . \'_element\', $element, \'' . $this->_module . '|' . $this->_actiongroupLower . '\');', 3);
		$content .= $php->getLine ('CopixSession::set ($sessionId . \'_errors\', $e->getErrors (), \'' . $this->_module . '|' . $this->_actiongroupLower . '\');', 3);
		$content .= $php->getLine ('return _arRedirect (_url (\'' . $this->_trigramme . 'edit\', array (\'errors\' => \'true\', \'sessionObject\' => $sessionId)));', 3);
		$content .= $php->getLine ('}', 2, 2);
		$content .= $php->getLine ('$params = array (', 2);
		$content .= $php->getLine ('\'title\' => ' . $this->_i18n ('admin.doEdit.confirmTitle', 'Sauvegarde effectuée') . ',', 3);
		$content .= $php->getLine ('\'redirect_url\' => _url (\'' . $this->_trigramme . '\', array (\'highlight\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'searchId\' => _request (\'searchId\'))),', 3);
		$content .= $php->getLine ('\'message\' => ' . $this->_i18n ('admin.doEdit.confirmMessage', ucfirst ($this->_keyWords['l_element']) . ' "%1$s" a été sauvegardé.', '$element->' . $this->_fieldCaption->__COPIX__EXTRAS__->gettor . ' ()') . ',', 3);
		$content .= $php->getLine ('\'links\' => array (', 3);
		$content .= $php->getLine ('_url (\'' . $this->_trigramme . 'edit\', array (\'id\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' ())) => ' . $this->_i18n ('admin.doEdit.linkEdit', 'Retour à l\'édition ' . $this->_keyWords['de_l_element']) . ',', 4);
		$content .= $php->getLine ('_url (\'' . $this->_trigramme . '\', array (\'highlight\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'searchId\' => _request (\'searchId\'))) => ' . $this->_i18n ('admin.doEdit.linkList', 'Retour à la liste ' . $this->_keyWords['des_elements']) . ',', 4);
		$content .= $php->getLine (')', 3);
		$content .= $php->getLine (');', 2);
		$content .= $php->getLine ('return CopixActionGroup::process (\'generictools|messages::getInformation\', $params);', 2);
		$content .= $php->getLine ('}', 1, 2);

		// processUp
		if ($this->_fieldPosition != null) {
			$content .= $php->getPHPDoc (array ('Monte ' . $this->_keyWords['l_element'] . ' dans la liste', null, '@return CopixActionReturn'), 1);
			$content .= $php->getLine ('public function processUp () {', 1);
			$content .= $php->getLine ('$this->_setPage (\'up\', ' . $this->_i18n ('admin.up.title', 'Monte ' . $this->_keyWords['l_element'] . ' dans la liste') . ');', 2);
			$content .= $php->getLine ('CopixRequest::assert (\'' . $this->_fieldId->__COPIX__EXTRAS__->property . '\');', 2, 2);
			$content .= $php->getLine ('$element = ' . $this->_fullService . '::get (_request (\'' . $this->_fieldId->__COPIX__EXTRAS__->property . '\'));', 2);
			$content .= $php->getLine ($this->_fullService . '::' . $this->_fieldPosition->__COPIX__EXTRAS__->settor . ' ($element, $element->' . $this->_fieldPosition->__COPIX__EXTRAS__->gettor . ' () - 1);', 2);
			$content .= $php->getLine ('return _arRedirect (_url (\'' . $this->_trigramme . '\', array (\'highlight\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'page\' => _request (\'page\'), \'searchId\' => _request (\'searchId\'))));', 2);
			$content .= $php->getLine ('}', 1, 2);
		}

		// processDown
		if ($this->_fieldPosition != null) {
			$content .= $php->getPHPDoc (array ('Descend ' . $this->_keyWords['l_element'] . ' dans la liste', null, '@return CopixActionReturn'), 1);
			$content .= $php->getLine ('public function processDown () {', 1);
			$content .= $php->getLine ('$this->_setPage (\'down\', ' . $this->_i18n ('admin.down.title', 'Descend ' . $this->_keyWords['l_element'] . ' dans la liste') . ');', 2);
			$content .= $php->getLine ('CopixRequest::assert (\'' . $this->_fieldId->__COPIX__EXTRAS__->property . '\');', 2, 2);
			$content .= $php->getLine ('$element = ' . $this->_fullService . '::get (_request (\'' . $this->_fieldId->__COPIX__EXTRAS__->property . '\'));', 2);
			$content .= $php->getLine ($this->_fullService . '::' . $this->_fieldPosition->__COPIX__EXTRAS__->settor . ' ($element, $element->' . $this->_fieldPosition->__COPIX__EXTRAS__->gettor . ' () + 1);', 2);
			$content .= $php->getLine ('return _arRedirect (_url (\'' . $this->_trigramme . '\', array (\'highlight\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'page\' => _request (\'page\'), \'searchId\' => _request (\'searchId\'))));', 2);
			$content .= $php->getLine ('}', 1, 2);
		}

		// processSetStatus
		if ($this->_fieldStatus != null) {
			$content .= $php->getPHPDoc (array ('Définit le statut ' . $this->_keyWords['d_un_element'], null, '@return CopixActionReturn'), 1);
			$content .= $php->getLine ('public function processSetEnabled () {', 1);
			$content .= $php->getLine ('$this->_setPage (\'setEnabled\', ' . $this->_i18n ('admin.setEnabled.title', 'Définit le statut ' . $this->_keyWords['d_un_element']) . ');', 2);
			$content .= $php->getLine ('CopixRequest::assert (\'' . $this->_fieldId->__COPIX__EXTRAS__->property . '\', \'' . $this->_fieldStatus->__COPIX__EXTRAS__->property . '\');', 2, 2);
			$content .= $php->getLine ('$element = ' . $this->_fullService . '::get (_request (\'' . $this->_fieldId->__COPIX__EXTRAS__->property . '\'));', 2);
			$content .= $php->getLine ('$element->' . $this->_fieldStatus->__COPIX__EXTRAS__->settor . ' (_request (\'' . $this->_fieldStatus->__COPIX__EXTRAS__->property . '\'));', 2);
			$content .= $php->getLine ($this->_fullService . '::update ($element);', 2);
			$content .= $php->getLine ('return _arRedirect (_url (\'' . $this->_trigramme . '\', array (\'highlight\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'searchId\' => _request (\'searchId\'))));', 2);
			$content .= $php->getLine ('}', 1, 2);
		}

		// processDelete
		$content .= $php->getPHPDoc (array ('Demande confirmation de suppression ' . $this->_keyWords['d_un_element'], null, '@return CopixActionReturn'), 1);
		$content .= $php->getLine ('public function processDelete () {', 1);
		$content .= $php->getLine ('$element = ' . $this->_fullService . '::get (_request (\'' . $this->_fieldId->__COPIX__EXTRAS__->property . '\'));', 2);
		$content .= $php->getLine ('$page = _request (\'page\');', 2);
		$content .= $php->getLine ('$searchId = _request (\'searchId\');', 2);
		$content .= $php->getLine ('$breadcrumb = array (', 2);
		$content .= $php->getLine ('_url (\'' . $this->_trigramme . 'edit\', array (\'id\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'page\' => $page, \'searchId\' => $searchId)) => $element->' . $this->_fieldCaption->__COPIX__EXTRAS__->gettor . ' (),', 3);
		$content .= $php->getLine ('_url (\'' . $this->_trigramme . 'delete\', array (\'id\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'page\' => $page, \'searchId\' => $searchId)) => ' . $this->_i18n ('admin.breadcrumb.delete', 'Supprimer'), 3);
		$content .= $php->getLine (');', 2);
		$content .= $php->getLine ('$this->_setPage (\'delete\', ' . $this->_i18n ('admin.delete.title', 'Confirmation de suppression') . ', $breadcrumb);', 2);
		if ($this->_menus) {
			$content .= $php->getLine ('$this->_setElementMenu ($element, \'delete\');', 2);
		}
		$content .= $php->getLine (null, 2);
		$content .= $php->getLine ('$params = array (', 2);
		$content .= $php->getLine ('\'message\' => ' . $this->_i18n ('admin.delete.confirmMessage', 'Etes-vous sur de vouloir supprimer ' . $this->_keyWords['l_element'] . ' "%1$s" ?', '$element->' . $this->_fieldCaption->__COPIX__EXTRAS__->gettor . ' ()') . ',', 3);
		$content .= $php->getLine ('\'confirm\' => _url (\'' . $this->_trigramme . 'doDelete\', array (\'' . $this->_fieldId->__COPIX__EXTRAS__->property . '\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'searchId\' => _request (\'searchId\'))),', 3);
		$content .= $php->getLine ('\'cancel\' => _url (\'' . $this->_trigramme . '\', array (\'page\' => $page, \'searchId\' => $searchId))', 3);
		$content .= $php->getLine (');', 2);
		$content .= $php->getLine ('return CopixActionGroup::process (\'generictools|Messages::getConfirm\', $params);', 2);
		$content .= $php->getLine ('}', 1, 2);

		// processDoDelete
		$content .= $php->getPHPDoc (array ('Effectue la suppression ' . $this->_keyWords['d_un_element'], null, '@return CopixActionReturn'), 1);
		$content .= $php->getLine ('public function processDoDelete () {', 1);
		$content .= $php->getLine ('$element = ' . $this->_fullService . '::get (_request (\'' . $this->_fieldId->__COPIX__EXTRAS__->property . '\'));', 2);
		$content .= $php->getLine ('$page = _request (\'page\');', 2);
		$content .= $php->getLine ('$searchId = _request (\'searchId\');', 2);
		$content .= $php->getLine ('$breadcrumb = array (', 2);
		$content .= $php->getLine ('_url (\'' . $this->_trigramme . 'edit\', array (\'id\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'page\' => $page, \'searchId\' => $searchId)) => $element->' . $this->_fieldCaption->__COPIX__EXTRAS__->gettor . ' (),', 3);
		$content .= $php->getLine ('_url (\'' . $this->_trigramme . 'delete\', array (\'id\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'page\' => $page, \'searchId\' => $searchId)) => ' . $this->_i18n ('admin.breadcrumb.delete', 'Supprimer'), 3);
		$content .= $php->getLine (');', 2);
		$content .= $php->getLine ('$this->_setPage (\'doDelete\', ' . $this->_i18n ('admin.doDelete.title', 'Effectue la suppression ' . $this->_keyWords['d_un_element']) . ', $breadcrumb);', 2);
		if ($this->_menus) {
			$content .= $php->getLine ('$this->_setElementMenu ($element, \'delete\');', 2);
		}
		$content .= $php->getLine (null, 2);
		$content .= $php->getLine ($this->_fullService . '::delete ($element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' ());', 2, 2);
		$content .= $php->getLine ('$params = array (', 2);
		$content .= $php->getLine ('\'title\' => ' . $this->_i18n ('admin.doDelete.confirmTitle', 'Suppression effectuée') . ',', 3);
		$content .= $php->getLine ('\'message\' => ' . $this->_i18n ('admin.doDelete.confirmMessage', 'La suppression de ' . $this->_keyWords['l_element'] . ' "%1$s" a été effectuée.', '$element->' . $this->_fieldCaption->__COPIX__EXTRAS__->gettor . ' ()') . ',', 3);
		$content .= $php->getLine ('\'redirect_url\' => _url (\'' . $this->_trigramme . '\', array (\'searchId\' => $searchId)),', 3);
		$content .= $php->getLine ('\'links\' => array (_url (\'' . $this->_trigramme . '\', array (\'searchId\' => $searchId)) => ' . $this->_i18n ('admin.doDelete.linkList', 'Retour à la liste ' . $this->_keyWords['des_elements']) . ')', 3);
		$content .= $php->getLine (');', 2);
		$content .= $php->getLine ('return CopixActionGroup::process (\'generictools|messages::getInformation\', $params);', 2);
		$content .= $php->getLine ('}', 1);

		$content .= $php->getLine ('}', 0, 0);
		$this->_write ($this->_getActiongroupsPath (), $this->_actiongroupLower . '.actiongroup.php', $content);

		// -------------------------------------
		// ---------- zone d'édition -----------
		// -------------------------------------

		$content = $php->getPHPDoc ('Edition d\'un objet ' . $this->_infoClass);
		$content .= $php->getLine ('class ZoneEdit' . $this->_infoClass . ' extends CopixZone {');

		$content .= $php->getPHPDoc (array ('Génération du contenu de la zone', null, '@param string $pToReturn', '@return boolean'), 1);
		$content .= $php->getLine ('protected function _createContent (&$pToReturn) {', 1);
		$content .= $php->getLine ('$ppo = _ppo ();', 2);
		$content .= $php->getLine ('if ($this->getParam (\'' . $this->_infoClassLower . '\') instanceof ' . $this->_fullClass . ') {', 2);
		$content .= $php->getLine ('$ppo->element = $this->getParam (\'' . $this->_infoClassLower . '\');', 3);
		$content .= $php->getLine ('} else if ($this->getParam (\'id\') !== null) {', 2);
		$content .= $php->getLine ('$ppo->element = ' . $this->_fullService . '::get ($this->getParam (\'id\'));', 3);
		$content .= $php->getLine ('} else {', 2);
		$content .= $php->getLine ('$ppo->element = ' . $this->_fullService . '::create ();', 3);
		$content .= $php->getLine ('}', 2);
		$content .= $php->getLine ('$ppo->errors = $this->getParam (\'errors\', array ());', 2);
		$content .= $php->getLine ('$ppo->inputId = $this->getParam (\'inputId\');', 2);
		$content .= $php->getLine ('$pToReturn = $this->_usePPO ($ppo, \'' . $this->_module . '|' . $this->_prefixLower . '/edit' . $this->_infoClassLower . '.zone.php\');', 2);
		$content .= $php->getLine ('return true;', 2);
		$content .= $php->getLine ('}', 1);

		$content .= $php->getLine ('}', 0, 0);
		$this->_write ($this->_getZonesPath (), 'edit' . $this->_infoClassLower . '.zone.php', $content);

		// -------------------------------------
		// ------ template editX.zone.php ------
		// -------------------------------------

		$content = $php->getLine ('<table class="CopixVerticalTable">');
		$index = 0;
		foreach ($this->_fields as $field) {
			if ($field->__COPIX__EXTRAS__->isEditable) {
				$content .= $php->getLine ('<tr <?php _eTag (\'trclass\', array (\'id\' => \'editElement\')) ?>>', 1);
				$class = ($index == $this->_countEditable - 1) ? ' class="last"' : null;
				$caption = ($field->required == 'yes') ? $field->__COPIX__EXTRAS__->caption . ' <span class="required">*</span>' : $field->__COPIX__EXTRAS__->caption;
				$style = ($index == 0) ? 'width: 140px;' : null;
				if ($field->__COPIX__EXTRAS__->type == 'tinymce' || $field->__COPIX__EXTRAS__->type == 'textarea') {
					$style .= 'vertical-align: top;';
				}
				if ($style != null) {
					$content .= $php->getLine ('<th style="' . $style . '"' . $class . '>' . $caption . '</th>', 2);
				} else {
					$content .= $php->getLine ('<th' . $class . '>' . $caption . '</th>', 2);
				}

				$content .= $php->getLine ('<td>', 2);
				if ($field == $this->_fieldId) {
					$content .= $php->getLine ('<?php if ($ppo->mode == \'add\') { ?>', 3);
					$content .= $this->_getPHP4Input ($field, '$ppo->inputId', $field->__COPIX__EXTRAS__->property, '$ppo->element->' . $field->__COPIX__EXTRAS__->gettor, 4, false);
					$content .= $php->getLine ('<?php } else { ?>', 3);
					$content .= $php->getLine ('<?php echo $ppo->element->' . $field->__COPIX__EXTRAS__->gettor . ' () ?>', 4);
					$content .= $php->getLine ('<?php } ?>', 3);
				} else {
					$content .= $this->_getPHP4Input ($field, '$ppo->inputId', $field->__COPIX__EXTRAS__->property, '$ppo->element->' . $field->__COPIX__EXTRAS__->gettor, 3, false);
				}
				$content .= $php->getLine ('</td>', 2);

				$content .= $php->getLine ('</tr>', 1);
				$index++;
			}
		}
		$content .= $php->getLine ('</table>', 0, 0);
		$this->_write ($this->_getTemplatesPath (), 'edit' . $this->_infoClassLower . '.zone.php', $content, false);

		// -------------------------------------
		// ------ template admin.list.php ------
		// -------------------------------------

		$content = null;
		$isFirst = 'true';

		// moteur de recherche
		if (isset ($this->_params['search']) && $this->_params['search'] == 'yes') {
			$content .= $php->getLine ('<?php _eTag (\'beginblock\', array (\'title\' => ' . $this->_i18n ('template.adminList.search', 'Recherche') . ', \'isFirst\' => true)) ?>');
			$content .= $php->getLine ('<div id="searchBlockHidden" style="display: none">');
			if ($this->_linksButtons) {
				$content .= $php->getLine ('<?php _eTag (\'button\', array (\'type\' => \'button\', \'caption\' => ' . $this->_i18n ('admin.showSearch', 'Afficher') . ', \'extra\' => \'onclick="showSearchBlock (true)"\', \'img\' => \'img/tools/show.png\')) ?>', 1);
			} else {
				$content .= $php->getLine ('<a href="#" onclick="return showSearchBlock (true)"><img src="<?php echo _resource (\'img/tools/show.png\') ?>" /> ' . $this->_i18n ('admin.showSearch', 'Afficher la recherche') . '</a>', 1);
			}
			$content .= $php->getLine ('</div>', 0, 2);
			$content .= $php->getLine ('<div id="searchBlock">');
			$content .= $php->getLine ('<form action="<?php echo _url (\'' . $this->_trigramme . 'setSearch\') ?>" method="POST">', 1);
			$content .= $php->getLine ('<input type="hidden" name="page" value="<?php echo $ppo->page ?>">', 1);
			$content .= $php->getLine ('<input type="hidden" name="searchId" value="<?php echo $ppo->searchId ?>">', 1);
			$content .= $php->getLine ('<table class="CopixVerticalTable">', 1);
			$isFirstTr = true;
			foreach ($this->_fieldsSearch as $field) {
				// étoiles ou date
				if ($field->__COPIX__EXTRAS__->type == 'stars' || $field->__COPIX__EXTRAS__->isDate) {
					$width = ($isFirstTr) ? ' style="width: 140px"' : null;
					$content .= $php->getLine ('<tr <?php _eTag (\'trclass\', array (\'id\' => \'search\')) ?>>', 2);
					$content .= $php->getLine ('<th' . $width . '>' . $field->__COPIX__EXTRAS__->caption . ' (début)</th>', 3);
					$content .= $php->getLine ('<td>', 3);
					$content .= $this->_getPHP4Input ($field, null, $field->__COPIX__EXTRAS__->property . 'From', '$ppo->search->' . $field->__COPIX__EXTRAS__->gettor . 'From', 4, true);
					$content .= $php->getLine ('</td>', 3);
					$content .= $php->getLine ('</tr>', 2);
					$content .= $php->getLine ('<tr <?php _eTag (\'trclass\', array (\'id\' => \'search\')) ?>>', 2);
					$content .= $php->getLine ('<th>' . $field->__COPIX__EXTRAS__->caption . ' (fin)</th>', 3);
					$content .= $php->getLine ('<td>', 3);
					$content .= $this->_getPHP4Input ($field, null, $field->__COPIX__EXTRAS__->property . 'To', '$ppo->search->' . $field->__COPIX__EXTRAS__->gettor . 'To', 4, true);
					$content .= $php->getLine ('</td>', 3);
					$content .= $php->getLine ('</tr>', 2);

				// tous les autres champs
				} else {
					$width = ($isFirstTr) ? ' style="width: 140px"' : null;
					$content .= $php->getLine ('<tr <?php _eTag (\'trclass\', array (\'id\' => \'search\')) ?>>', 2);
					$content .= $php->getLine ('<th' . $width . '>' . $field->__COPIX__EXTRAS__->caption . '</th>', 3);
					$content .= $php->getLine ('<td>', 3);
					$content .= $this->_getPHP4Input ($field, null, $field->__COPIX__EXTRAS__->property, '$ppo->search->' . $field->__COPIX__EXTRAS__->gettor, 4, true);
					$content .= $php->getLine ('</td>', 3);
					$content .= $php->getLine ('</tr>', 2);
				}

				$isFirstTr = false;
			}
			$content .= $php->getLine ('</table>', 1);
			$content .= $php->getLine ('<br />', 1);
			$content .= $php->getLine ('<table style="width: 100%">', 1);
			$content .= $php->getLine ('<tr>', 2);
			$content .= $php->getLine ('<td style="width: 33%"></td>', 3);
			$content .= $php->getLine ('<td style="text-align: center"><?php _eTag (\'button\', array (\'action\' => \'search\')) ?></td>', 4);
			$content .= $php->getLine ('<td style="width: 33%; text-align: right">', 3);
			if ($this->_linksButtons) {
				$content .= $php->getLine ('<?php _eTag (\'button\', array (\'type\' => \'button\', \'caption\' => ' . $this->_i18n ('admin.closeSearch', 'Fermer') . ', \'extra\' => \'onclick="showSearchBlock (false)"\', \'img\' => \'img/tools/close.png\')) ?>', 4);
			} else {
				$content .= $php->getLine ('<a href="#" onclick="return showSearchBlock (false)"><img src="<?php echo _resource (\'img/tools/close.png\') ?>" /> ' . $this->_i18n ('admin.closeSearch', 'Fermer la recherche', array (), false) . '</a>', 4);
			}
			$content .= $php->getLine ('</td>', 3);
			$content .= $php->getLine ('</tr>', 2);
			$content .= $php->getLine ('</table>', 1);
			$content .= $php->getLine ('</form>', 1);
			$content .= $php->getLine ('</div>', 0, 2);

			$content .= $php->getLine ('<script type="text/javascript">');
			$content .= $php->getLine ('function showSearchBlock (pShow, pSavePreference) { ');
			$content .= $php->getLine ('$ (\'searchBlock\').setStyle (\'display\', pShow ? \'block\' : \'none\');', 1);
			$content .= $php->getLine ('$ (\'searchBlockHidden\').setStyle (\'display\', pShow ? \'none\' : \'block\');', 1);
			$content .= $php->getLine ('if (pSavePreference == undefined || pSavePreference) {', 1);
			$content .= $php->getLine ('Copix.savePreference (\'' . $this->_trigramme . 'showSearch\', pShow);', 2);
			$content .= $php->getLine ('}', 1);
			$content .= $php->getLine ('return false;', 1);
			$content .= $php->getLine ('}');
			$content .= $php->getLine ('showSearchBlock (<?php echo CopixUserPreferences::get (\'' . $this->_trigramme . 'showSearch\', \'true\') ?>, false);');
			$content .= $php->getLine ('</script>');

			$content .= $php->getLine ('<?php _eTag (\'endblock\') ?>', 0, 2);
			$isFirst = 'false';
		}

		// liste des éléments
		$i18nCaption = '<?php echo ' . $this->_i18n ('template.adminList.caption', $this->_fieldCaption->__COPIX__EXTRAS__->caption) . ' ?>';
		$linkEdit = '<a href="<?php echo _url (\'' . $this->_trigramme . 'edit\', array (\'id\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'page\' => $ppo->page, \'searchId\' => $ppo->searchId)) ?>">';

		$content .= $php->getLine ('<?php _eTag (\'beginblock\', array (\'title\' => ' . $this->_i18n ('template.adminList.elementsList', 'Liste ' . $this->_keyWords['des_elements']) . ', \'isFirst\' => ' . $isFirst . ')) ?>');
		$content .= $php->getLine ('<?php if (count ($ppo->elements) <= 0) { ?>');
		$content .= $php->getLine ('<?php echo ' . $this->_i18n ('template.adminList.noElements', 'Aucun élément disponible.<br />') . ' ?><br />', 1);
		$content .= $php->getLine ('<?php } else { ?>');
		$content .= $php->getLine ('<table class="CopixTable">', 1);
		$content .= $php->getLine ('<tr>', 2);
		$countList = 1;
		foreach ($this->_fields as $field) {
			if ($field->__COPIX__EXTRAS__->list) {
				$countList++;
			}
		}
		$width = ($countList > 1) ? ' style="width: ' . round (100 / $countList) . '%"' : null;
		$content .= $php->getLine ('<th' . $width . '>' . $i18nCaption . '</th>', 3);
		$currentCountList = 1;
		foreach ($this->_fields as $field) {
			if ($field->__COPIX__EXTRAS__->list) {
				$currentCountList++;
				$content .= $php->getLine ('<th' . ($countList > $currentCountList ? $width : null) . '>' . $field->__COPIX__EXTRAS__->caption . '</th>', 3);
			}
		}
		$colspan = 2;
		if ($this->_fieldPosition != null) {
			$colspan += 2;
		}
		if ($this->_fieldStatus != null) {
			$colspan += 1;
		}
		$content .= $php->getLine ('<th class="last" colspan="' . $colspan . '"></th>', 3);
		$content .= $php->getLine ('</tr>', 2);

		$content .= $php->getLine ('<?php foreach ($ppo->elements as $element) { ?>', 2);
		$html = '<tr <?php _eTag (\'trclass\', array (\'id\' => \'elements\', ';
		$html .= '\'highlight\' => ($element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' () == $ppo->highlight))) ?>>';
		$content .= $php->getLine ($html, 3);
		$content .= $php->getLine ('<td>' . $linkEdit . '<?php echo $element->' . $this->_fieldCaption->__COPIX__EXTRAS__->gettor . ' () ?></a></td>', 4);
		foreach ($this->_fields as $field) {
			if ($field->__COPIX__EXTRAS__->list) {
				if ($field->__COPIX__EXTRAS__->phpType == 'float' || $field->__COPIX__EXTRAS__->phpType == 'int') {
					$content .= $php->getLine ('<td><?php echo $element->' . $field->__COPIX__EXTRAS__->gettor . ' (true) ?></td>', 4);
				} else {
					$content .= $php->getLine ('<td><?php echo $element->' . $field->__COPIX__EXTRAS__->gettor . ' () ?></td>', 4);
				}
			}
		}

		// edition
		$content .= $php->getLine ('<td class="action">', 4);
		$content .= $php->getLine ($linkEdit . '<?php _eTag (\'copixicon\', array (\'type\' => \'update\'))?></a>', 5);
		$content .= $php->getLine ('</td>', 4);

		// move up
		if ($this->_fieldPosition != null) {
			$content .= $php->getLine ('<td class="action">', 4);
			$if = array ('$element->' . $this->_fieldPosition->__COPIX__EXTRAS__->gettor . ' () > 1');
			if ($this->_fieldStatus != null) {
				$if[] = '$element->' . $this->_fieldStatus->__COPIX__EXTRAS__->gettor . ' ()';
			}
			$content .= $php->getLine ('<?php if (' . implode (' && ', $if) . ') { ?>', 5);
			$content .= $php->getLine ('<a href="<?php echo _url (\'' . $this->_trigramme . 'up\', array (\'id\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'page\' => $ppo->page, \'searchId\' => $ppo->searchId)) ?>"', 6);
			$i18n = $this->_i18n ('template.adminList.up', 'Monter');
			$content .= $php->getLine ('><img src="<?php echo _resource (\'img/tools/up.png\') ?>" alt="<?php echo ' . $i18n . ' ?>" title="<?php echo ' . $i18n . ' ?>" />', 7);
			$content .= $php->getLine ('</a>', 6);
			$content .= $php->getLine ('<?php } ?>', 5);
			$content .= $php->getLine ('</td>', 4);
		}

		// move down
		if ($this->_fieldPosition != null) {
			$content .= $php->getLine ('<td class="action">', 4);
			$if = array ('($element->' . $this->_fieldPosition->__COPIX__EXTRAS__->gettor . ' () < $ppo->countElements)');
			if ($this->_fieldStatus != null) {
				$if[] = '($element->' . $this->_fieldStatus->__COPIX__EXTRAS__->gettor . ' ())';
			}
			$content .= $php->getLine ('<?php if (' . implode (' && ', $if) . ') { ?>', 5);
			$content .= $php->getLine ('<a href="<?php echo _url (\'' . $this->_trigramme . 'down\', array (\'id\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'page\' => $ppo->page, \'searchId\' => $ppo->searchId)) ?>"', 6);
			$i18n = $this->_i18n ('template.adminList.down', 'Descendre');
			$content .= $php->getLine ('><img src="<?php echo _resource (\'img/tools/down.png\') ?>" alt="<?php echo ' . $i18n . ' ?>" title="<?php echo ' . $i18n . ' ?>" />', 7);
			$content .= $php->getLine ('</a>', 6);
			$content .= $php->getLine ('<?php } ?>', 5);
			$content .= $php->getLine ('</td>', 4);
		}

		// statut
		if ($this->_fieldStatus != null) {
			$content .= $php->getLine ('<td class="action">', 4);
			$content .= $php->getLine ('<?php if ($element->' . $this->_fieldStatus->__COPIX__EXTRAS__->gettor . ' ()) { ?>', 5);
			$content .= $php->getLine ('<a href="<?php echo _url (\'' . $this->_trigramme . 'setEnabled\', array (\'id\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'' . $this->_fieldStatus->__COPIX__EXTRAS__->property . '\' => \'false\', \'page\' => $ppo->page, \'searchId\' => $ppo->searchId)) ?>"', 6);
			$i18n = $this->_i18n ('template.adminList.disable', 'Désativer');
			$content .= $php->getLine ('><img src="<?php echo _resource (\'img/tools/enable.png\') ?>" alt="<?php echo ' . $i18n . ' ?>" title="<?php echo ' . $i18n . ' ?>" />', 7);
			$content .= $php->getLine ('</a>', 6);
			$content .= $php->getLine ('<?php } else { ?>', 5);
			$content .= $php->getLine ('<a href="<?php echo _url (\'' . $this->_trigramme . 'setEnabled\', array (\'id\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'' . $this->_fieldStatus->__COPIX__EXTRAS__->property . '\' => \'true\', \'page\' => $ppo->page, \'searchId\' => $ppo->searchId)) ?>"', 6);
			$i18n = $this->_i18n ('template.adminList.enable', 'Activer');
			$content .= $php->getLine ('><img src="<?php echo _resource (\'img/tools/disable.png\') ?>" alt="<?php echo ' . $i18n . ' ?>" title="<?php echo ' . $i18n . ' ?>" />', 7);
			$content .= $php->getLine ('</a>', 6);
			$content .= $php->getLine ('<?php } ?>', 4);
			$content .= $php->getLine ('</td>', 4);
		}

		// suppression
		$content .= $php->getLine ('<td class="action">', 4);
		$content .= $php->getLine ('<a href="<?php echo _url (\'' . $this->_trigramme . 'delete\', array (\'id\' => $element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' (), \'page\' => 1, \'searchId\' => $ppo->searchId)) ?>"', 5);
		$content .= $php->getLine ('><?php _eTag (\'copixicon\', array (\'type\' => \'delete\')) ?>', 6);
		$content .= $php->getLine ('</a>', 5);
		$content .= $php->getLine ('</td>', 4);

		$content .= $php->getLine ('</tr>', 3);
		$content .= $php->getLine ('<?php } ?>', 2);

		$content .= $php->getLine ('</table>', 1);

		$content .= $php->getLine ('<br />', 1);
		$content .= $php->getLine ('<center><?php echo CopixPager::getHTML ($ppo->countElements, $ppo->countPerPage, _url (\'' . $this->_trigramme . '\', array (\'page\' => \'__page__\', \'searchId\' => $ppo->searchId)), $ppo->page) ?></center>', 1);

		$content .= $php->getLine ('<?php } ?>');
		$content .= $php->getLine ('<?php _eTag (\'endblock\') ?>', 0, 2);

		// liens ajouter / retour
		$content .= $php->getLine ('<table style="width: 100%">');
		$content .= $php->getLine ('<tr>', 1);
		$content .= $php->getLine ('<td style="width: 50%">', 2);
		if ($this->_linksButtons) {
			$content .= $php->getLine ('<?php _eTag (\'button\', array (\'type\' => \'button\', \'caption\' => ' . $this->_i18n ('template.adminList.addElement', 'Ajouter') . ', \'url\' => _url (\'' . $this->_trigramme . 'edit\', array (\'page\' => $ppo->page, \'searchId\' => $ppo->searchId)), \'img\' => \'img/tools/add.png\')) ?>', 3);
		} else {
			$content .= $php->getLine ('<a href="<?php echo _url (\'' . $this->_trigramme . 'edit\', array (\'page\' => $ppo->page, \'searchId\' => $ppo->searchId)) ?>"><?php _eTag (\'copixicon\', array (\'type\' => \'add\')) ?> ' . $this->_i18n ('template.adminList.addElement', 'Ajouter ' . $this->_keyWords['un_element']) . '</a>', 3);
		}
		$content .= $php->getLine ('</td>', 2);
		$content .= $php->getLine ('<td><?php _eTag (\'back\', array (\'url\' => \'admin||\')) ?></td>', 2);
		$content .= $php->getLine ('</tr>', 1);
		$content .= $php->getLine ('</table>', 0, 0);

		$this->_write ($this->_getTemplatesPath (), 'admin.list.php', $content, false);

		// -------------------------------------
		// ------ template admin.edit.php ------
		// -------------------------------------

		$content = null;

		// inclusion de tiny_mce_src.js
		foreach ($this->_fields as $field) {
			if ($field->__COPIX__EXTRAS__->isEditable && $field->__COPIX__EXTRAS__->type == 'tinymce') {
				$content .= $php->getLine ('<?php CopixHTMLHeader::addJSLink (_resource (\'js/tiny_mce/tiny_mce_src.js\'), array (\'concat\' => false)); ?>', 0, 2);
			}
		}

		$content .= $php->getLine ('<?php _eTag (\'error\', array (\'message\' => $ppo->errors)) ?>', 0, 2);
		$content .= $php->getLine ('<form action="<?php echo _url (\'' . $this->_trigramme . 'doEdit\') ?>" method="POST">');
		$content .= $php->getLine ('<input type="hidden" name="' . $this->_fieldId->__COPIX__EXTRAS__->property . '" value="<?php echo $ppo->element->' . $this->_fieldId->__COPIX__EXTRAS__->gettor . ' () ?>" />');
		$content .= $php->getLine ('<input type="hidden" name="mode" value="<?php echo $ppo->mode ?>" />', 0);
		$content .= $php->getLine ('<input type="hidden" name="page" value="<?php echo $ppo->page ?>" />', 0);
		$content .= $php->getLine ('<input type="hidden" name="searchId" value="<?php echo $ppo->searchId ?>" />', 0, 2);
		$content .= $php->getLine ('<?php _eTag (\'beginblock\', array (\'title\' => ' . $this->_i18n ('template.adminEdit.informations', 'Informations') . ', \'isFirst\' => true)) ?>');
		$content .= $php->getLine ('<?php echo CopixZone::process (\'Edit' . $this->_infoClass . '\', array (\'' . $this->_infoClassLower . '\' => $ppo->element, \'errors\' => $ppo->errors)) ?>');
		$content .= $php->getLine ('<?php _eTag (\'endblock\') ?>', 0, 2);

		// submit et lien retour
		$content .= $php->getLine ('<table style="width: 100%">');
		$content .= $php->getLine ('<tr>', 1);
		$content .= $php->getLine ('<td style="width: 20%"></td>', 2);
		$content .= $php->getLine ('<td style="text-align: center">', 2);
		$content .= $php->getLine ('<?php', 3);
		$content .= $php->getLine ('if ($ppo->mode == \'add\') {', 3);
		$content .= $php->getLine ('_eTag (\'button\', array (\'caption\' => \'Ajouter\', \'img\' => \'img/tools/save.png\'));', 4);
		$content .= $php->getLine ('} else {', 3);
		$content .= $php->getLine ('_eTag (\'button\', array (\'action\' => \'save\'));', 4);
		$content .= $php->getLine ('}', 3);
		$content .= $php->getLine ('?>', 3);
		$content .= $php->getLine ('&nbsp;&nbsp;', 3);
		$content .= $php->getLine ('<?php _eTag (\'button\', array (\'action\' => \'cancel\', \'url\' => _url (\'' . $this->_trigramme . '\', array (\'page\' => $ppo->page, \'searchId\' => $ppo->searchId)))) ?>', 3);
		$content .= $php->getLine ('</td>', 2);
		$content .= $php->getLine ('<td style="width: 20%; text-align: right"><?php _eTag (\'back\', array (\'url\' => _url (\'' . $this->_trigramme . '\', array (\'page\' => $ppo->page, \'searchId\' => $ppo->searchId)))) ?></td>', 2);
		$content .= $php->getLine ('</tr>', 1);
		$content .= $php->getLine ('</table>');
		$content .= $php->getLine ('</form>', 0, 0);

		$this->_write ($this->_getTemplatesPath (), 'admin.edit.php', $content, false);

		// -------------------------------------
		// ------------ module.xml ------------
		// -------------------------------------

		$moduleXML = $this->_path . 'module.xml';
		if (is_writable ($moduleXML)) {
			$addLink = true;
			$xml = simplexml_load_file ($moduleXML);

			// création de la node admin si elle n'existe pas
			if (!isset ($xml->admin)) {
				$xml->addChild ('admin');
			}

			// recherche de l'existance du lien à créer pour ne pas le faire 2 fois
			if (isset ($xml->admin->link)) {
				foreach ($xml->admin->link as $node) {
					$attributes = $node->attributes ();
					if (isset ($attributes['url']) && (string)$attributes['url'] == $this->_trigramme) {
						$addLink = false;
					}
				}
			}

			// création du lien d'admin
			if ($addLink) {
				$node = $xml->admin->addChild ('link');
				$node->addAttribute ('url', $this->_trigramme);
				$node->addAttribute ('caption', 'Administration ' . $this->_keyWords['des_elements']);
				if ($this->_params['credentials'] != null) {
					$node->addAttribute ('credentials', $this->_params['credentials']);
				}
			}

			CopixFile::write ($moduleXML, $xml->asXML ());
		} else {
			echo 'Le fichier "' . $moduleXML . '" n\'a pas les droits d\'écriture ou n\'existe pas.<br />';
		}
	}

	/**
	 * Génération de la classe de recherche
	 */
	public function generateSearchClass () {
		$php = new CopixPHPGenerator ();
		$content = $php->getPHPDoc ('Recherche ' . $this->_keyWords['d_un_element']);
		$content .= $php->getLine ('class ' . $this->_prefix . $this->_searchClass . ' {');

		// propriétés
		foreach ($this->_fieldsSearch as $field) {
			// champs avec une recherche "depuis" et "jusqu'à"
			if ($field->__COPIX__EXTRAS__->type == 'stars' || $field->__COPIX__EXTRAS__->isDate) {
				$content .= $php->getPHPDoc (array ($field->__COPIX__EXTRAS__->caption, null, '@var ' . $field->__COPIX__EXTRAS__->phpType), 1);
				$content .= $php->getLine ('private $_' . $field->__COPIX__EXTRAS__->property . 'From = null;', 1, 2);

				$content .= $php->getPHPDoc (array ($field->__COPIX__EXTRAS__->caption, null, '@var ' . $field->__COPIX__EXTRAS__->phpType), 1);
				$content .= $php->getLine ('private $_' . $field->__COPIX__EXTRAS__->property . 'To = null;', 1, 2);

			// recherche simple
			} else {
				$content .= $php->getPHPDoc (array ($field->__COPIX__EXTRAS__->caption, null, '@var ' . $field->__COPIX__EXTRAS__->phpType), 1);
				$content .= $php->getLine ('private $_' . $field->__COPIX__EXTRAS__->property . ' = null;', 1, 2);
			}
		}

		// offset
		$content .= $php->getPHPDoc (array ('Index de début de recherche', null, '@var int'), 1);
		$content .= $php->getLine ('private $_offset = null;', 1, 2);

		// count
		$content .= $php->getPHPDoc (array ('Nombre d\'élements à retourner', null, '@var int'), 1);
		$content .= $php->getLine ('private $_count = null;', 1, 2);

		// orderBy
		$content .= $php->getPHPDoc (array ('Ordres de tri', null, '@var array'), 1);
		$content .= $php->getLine ('private $_orderBy = null;', 1, 2);

		// accesseurs
		foreach ($this->_fieldsSearch as $field) {
			// champs avec une recherche "depuis" et "jusqu'à"
			if ($field->__COPIX__EXTRAS__->type == 'stars' || $field->__COPIX__EXTRAS__->isDate) {
				$content .= $this->_getPHP4Settor ($field, $field->__COPIX__EXTRAS__->property . 'From', $field->__COPIX__EXTRAS__->settor . 'From', true);
				$content .= $this->_getPHP4Gettor ($field, $field->__COPIX__EXTRAS__->property . 'From', $field->__COPIX__EXTRAS__->gettor . 'From');
				$content .= $this->_getPHP4Settor ($field, $field->__COPIX__EXTRAS__->property . 'To', $field->__COPIX__EXTRAS__->settor . 'To', true);
				$content .= $this->_getPHP4Gettor ($field, $field->__COPIX__EXTRAS__->property . 'To', $field->__COPIX__EXTRAS__->gettor . 'To');

			// recherche simple
			} else {
				$content .= $this->_getPHP4Settor ($field, $field->__COPIX__EXTRAS__->property, $field->__COPIX__EXTRAS__->settor, true);
				$content .= $this->_getPHP4Gettor ($field, $field->__COPIX__EXTRAS__->property, $field->__COPIX__EXTRAS__->gettor);
			}
		}

		// offset
		$content .= $php->getPHPDoc (array ('Définit l\'index de début de recherche', null, '@param int $pOffset Index de départ'), 1);
		$content .= $php->getLine ('public function setOffset ($pOffset) {', 1);
		$content .= $php->getLine ('$this->_offset = $pOffset;', 2);
		$content .= $php->getLine ('return $this;', 2);
		$content .= $php->getLine ('}', 1, 2);
		$content .= $php->getPHPDoc (array ('Retourne l\'index de début de recherche', null, '@return int'), 1);
		$content .= $php->getLine ('public function getOffset () {', 1);
		$content .= $php->getLine ('return $this->_offset;', 2);
		$content .= $php->getLine ('}', 1, 2);

		// count
		$content .= $php->getPHPDoc (array ('Définit le nombre ' . $this->_keyWords['d_elements'] . ' à retourner', null, '@param int $pCount Nombre ' . $this->_keyWords['d_elements'] . ' à retourner'), 1);
		$content .= $php->getLine ('public function setCount ($pCount) {', 1);
		$content .= $php->getLine ('$this->_count = $pCount;', 2);
		$content .= $php->getLine ('return $this;', 2);
		$content .= $php->getLine ('}', 1, 2);
		$content .= $php->getPHPDoc (array ('Retourne le nombre ' . $this->_keyWords['d_elements'] . ' à retourner', null, '@return int'), 1);
		$content .= $php->getLine ('public function getCount () {', 1);
		$content .= $php->getLine ('return $this->_count;', 2);
		$content .= $php->getLine ('}', 1, 2);

		// order
		$content .= $php->getPHPDoc (array ('Ajoute un order de tri', null, '@param string $pName Nom du champ', '@param string $pKind Type de tri, ASC ou DESC'), 1);
		$content .= $php->getLine ('public function addOrderBy ($pName, $pKind = \'ASC\') {', 1);
		$content .= $php->getLine ('$this->_orderBy[] = array ($pName, $pKind);', 2);
		$content .= $php->getLine ('return $this;', 2);
		$content .= $php->getLine ('}', 1, 2);
		$content .= $php->getPHPDoc (array ('Supprime les ordres de tri'), 1);
		$content .= $php->getLine ('public function clearOrderBy () {', 1);
		$content .= $php->getLine ('$this->_orderBy = array ();', 2);
		$content .= $php->getLine ('return $this;', 2);
		$content .= $php->getLine ('}', 1, 2);
		$content .= $php->getPHPDoc (array ('Retourne les ordres de tri', null, '@return array'), 1);
		$content .= $php->getLine ('public function getOrderBy () {', 1);
		$content .= $php->getLine ('return $this->_orderBy;', 2);
		$content .= $php->getLine ('}', 1);

		$content .= $php->getLine ('}', 0, 0);
		$this->_write ($this->_getClassesPath (), $this->_searchClassLower . '.class.php', $content);
	}
}