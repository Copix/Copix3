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
 * Préférences de l'utilisateur courant
 * 
 * @package copix
 * @subpackage auth
 */
class CopixUserPreferences {
	/**
	 * Cache des préférences
	 * 
	 * @var array
	 */
	private static $_cache = array ();

	/**
	 * Retourne les préférences depuis la base de données ou le cache si il existe
	 *
	 * @param string $pIdUser Identifiant de l'utilisateur, null pour l'utilisateur courant
	 * @param string $pUserHandler Nom du userhandler, null pour l'utilisateur courant
	 * @return array
	 */
	private static function _getPreferences ($pIdUser, $pUserHandler, &$pIdCache) {
		$pIdCache = self::_getIdCache ($pIdUser, $pUserHandler);

		// cache interne
		if (array_key_exists ($pIdCache, self::$_cache)) {
			return self::$_cache[$pIdCache];
		}

		// recherche des préférences
		$sp = _daoSP ()->addCondition ('id_user', '=', self::_getIdUser ($pIdUser))->addCondition ('id_userhandler', '=', self::_getUserHandler ($pUserHandler));
		$results = DAOcopixuserpreferences::instance ()->findBy ($sp);
		self::$_cache[$pIdCache] = array ();
		foreach ($results as $result) {
			self::$_cache[$pIdCache][$result->name_pref] = $result->value_pref;
		}

		return self::$_cache[$pIdCache];
	}

	/**
	 * Retourne l'identifiant de cache
	 *
	 * @param string $pIdUser Identifiant de l'utilisateur, null pour l'utilisateur courant
	 * @param string $pUserHandler Nom du userhandler, null pour l'utilisateur courant
	 * @return string
	 */
	private static function _getIdCache ($pIdUser = null, $pUserHandler = null) {
            return 'pref-' . self::_getIdUser ($pIdUser) . '-' . self::_getUserHandler ($pUserHandler);
	}

	/**
	 * Retourne l'identifiant de l'utilisateur
	 *
	 * @param string $pIdUser Identifiant de l'utilisateur, null pour l'utilisateur courant
	 * @return string
	 */
	private static function _getIdUser ($pIdUser = null) {
		return ($pIdUser == null) ? (($userId = _currentUser ()->getId ()) == null ? '----NO----USER----' : $userId) : $pIdUser;
	}

	/**
	 * Retourne le user handler
	 *
	 * @param string $pUserHandler Nom du userhandler, null pour l'utilisateur courant
	 * @return string
	 */
	private static function _getUserHandler ($pUserHandler = null) {
		return ($pUserHandler == null) ? (($userHandler = _currentUser ()->getHandler ()) == null ? '----NO----HANDLER----' : $userHandler) : $pUserHandler;
	}

	/**
	 * Vide le cache interne
	 *
	 * @param string $pIdUser Identifiant de l'utilisateur, null pour l'utilisateur courant
	 * @param string $pUserHandler Nom du userhandler, null pour l'utilisateur courant
	 */
	private static function _clearCache ($pIdUser = null, $pUserHandler = null) {
		$idCache = self::_getIdCache ($pIdUser, $pUserHandler);
		if (array_key_exists ($idCache, self::$_cache)) {
			unset (self::$_cache[$idCache]);
		}
	}

	/**
	 * Retourne l'utilisateur courant si $pIdUser est null, sinon, tous les utilisateurs connectés
	 *
	 * @param string $pIdUser Identifiant de l'utilisateur, null pour l'utilisateur courant
	 * @param string $pUserHandler Nom du userhandler, null pour l'utilisateur courant
	 * @param string $pLogin login de l'utilisateur
	 * @return array
	 */
	private static function _getUsers ($pIdUser = null, $pUserHandler = null, $pLogin = null) {
		$toReturn = array ();

		// tous les utilisateurs connectés, ou un utilisateur spécifique si non connecté
		if ($pIdUser == null) {
			foreach (_currentUser ()->getResponses () as $response) {
				if ($response->getResult ()) {
					$toReturn[] = array ($response->getId (), $response->getHandler (), $response->getLogin ());
				}
			}
			if (count ($toReturn) == 0) {
				$toReturn[] = array ('----NO----USER----', '----NO----HANDLER----', '----NO----USER----');
			}
		// uniquement l'utilisateur demandé
		} else {
			$toReturn[] = array ($pIdUser, $pUserHandler, $pLogin);
		}

		return $toReturn;
	}

