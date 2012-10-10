<?php
/**
 * @package copix
 * @subpackage auth
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Préférences d'un groupe
 * 
 * @package copix
 * @subpackage auth
 */
class CopixGroupPreferences {
	/**
	 * Cache des préférences
	 * 
	 * @var array
	 */
	private static $_cache = array ();

	/**
	 * Retourne les préférences depuis la base de données ou le cache si il existe
	 *
	 * @param string $pIdGroup Identifiant du groupe, null pour le groupe courant
	 * @param string $pGroupHandler Nom du grouphandler, null pour le groupe courant
	 * @return array
	 */
	private static function _getPreferences ($pIdGroup, $pGroupHandler, &$pIdCache) {
		$pIdCache = self::_getIdCache ($pIdGroup, $pGroupHandler);

		// cache interne
		if (array_key_exists ($pIdCache, self::$_cache)) {
			return self::$_cache[$pIdCache];
		}

		// recherche des préférences
		$sp = _daoSP ();

		//_dump (_currentUser ()->getGroups ());

		// groupes
		if ($pIdGroup == null) {
			$grouphandlers = _currentUser ()->getGroups ();
			// groups['module|grouphandler'][ID] = NAME
			if (count ($grouphandlers) > 0) {
				foreach ($grouphandlers as $idGroupHandler => $groups) {
					foreach ($groups as $idGroup => $name) {
						$pIdGroup[] = $idGroup;
					}
				}
			}
		}
		$sp->addCondition ('id_group', '=', $pIdGroup);

		// group handlers
		if ($pGroupHandler == null) {
			$grouphandlers = _currentUser ()->getGroups ();
			// groups['module|grouphandler'][ID] = NAME
			if (count ($grouphandlers) > 0) {
				foreach ($grouphandlers as $idGroupHandler => $groups) {
					$pGroupHandler[] = $idGroupHandler;
				}
			}
		}
		$sp->addCondition ('id_grouphandler', '=', $pGroupHandler);
		
		$results = DAOcopixgrouppreferences::instance ()->findBy ($sp);
		self::$_cache[$pIdCache] = array ();
		foreach ($results as $result) {
			if (!isset (self::$_cache[$pIdCache][$result->id_grouphandler . '~' . $result->id_group])) {
				self::$_cache[$pIdCache][$result->id_grouphandler . '~' . $result->id_group] = array ();
			}
			self::$_cache[$pIdCache][$result->id_grouphandler . '~' . $result->id_group][$result->name_pref] = $result->value_pref;
		}

		return self::$_cache[$pIdCache];
	}

	/**
	 * Retourne l'identifiant de cache
	 *
	 * @param string $pIdGroup Identifiant du groupe, null pour le groupe courant
	 * @param string $pGroupHandler Nom du grouphandler, null pour le groupe courant
	 * @return string
	 */
	private static function _getIdCache ($pIdGroup = null, $pGroupHandler = null) {
            return 'pref-' . self::_getIdGroup ($pIdGroup) . '-' . self::_getGroupHandler ($pGroupHandler);
	}

	/**
	 * Retourne l'identifiant du groupe
	 *
	 * @param string $pIdGroup Identifiant du groupe, null pour le groupe courant
	 * @return string
	 */
	private static function _getIdGroup ($pIdGroup = null) {
		if ($pIdGroup == null) {
			$grouphandlers = _currentUser ()->getGroups ();
			// groups['module|grouphandler'][ID] = NAME
			if (count ($grouphandlers) > 0) {
				foreach ($grouphandlers as $idGroupHandler => $groups) {
					foreach ($groups as $idGroup => $name) {
						return $idGroup;
					}
				}
			}
		} else {
			return $pIdGroup;
		}

		return '----NO----GROUP----';
	}