	/**
	 * Retourne la liste des préférences
	 *
	 * @param string $pModule Si on ne veut que les préférences d'un module, indiquer le nom (possibilité de passer un tableau de nom de module)
	 * @param string $pIdUser Identifiant de l'utilisateur, null pour l'utilisateur courant
	 * @param string $pUserHandler Nom du userhandler, null pour l'utilisateur courant
	 * @return array
	 */
	public static function getList ($pModule = null, $pOnlyDefined = false, $pIdUser = null, $pUserHandler = null) {
		$toReturn = array ();

		// préférences sauvegardées
		$idCache = null;
		$values = self::_getPreferences ($pIdUser, $pUserHandler, $idCache);

		$query = 'SELECT DISTINCT(name_pref) FROM copixuserpreferences ORDER BY name_pref';
		$dbPreferences = _doQuery ($query);
		$preferences = array ();
		foreach ($dbPreferences as $record) {
			$preferences[$record->name_pref] = (isset ($values[$record->name_pref])) ? $values[$record->name_pref] : null;
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
			foreach ($infos->getUserPreferencesGroups () as $group) {
				$groupPreferences = $group->getList ();
				if (!isset ($toReturn[$group->getId ()])) {
					$toReturn[$group->getId ()] = $group;
					foreach ($group->getList () as $pref) {
						$pref->value = (isset ($preferences[$pref->getName ()])) ? $preferences[$pref->getName ()] : $pref->getDefaultValue ();
					}
				}
				foreach ($group->getList () as $preference) {
					$preference->value = (isset ($preferences[$preference->getName ()])) ? $preferences[$preference->getName ()] : $preference->getDefaultValue ();
					$toReturn[$group->getId ()]->add ($preference);
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
	 * @param string $pIdUser Identifiant de l'utilisateur, null pour l'utilisateur courant
	 * @param string $pUserHandler Nom du userhandler, null pour l'utilisateur courant
	 * @return boolean
	 */
	public static function exists ($pName, $pIdUser = null, $pUserHandler = null) {
		$preferences = self::getList ($pIdUser, $pUserHandler);
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
	 * @param string $pIdUser Identifiant de l'utilisateur, null pour l'utilisateur courant
	 * @param string $pUserHandler Nom du userhandler, null pour l'utilisateur courant
	 * @return string
	 */
	public static function get ($pName, $pDefaultValue = null, $pIdUser = null, $pUserHandler = null) {
		$idCache = null;
		$preferences = self::_getPreferences ($pIdUser, $pUserHandler, $idCache);

		// la préférence a été sauvegardée en base
		if (array_key_exists ($pName, $preferences)) {
			return self::$_cache[$idCache][$pName];

		// la préférence n'a pas été sauvegardée en base
		} else {
			// tentative de récupération de la valeur par défaut, si la préférence est définie dans le module.xml
			try {
				$infos = CopixModule::getInformations (substr ($pName, 0, strpos ($pName, '|')));
				$groups = $infos->getUserPreferencesGroups ();
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

			// on stock uniquement dans le cache interne, et pas en base, pour prendre en compte un changement de la valeur par défaut dans le module.xml
			return self::$_cache[$idCache][$pName] = $toReturn;
		}
	}

	/**
	 * Définit la valeur de la préférence
	 *
	 * @param string $pName Nom de la préférence
	 * @param string $pValue Valeur
	 * @param string $pIdUser Identifiant de l'utilisateur, null pour tous les utilisateurs connectés
	 * @param string $pUserHandler Nom du userhandler, null pour tous les utilisateurs connectés
	 * @param string $pLogin Login de l'utilisateur
	 */
	public static function set ($pName, $pValue, $pIdUser = null, $pUserHandler = null, $pLogin = null) {
		$module = substr ($pName, 0, strpos ($pName, '|'));
		if (!in_array ($module, CopixModule::getList ())) {
			throw new CopixUserPreferencesException (_i18n ('copix:copixuserpreferences.error.moduleNotFound', $module));
		}

		$dao = DAOcopixuserpreferences::instance ();
		$record = new DAORecordCopixUserPreferences ();
		foreach (self::_getUsers ($pIdUser, $pUserHandler, $pLogin) as $user) {
			// si on n'a pas de login, c'est soit qu'on a passé un user dans $pIdUser, soit qu'on modifie une préférence d'un user non connecté
			// on va essayer de trouver son login, qu'on peut avoir si il a définit une fois une préférance et qu'on avait son login
			$query = 'SELECT DISTINCT(login_user) FROM copixuserpreferences WHERE id_user = :id_user AND id_userhandler = :id_userhandler AND login_user IS NOT NULL';
			$binds = array (':id_user' => $user[0], ':id_userhandler' => $user[1]);
			$result = _doQuery ($query, $binds);
			if (count ($result) == 1) {
				$user[2] = $result[0]->login_user;
			}

			$record->id_pref = null;
			$record->id_user = $user[0];
			$record->id_userhandler = $user[1];
			$record->login_user = $user[2];
			$record->name_pref = $pName;
			$record->value_pref = $pValue;

			// recherche de l'existance de la préférence
			$sp = _daoSP ()->addCondition ('id_user', '=', $user[0])->addCondition ('id_userhandler', '=', $user[1]);
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

			self::_clearCache ($user[0], $user[1]);
		}
	}

	/**
	 * Supprime la préférence demandée
	 *
	 * @param string $pName Nom de la préférence
	 * @param string $pIdUser Identifiant de l'utilisateur, null pour tous les utilisateurs connectés
	 * @param string $pUserHandler Nom du userhandler, null pour tous les utilisateurs connectés
	 */
	public static function delete ($pName, $pIdUser = null, $pUserHandler = null) {
		$dao = DAOcopixuserpreferences::instance ();
		foreach (self::_getUsers ($pIdUser, $pUserHandler) as $user) {
			$sp = _daoSP ()->addCondition ('id_user', '=', $user[0])->addCondition ('id_userhandler', '=', $user[1]);
			$sp->addCondition ('name_pref', '=', $pName);
			$results = $dao->deleteBy ($sp);

			self::_clearCache ($user[0], $user[1]);
		}
	}

	/**
	 * Supprime toutes les préférences
	 *
	 * @param string $pIdUser Identifiant de l'utilisateur, null pour tous les utilisateurs connectés
	 * @param string $pUserHandler Nom du userhandler, null pour tous les utilisateurs connectés
	 */
	public static function deleteAll ($pIdUser = null, $pUserHandler = null) {
		$dao = DAOcopixuserpreferences::instance ();
		foreach (self::_getUsers ($pIdUser, $pUserHandler) as $user) {
			$sp = _daoSP ()->addCondition ('id_user', '=', $user[0])->addCondition ('id_userhandler', '=', $user[1]);
			$results = $dao->deleteBy ($sp);

			self::_clearCache ($user[0], $user[1]);
		}
	}

	/**
	 * Retourne les utilisateurs pouvant avoir une configuration
	 *
	 * @param boolean $pOnlyRegistered Indique si on ne veut que les utilisateurs connectés, ou les connectés + ceux qui ont déja une config
	 * @return array
	 */
	public static function getUsers ($pOnlyRegistered = true) {
		$users = array ();
		$usersSort = array ();
		$toReturn = array ();
		// si on est admin on prend tous les utilisateurs ayant des préférences définies
		if ($pOnlyRegistered === false) {
			$query = 'SELECT id_userhandler, id_user, login_user FROM copixuserpreferences GROUP BY id_userhandler, id_user';
			$results = _doQuery ($query);
			foreach ($results as $record) {
				$key = $record->login_user . '|' . $record->id_user . '|' . $record->id_userhandler;
				$users[$key] = array ('user' => $record->id_user, 'userhandler' => $record->id_userhandler, 'login' => $record->login_user);
				$usersSort[$record->login_user] = true;
			}
		}
		foreach (_currentUser ()->getResponses () as $response) {
			if ($response->getResult ()) {
				$key = $response->getLogin () . '|' . $response->getId () . '|' . $response->getHandler ();
				$users[$key] = array ('user' => $response->getId (), 'userhandler' => $response->getHandler (), 'login' => $response->getLogin ());
				$usersSort[$response->getLogin ()] = true;
			}
		}
		
		ksort ($usersSort);
		foreach ($usersSort as $login => $useless) {
			foreach ($users as $user) {
				if ($user['login'] == $login) {
					$toReturn[] = $user;
				}
			}
		}

		return $toReturn;
	}
}