	/**
	 * Retourne le group handler
	 *
	 * @param string $pGroupHandler Nom du grouphandler, null pour le groupe courant
	 * @return string
	 */
	private static function _getGroupHandler ($pGroupHandler = null) {
		if ($pGroupHandler == null) {
			$grouphandlers = _currentUser ()->getGroups ();
			// groups['module|grouphandler'][ID] = NAME
			if (count ($grouphandlers) > 0) {
				foreach ($grouphandlers as $idGroupHandler => $groups) {
					return $idGroupHandler;
				}
			}
		} else {
			return $pGroupHandler;
		}

		return '----NO----HANDLER----';
	}

	/**
	 * Vide le cache interne
	 *
	 * @param string $pIdGroup Identifiant du groupe, null pour le groupe courant
	 * @param string $pGroupHandler Nom du grouphandler, null pour le groupe courant
	 */
	private static function _clearCache ($pIdGroup = null, $pGroupHandler = null) {
		$idCache = self::_getIdCache ($pIdGroup, $pGroupHandler);
		if (array_key_exists ($idCache, self::$_cache)) {
			unset (self::$_cache[$idCache]);
		}
	}

	/**
	 * Retourne les groupes demandés, null pour tous les groupes de l'utilisateur courant
	 *
	 * @param string $pIdGroup Identifiant du groupe, null pour le groupe courant
	 * @param string $pGroupHandler Nom du grouphandler, null pour le groupe courant
	 * @param string $pName Nom du groupe
	 * @return array
	 */
	private static function _getGroups ($pIdGroup = null, $pGroupHandler = null, $pName = null) {
		$toReturn = array ();

		// tous les utilisateurs connectés, ou un utilisateur spécifique si non connecté
		if ($pIdGroup == null) {
			foreach (_currentUser ()->getGroups () as $keys) {
				foreach ($keys as $idGroupHandler => $groups) {
					foreach ($groups as $id => $group) {
						$toReturn[] = array ($id, $idGroupHandler, $group);
					}
				}
			}
			if (count ($toReturn) == 0) {
				$toReturn[] = array ('----NO----GROUP----', '----NO----HANDLER----', '----NO----GROUP----');
			}
		// uniquement l'utilisateur demandé
		} else {
			$toReturn[] = array ($pIdGroup, $pGroupHandler, $pName);
		}

		return $toReturn;
	}

	/**
	 * Retourne la liste des préférences
	 *
	 * @param string $pModule Si on ne veut que les préférences d'un module, indiquer le nom (possibilité de passer un tableau de nom de module)
	 * @param boolean $pOnlyDefined Indique si on veut que les préférences définies
	 * @param string $pIdGroup Identifiant du groupe, null pour le groupe courant
	 * @param string $pGroupHandler Nom du grouphandler, null pour le groupe courant
	 * @return array
	 */
	public static function getList ($pModule = null, $pOnlyDefined = false, $pIdGroup = null, $pGroupHandler = null) {
		$toReturn = array ();

		// préférences sauvegardées
		$idCache = null;
		$values = self::_getPreferences ($pIdGroup, $pGroupHandler, $idCache);

		$query = 'SELECT DISTINCT(name_pref) FROM copixgrouppreferences ORDER BY name_pref';
		$dbPreferences = _doQuery ($query);
		$preferences = array ();
		foreach ($dbPreferences as $record) {
			foreach ($values as $pref) {
				if (isset ($pref[$record->name_pref])) {
					$preferences[$record->name_pref] = $pref[$record->name_pref];
					break;
				}
				$preferences[$record->name_pref] = null;
			}
		}

		// préférences définies dans les module.xml
		if (is_array ($pModule)) {
			$modules = $pModule;
		} else if ($pModule !== null) {
			$modules = array ($pModule);
		} else {
			$modules = CopixModule::getList ();
		}
		foreach ($modules as $name) {
			$infos = CopixModule::getInformations ($name);
			foreach ($infos->getGroupPreferencesGroups () as $group) {
				$groupPreferences = $group->getList ();
				if (!isset ($toReturn[$group->getId ()])) {
					$toReturn[$group->getId ()] = $group;
					foreach ($group->getList () as $pref) {
						$pref->value = (isset ($preferences[$pref->getName ()])) ? $preferences[$pref->getName ()] : $pref->getDefaultValue ();
					}
				}
				foreach ($group->getList () as $preference) {
					if (!isset ($groupPreferences[$preference->getName ()])) {
						$preference->value = (isset ($preferences[$preference->getName ()])) ? $preferences[$preference->getName ()] : $preference->getDefaultValue ();
						$toReturn[$group->getId ()]->add ($preference);
					}
					if (isset ($preferences[$preference->getName ()])) {
						unset ($preferences[$preference->getName ()]);
					}
				}
			}
		}

		// préférences sauvegardées qui ne sont pas définies dans un module.xml
		if (!$pOnlyDefined && count ($preferences) > 0) {
			if (!isset ($toReturn['default'])) {
				$toReturn['default'] = new CopixModulePreferencesGroup ('default', _i18n ('copix:modules.group.default'));
			}
			foreach ($preferences as $name => $value) {
				if ($pModule === null || in_array (substr ($name, 0, strpos ($name, '|')), $modules)) {
					$toAdd = new CopixModulePreference ($name, $name, null, 'text');
					$toAdd->value = $value;
					$toReturn['default']->add ($toAdd);
				}
			}
			if (count ($toReturn['default']->getList ()) == 0) {
				unset ($toReturn['default']);
			}
		}
		
		return $toReturn;
	}

	/**
	 * Indique si la préférence existe
	 *
	 * @param string $pName Nom de la préférence
	 * @param string $pIdGroup Identifiant du groupe, null pour le groupe courant
	 * @param string $pGroupHandler Nom du grouphandler, null pour le groupe courant
	 * @return boolean
	 */
	public static function exists ($pName, $pIdGroup = null, $pGroupHandler = null) {
		$preferences = self::getList ($pIdGroup, $pGroupHandler);
		foreach ($preferences as $group) {
			if (array_key_exists ($pName, $group->getPreferences ())) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Retourne la valeur de la préférence demandée
	 *
	 * @param string $pName Nom de la préférence
	 * @param string $pDefaultValue Valeur par défaut si la préférence n'existe pas
	 * @param array $pPriorities Priorités des valeurs, si un des groupes a la 1ère valeur, on renvoi celle là, sinon on test la 2ème valeur, etc
	 * @param string $pIdGroup Identifiant du groupe, null pour le groupe courant
	 * @param string $pGroupHandler Nom du grouphandler, null pour le groupe courant
	 * @return string
	 */
	public static function get ($pName, $pDefaultValue = null, $pPriorities = array (), $pIdGroup = null, $pGroupHandler = null) {
		$idCache = null;
		if (!is_array ($pPriorities)) {
			$pPriorities = array ($pPriorities);
		}
		$preferences = self::_getPreferences ($pIdGroup, $pGroupHandler, $idCache);

		$values = array ();
		// recherche dans les préférences sauvegardées en base
		foreach ($preferences as $pref) {
			if (array_key_exists ($pName, $pref)) {
				$values[$pref[$pName]] = true;
			}
		}
		if (count ($values) > 0) {
			if (count ($pPriorities) > 0) {
				foreach ($pPriorities as $priority) {
					if (array_key_exists ($priority, $values)) {
						return $priority;
					}
				}
			}
			$keys = array_keys ($values);
			//_dump ($preferences);
			return array_shift ($keys);
		}

		// tentative de récupération de la valeur par défaut, si la préférence est définie dans le module.xml
		try {
			$infos = CopixModule::getInformations (substr ($pName, 0, strpos ($pName, '|')));
			$groups = $infos->getGroupPreferencesGroups ();
			$toReturn = $pDefaultValue;
			foreach ($groups as $group) {
				$groupPreferences = $group->getList ();
				if (array_key_exists ($pName, $groupPreferences)) {
					$toReturn = ($pDefaultValue == null) ? $groupPreferences[$pName]->getDefaultValue () : $pDefaultValue;
				}
			}
		// le nom du module n'existe pas
		} catch (Exception $e) {
			$toReturn = $pDefaultValue;
		}

		return $toReturn;
	}

	/**
	 * Définit la valeur de la préférence
	 *
	 * @param string $pName Nom de la préférence
	 * @param string $pValue Valeur
	 * @param string $pIdGroup Identifiant du groupe, null pour le(s) groupe(s) courant
	 * @param string $pGroupHandler Nom du grouphandler, null pour le(s) groupe(s) courant
	 * @param string $pName Nom du groupe
	 */
	public static function set ($pName, $pValue, $pIdGroup = null, $pGroupHandler = null) {
		$module = substr ($pName, 0, strpos ($pName, '|'));
		
		if (!in_array ($module, CopixModule::getList ())) {
			throw new CopixUserPreferencesException (_i18n ('copix:copixuserpreferences.error.moduleNotFound', $module));
		}

		$dao = DAOcopixgrouppreferences::instance ();
		$record = new DAORecordCopixGroupPreferences ();
		foreach (self::_getGroups ($pIdGroup, $pGroupHandler, $pName) as $group) {
			// si on n'a pas de login, c'est soit qu'on a passé un user dans $pIdGroup, soit qu'on modifie une préférence d'un user non connecté
			// on va essayer de trouver son login, qu'on peut avoir si il a définit une fois une préférance et qu'on avait son login
			$query = 'SELECT DISTINCT(name_group) FROM copixgrouppreferences WHERE id_group = :id_group AND id_grouphandler = :id_grouphandler AND name_group IS NOT NULL';
			$binds = array (':id_group' => $group[0], ':id_grouphandler' => $group[1]);
			$result = _doQuery ($query, $binds);
			if (count ($result) == 1) {
				$group[2] = $result[0]->name_group;
			}

			$record->id_pref = null;
			$record->id_group = $group[0];
			$record->id_grouphandler = $group[1];
			$record->name_group = $group[2];
			$record->name_pref = $pName;
			$record->value_pref = $pValue;

			// recherche de l'existance de la préférence
			$sp = _daoSP ()->addCondition ('id_group', '=', $group[0])->addCondition ('id_grouphandler', '=', $group[1]);
			$sp->addCondition ('name_pref', '=', $pName);
			$results = $dao->findBy ($sp);

			// elle n'existe pas
			if (count ($results) != 1) {
				$dao->insert ($record);
			// elle existe déja
			} else {
				$record->id_pref = $results[0]->id_pref;
				$dao->update ($record);
			}

			self::_clearCache ($group[0], $group[1]);
		}
	}

	/**
	 * Supprime la préférence demandée
	 *
	 * @param string $pName Nom de la préférence
	 * @param string $pIdGroup Identifiant du groupe, null pour le(s) groupe(s) courant
	 * @param string $pGroupHandler Nom du grouphandler, null pour le(s) groupe(s) courant
	 */
	public static function delete ($pName, $pIdGroup = null, $pGroupHandler = null) {
		$dao = DAOcopixgrouppreferences::instance ();
		foreach (self::_getGroups ($pIdGroup, $pGroupHandler) as $group) {
			$sp = _daoSP ()->addCondition ('id_group', '=', $group[0])->addCondition ('id_grouphandler', '=', $group[1])->addCondition ('name_pref', '=', $pName);
			$results = $dao->deleteBy ($sp);

			self::_clearCache ($group[0], $group[1]);
		}
	}

	/**
	 * Supprime toutes les préférences
	 *
	 * @param string $pIdGroup Identifiant du groupe, null pour le(s) groupe(s) courant
	 * @param string $pGroupHandler Nom du grouphandler, null pour le(s) groupe(s) courant
	 */
	public static function deleteAll ($pIdGroup = null, $pGroupHandler = null) {
		$dao = DAOcopixgrouppreferences::instance ();
		foreach (self::_getGroups ($pIdGroup, $pGroupHandler) as $group) {
			$sp = _daoSP ()->addCondition ('id_group', '=', $group[0])->addCondition ('id_grouphandler', '=', $group[1]);
			$results = $dao->deleteBy ($sp);

			self::_clearCache ($group[0], $group[1]);
		}
	}
}