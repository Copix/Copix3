<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Gestion des actions sur le CMS
 *
 * @package cms
 * @subpackage heading
 */
class HeadingActionsService extends CopixLogAbstractStrategy {
	/**
	 * Actions générales
	 */
	const COPY = 1;
	const ARCHIVE = 2;
	const DELETE = 3;
	const INSERT = 4;
	const UPDATE = 5;
	const MOVE = 6;
	const PUBLISH = 7;
	const VERSION = 8;
	const POSITION_CHANGE = 9;
	const COMMENT_CHANGE = 10;
	const TARGET_CHANGE = 11;
	const THEME_CHANGE = 12;
	const URL_CHANGE = 13;
	const URL_INHERITED = 14;
	const PLANNED = 15;
	const CREDENTIAL_INHERITED = 50;
	const CREDENTIAL_DELETE_GROUP = 51;
	const CREDENTIAL_SAVE = 52;
	const MENU_CHANGE = 61;
	const MENU_NONE = 62;
	const MENU_INHERITED = 63;

	/**
	 * Retourne la liste des actions possibles
	 * ATTENTION url, credential et menu sont "regroupés" pour n'avoir qu'un seul libellé
	 *
	 * @return array
	 */
	public function getTypes () {
		return array (
			self::ARCHIVE => 'Archivage',
			self::POSITION_CHANGE => 'Changement de la position',
			self::COMMENT_CHANGE => 'Changement de la note d\'administration',
			self::TARGET_CHANGE => 'Changement de la cible',
			self::THEME_CHANGE => 'Changement du thème',
			self::URL_CHANGE => 'Changement de l\'adresse',
			self::CREDENTIAL_SAVE => 'Changement des droits',
			self::MENU_CHANGE => 'Changement des menus',
			self::COPY => 'Copie',
			self::INSERT => 'Création d\'un élément',
			self::VERSION => 'Création d\'une version',
			self::MOVE => 'Déplacement',
			self::UPDATE => 'Mise à jour',
			self::PUBLISH => 'Publication',
			self::DELETE => 'Suppression',
			self::PLANNED => 'Planification'
		);
	}

	/**
	 * Notifier une action via un élément
	 *
	 * @param int $pType Type de l'action
	 * @param stdClass $pElement Elément
	 * @param array $pExtras Informations supplémentaires
	 */
	public function notify ($pType, $pElement, $pExtras = array ()) {
		$hei = _ioClass ('HeadingElementInformationServices');
		$params = array (
			'message' => $this->getMessage ($pType, $pElement, $pExtras),
			'type' => $pType,
			'element' => $pElement
		);
		if ($pElement != null)  {
			$params['public_id_hei'] = $pElement->public_id_hei;
		}
		_notify ('cms_action', array_merge ($params, $pExtras));
	}

	/**
	 * Notifier une action via un identifiant interne
	 *
	 * @param int $pType Type de l'action
	 * @param int $pIdHelt Identifiant interne
	 * @param array $pExtras Informations supplémentaires
	 */
	public function notifyById ($pType, $pIdHelt, $pTypeHei, $pExtras = array ()) {
		self::notify ($pType, _ioClass ('HeadingElementInformationServices')->getById ($pIdHelt, $pTypeHei), $pExtras);
	}

	/**
	 * Notifier une action via un identifiant publique
	 *
	 * @param int $pType Type de l'action
	 * @param int $pPublicId Identifiant publique
	 * @param array $pExtras Informations supplémentaires
	 */
	public function notifyByPublicId ($pType, $pPublicId, $pExtras = array ()) {
		self::notify ($pType, _ioClass ('HeadingElementInformationServices')->get ($pPublicId), $pExtras);
	}

	/**
	 * Retourne le message de l'action
	 *
	 * @param int $pType Type de l'action, utiliser HeadingActionsService::XXX
	 * @param stdClass $pElement Objet sur lequel on a fait une action
	 * @param array $pExtras Informations supplémentaires
	 */
	public function getMessage ($pType, $pElement, $pExtras = array ()) {
		$hei = _ioClass ('HeadingElementInformationServices');
		switch ($pType) {
			case self::COPY :
			case self::MOVE :
				$libelle = ($pType == self::COPY) ? 'Copie ' : 'Déplacement ';
				$toReturn = $libelle . 'de l\'élément.';
				break;

			case self::ARCHIVE : $toReturn = 'Archivage de l\'élément.'; break;

			case self::DELETE : $toReturn = 'Suppression de l\'élément.'; break;

			case self::CREDENTIAL_INHERITED : $toReturn = 'Modification des droits : droits de la rubrique parente.'; break;

			case self::CREDENTIAL_SAVE :
				$groups = CopixGroupHandlerFactory::getAllGroupList ();
				$group = (isset ($groups[$pExtras['group_handler']][$pExtras['group_id']])) ? $groups[$pExtras['group_handler']][$pExtras['group_id']] : 'INCONNU';
				$caption = _ioClass ('HeadingElementCredentials')->getCaption ($pExtras['credential']);
				$toReturn = 'Modification des droits du groupe ' . $group . ' : ' . $caption . '.';
				break;

			case self::CREDENTIAL_DELETE_GROUP : $toReturn = 'Suppression des droits du groupe "' . $pExtras['group_id'] . '".'; break;

			case self::INSERT : $toReturn = 'Création de l\'élément.'; break;

			case self::UPDATE :
				$status = ($pElement->status_hei == HeadingElementStatus::PUBLISHED) ? 'de la version publiée' : 'du brouillon';
				$toReturn = 'Modification ' . $status . '.';
				break;

			case self::POSITION_CHANGE : $toReturn = 'Changement de position.'; break;

			case self::PUBLISH : $toReturn = 'Publication du brouillon.'; break;
			
			case self::PLANNED : $toReturn = 'Planification du brouillon.'; break;

			case self::VERSION : $toReturn = 'Création d\'un brouillon.'; break;

			case self::COMMENT_CHANGE :
				$note = (strlen ($pElement->comment_hei) > 20) ? utf8_encode (substr (utf8_decode ($pElement->comment_hei), 0, 20)) . ' (...)' : $pElement->comment_hei;
				$toReturn = 'Modification de la note d\'administration : ' . $note . '.';
				break;

			case self::TARGET_CHANGE : $toReturn = 'Modification de la cible : ' . $pExtras['target_new'] . '.'; break;

			case self::THEME_CHANGE :
				$caption = ($pExtras['theme_new'] == null) ? 'thème de la rubrique parente' : $pExtras['theme_new'];
				$toReturn = 'Modification du thème : ' . $caption . '.';
				break;

			case self::URL_CHANGE : $toReturn = 'Modification de l\'adresse : ' . $pExtras['base_url_hei_new'] . $pExtras['url_id_hei_new'] . '.'; break;

			case self::URL_INHERITED : $toReturn = 'Modification de l\'adresse : définie par la rubrique parente.'; break;

			case self::MENU_CHANGE :
				$toReturn = 'Modification du menu ' . _ioClass ('HeadingElementMenuServices')->getCaption ($pElement->type_hem) . ' : il pointe sur ' . $hei->get ($pElement->public_id_hem)->caption_hei;
				break;

			case self::MENU_NONE : $toReturn = 'Pas de menu pour ' . _ioClass ('HeadingElementMenuServices')->getCaption ($pElement->type_hem) . '.'; break;

			case self::MENU_INHERITED : $toReturn = 'Modification du menu ' . _ioClass ('HeadingElementMenuServices')->getCaption ($pElement->type_hem) . ' : définit par la rubrique parente.'; break;

			default :
				$toReturn = 'Action inconnue.';
				break;
		}
		return $toReturn;
	}

	/**
	 * Lie un profil de log à une action loguée
	 *
	 * @param string $pProfile Nom du profil
	 * @param int $pAction Identifiant de l'action
	 */
	public function _linkProfileToAction ($pProfile, $pAction) {
		$record = DAORecordcms_actions_profiles::create ();
		$record->id_profile = $pProfile;
		$record->id_action = $pAction;
		DAOcms_actions_profiles::instance ()->insert ($record);
	}

	/**
	 * Effectue un log
	 *
	 * @param string $pProfile Nom du profil
	 * @param string $pType Type de log
	 * @param int $pLevel Niveau de log, utiliser les constantes de CopixLog
	 * @param string $pDate Date et heure du log, format YmdHis
	 * @param string $pMessage Message à loger
	 * @param array $pExtras Informations supplémentaires
	 */
	public function log ($pProfile, $pType, $pLevel, $pDate, $pMessage, $pExtras) {
		if ($pType != 'cms_action') {
			throw new CopixLogException ('Seul le type de log "cms_action" peut être logué via la stratégie HeadingActionsService.');
		}

		// identifiants des logs déja effectués, pour ne pas dupliquer les informations dans cms_actions
		static $_logs = array ();

		$daoProfiles = DAOcms_actions_profiles::instance ();

		// si l'action existe déja, on n'ajoute qu'une ligne dans cms_actions_profiles
		if (array_key_exists ($pExtras['id'], $_logs)) {
			$this->_linkProfileToAction ($pProfile, $_logs[$pExtras['id']]);
			return null;
		}

		// sauvegarde des informations générales
		$record = DAORecordcms_actions::create ();
		$record->url_action = $pExtras['request_uri'];
		$record->referer_action = $pExtras['referer'];
		$record->date_action = $pDate;
		$record->page_id_action = $pExtras['page_id'];
		$record->type_action = $pExtras['type'];
		$record->element_action = var_export ($pExtras['element'], true);
		$record->public_id_hei = $pExtras['public_id_hei'];
		$record->hierarchy_action = $pExtras['element']->hierarchy_hei;
		$record->level_action = $pLevel;
		$record->version_action = $pExtras['element']->version_hei;
		DAOcms_actions::instance ()->insert ($record);
		$_logs[$pExtras['id']] = $record->id_action;
		$this->_linkProfileToAction ($pProfile, $_logs[$pExtras['id']]);

		// sauvegarde des extras
		$unwanted = array ('type', 'message', 'element', 'public_id_hei', 'class_name', 'file', 'function_name', 'host', 'line', 'file', 'page_id', 'referer', 'request_uri', 'user', 'user_agent');
		foreach ($unwanted as $word) {
			if (isset ($pExtras[$word])) {
				unset ($pExtras[$word]);
			}
		}
		$values = array ();
		$binds = array ();
		foreach ($pExtras as $name => $value) {
			$id = uniqid ();
			$values[] = '(:action' . $id . ', :extra' . $id . ', :value' . $id . ')';
			$binds[':action' . $id] = $record->id_action;
			$binds[':extra' . $id] = $name;
			$binds[':value' . $id] = $value;
		}
		if (count ($values) > 0) {
			$query = 'INSERT INTO cms_actions_extras (id_action, id_extra, value_extra) VALUES ' . implode (', ', $values);
			_doQuery ($query, $binds);
		}

		// sauvegarde des utilisateurs
		$identities = _currentUser ()->getIdentities ();
		if (count ($identities) > 0) {
			$binds = array ();
			$values = array ();
			foreach (_currentUser ()->getIdentities () as $identity) {
				$id = uniqid ();
				$values[] = '(:action' . $id . ', :userhandler' . $id . ', :user' . $id . ', :login' . $id . ')';
				$binds[':action' . $id] = $record->id_action;
				$binds[':userhandler' . $id] = $identity[0];
				$binds[':user' . $id] = $identity[1];
				$binds[':login' . $id] = _currentUser ()->getHandlerResponse ($identity[0])->getLogin ();
			}
			$query = 'INSERT INTO cms_actions_users (id_action, userhandler_user, id_user, login_user) VALUES ' . implode (', ', $values);
			_doQuery ($query, $binds);
		}
	}

	/**
	 * Retourne les données pour les identifiants d'action donnés
	 *
	 * @param string $pProfile Nom du profil
	 * @param array $pAction Identifiant de l'action
	 * @param string $pClass Nom de la classe à utiliser
	 * @return CopixLogData[]
	 */
	private function _getData ($pProfile, $pAction, $pClass = 'CopixLogData') {
		$hei = _ioClass ('HeadingElementInformationServices');
		$toReturn = array ();
		$action = DAOcms_actions::instance ()->get ($pAction);

		$extras = array ();
		$extras['element'] = eval ('return ' . preg_replace ('/[a-z_]+::__set_state/', 'new CopixPPO ', strtolower ($action->element_action)) . ';');
		$extras['request_uri'] = $action->url_action;
		$extras['referer'] = $action->referer_action;
		$extras['page_id'] = $action->page_id_action;
		$extras['type'] = 'cms_action';
		$extras['action_type'] = $action->type_action;
		$extras['level'] = $action->level_action;
		$extras['version'] = $action->version_action;
		$extras['public_id_hei'] = $action->public_id_hei;
		try {
			$extras['hierarchy'] = implode (' > ', $hei->getHeadingPathCaption ($action->public_id_hei));
		} catch (Exception $e) {
			$extras['hierarchy'] = $action->hierarchy_action;
		}

		// recherche des extras
		foreach (DAOcms_actions_extras::instance ()->findBy (_daoSP ()->addCondition ('id_action', '=', $action->id_action)) as $extra) {
			$extras[$extra->id_extra] = $extra->value_extra;
		}

		// recherche des utilisateurs
		$extras['user'] = array ();
		foreach (DAOcms_actions_users::instance ()->findBy (_daoSP ()->addCondition ('id_action', '=', $action->id_action)) as $user) {
			$extras['user'][] = array ($user->userhandler_user, $user->id_user, $user->login_user);
		}

		$message = $this->getMessage ($action->type_action, $extras['element'], $extras);
		return new $pClass ($pProfile, 'cms_action', $action->level_action, $action->date_action, $message, $extras);
	}

	/**
	 * Retourne les éléments qui correspondent aux paramètres de recherche indiqués
	 *
	 * @param string $pProfile Nom du profil
	 * @param int $pStart Index du premier élément à retourner
	 * @param int $pCount Nombre d'éléments à retourner, null pour tous
	 * @return string[]
	 */
	public function get ($pProfile, $pStart = 0, $pCount = 20) {
		$sp = _daoSP ()->addCondition ('id_profile', '=', $pProfile)->orderBy (array ('id_action', 'DESC'));
		if ($pStart > 0) {
			$sp->setOffset ($pStart);
		}
		if ($pCount != null) {
			$sp->setCount ($pCount);
		}
		$toReturn = array ();
		foreach (DAOcms_actions_profiles::instance ()->findBy ($sp) as $record) {
			$toReturn[] = $this->_getData ($pProfile, $record->id_action);
		}
		return $toReturn;
	}

	/**
	 * Retourne les éléments qui correspondent à la recherche donnée
	 *
	 * @param string $pProfile Identifiant du profile
	 * @param CopixPPO $pSearch Paramètres de recherche
	 * @param int $pStart Index du premier élément à retourner
	 * @param int $pCount Nombre d'éléments à retourner
	 */
	public function search ($pProfile, $pSearch, $pStart = 0, $pCount = 20) {
		$pages = $this->_search ($pProfile, $pSearch, $pStart, $pCount);
		
		$prevPageId = null;
		$prevPublicId = null;
		$sp = _daoSP ()->addCondition ('page_id_action', '=', $pages)->orderBy (array ('id_action', 'DESC'));
		$toReturn = array ();
		foreach (DAOcms_actions::instance ()->findBy ($sp) as $record) {
			if ($prevPageId != $record->page_id_action || $prevPublicId != $record->public_id_hei) {
				$toEdit = $toReturn[] = $this->_getData ($pProfile, $record->id_action, 'HeadingActionData');
				$toEdit->setPageId ($record->page_id_action);
			} else {
				$toEdit->addAction ($this->_getData ($pProfile, $record->id_action));
			}
			$prevPageId = $record->page_id_action;
			$prevPublicId = $record->public_id_hei;
		}

		return $toReturn;
	}

	/**
	 * Retourne la date au format Copix
	 *
	 * @param string $pDate Date dans divers formats
	 * @return string
	 */
	private function _getDate ($pDate) {
		if (strpos ($pDate, '/') !== false) {
			list ($day, $month, $year) = explode ('/', $pDate);
			return $year . $month . $day;
		} else {
			return date ('Ymd', $pDate);
		}
	}

	/**
	 * Retourne les identifiants de page correspondants à la recherche donnée
	 *
	 * @param string $pProfileIdentifiant du profil de log
	 * @param CopixPPO $pSearch Paramètres de recherche
	 * @param int $pStart Index du premier élément à retourner
	 * @param int $pCount Nombre d'éléments à retourner
	 * @param boolean $pReturnCount Indique si on veut compter les résultats ou les renvoyer
	 * @return mixed
	 */
	private function _search ($pProfile, $pSearch, $pStart, $pCount, $pReturnCount = false) {
		// pour regrouper des actions au sein d'un groupe, on utilise l'identifiant de la page sur laquelle ont été effectuées les modifications
		// on cherche donc tous les identifiants de page correspondant à la recherche demandée, pour ensuite retrouver toutes les actions effectuées sur cette page
		// les actions précises que l'on veut seront contenues dans ce bloc d'action
		// ça permet d'avoir un historique de toutes les actions effectuées dans la page au lieu de ne retourner que l'action recherchée
		$haveSearch = false;
		$toReturn = array ();
		$query = ($pReturnCount) ? 'SELECT COUNT(DISTINCT page_id_action) countPages' : 'SELECT DISTINCT page_id_action';
		$from = array ('cms_actions a' => true);
		$where = array ();
		$binds = array ();

		// public_id_hei
		if ($pSearch->public_id_hei != null) {
			$haveSearch = true;
			$from['cms_actions_profiles p'] = true;
			$where['p.id_profile = :profile'] = true;
			$where['a.id_action = p.id_action'] = true;
			$where['a.public_id_hei = :public_id_hei'] = true;
			$binds = array_merge ($binds, array (':profile' => $pProfile, ':public_id_hei' => $pSearch->public_id_hei));
		}

		// hierarchy_hei
		if ($pSearch->hierarchy_hei != null && $pSearch->hierarchy_hei > 0) {
			$haveSearch = true;
			$where['(a.hierarchy_action LIKE :hierarchy_hei OR a.hierarchy_action LIKE :hierarchy_hei2)'] = true;
			$binds = array_merge ($binds, array (':hierarchy_hei' => '%-' . $pSearch->hierarchy_hei . '-%', ':hierarchy_hei2' => '%-' . $pSearch->hierarchy_hei));
		}

		// users
		if (is_array ($pSearch->users) && count ($pSearch->users) > 0) {
			$haveSearch = true;
			$from['cms_actions_users u'] = true;
			$where['u.id_action = a.id_action'] = true;
			$or = array ();
			foreach ($pSearch->users as $index => $user) {
				$or[] = 'u.login_user = :user' . $index;
				$binds = array_merge ($binds, array (':user' . $index => $user));
			}
			$where['(' . implode (' OR ', $or) . ')'] = true;
		}

		// types d'actions
		if (is_array ($pSearch->types) && count ($pSearch->types) > 0) {
			$haveSearch = true;
			foreach ($pSearch->types as $type) {
				if ($type == self::MENU_CHANGE) {
					$where['(a.type_action = :type1 OR a.type_action = :type2 OR a.type_action = :type3)'] = true;
					$binds = array_merge ($binds, array (':type1' => self::MENU_CHANGE, ':type2' => self::MENU_INHERITED, ':type3' => self::MENU_NONE));
				} else if ($type == self::CREDENTIAL_SAVE) {
					$where['(a.type_action = :type1 OR a.type_action = :type2 OR a.type_action = :type3)'] = true;
					$binds = array_merge ($binds, array (':type1' => self::CREDENTIAL_SAVE, ':type2' => self::CREDENTIAL_INHERITED, ':type3' => self::CREDENTIAL_DELETE_GROUP));
				} else if ($type == self::URL_CHANGE) {
					$where['(a.type_action = :type1 OR a.type_action = :type2)'] = true;
					$binds = array_merge ($binds, array (':type1' => self::URL_CHANGE, ':type2' => self::URL_INHERITED));
				} else {
					$where['a.type_action = :type'] = true;
					$binds = array_merge ($binds, array (':type' => $type));
				}
			}
		}

		// date
		if ($pSearch->date_from != null) {
			$haveSearch = true;
			$where['date_action >= :date_from'] = true;
			$binds = array_merge ($binds, array (':date_from' => $this->_getDate ($pSearch->date_from)));
		}
		if ($pSearch->date_to != null) {
			$haveSearch = true;
			$where['date_action <= :date_to'] = true;
			$binds = array_merge ($binds, array (':date_to' => $this->_getDate ($pSearch->date_to)));
		}

		if ($haveSearch) {
			$query .= ' FROM ' . implode (', ', array_keys ($from)) . ' WHERE ' . implode (' AND ', array_keys ($where)) . ' ORDER BY a.id_action DESC';
			$pages = CopixDB::getConnection ()->doQuery ($query, $binds, $pStart, $pCount);
		} else {
			$query .= ' FROM cms_actions ORDER BY id_action DESC';
			$pages =  CopixDB::getConnection ()->doQuery ($query, array (), $pStart, $pCount);
		}
		
		if ($pReturnCount) {
			return $pages[0]->countPages;
		} else {
			$toReturn = array ();
			foreach ($pages as $record) {
				$toReturn[] = $record->page_id_action;
			}
			return $toReturn;
		}
	}

	/**
	 * Compte le nombre de résultats d'une recherche
	 *
	 * @param string $pProfile Profil de log
	 * @param CopixPPO $pSearch Paramètres de recherche
	 * @return int
	 */
	public function countBy ($pProfile, $pSearch) {
		return $this->_search ($pProfile, $pSearch, 0, null, true);
	}

	/**
	 * Retourne les auteurs qui ont fait au moins une action dans le CMS
	 *
	 * @param string $pProfile Identifiant du profil de log
	 * @return array
	 */
	public function getUsers ($pProfile) {
		$query = 'SELECT DISTINCT login_user FROM cms_actions_users ORDER BY login_user';
		$toReturn = array ();
		foreach (_doQuery ($query) as $record) {
			$toReturn[$record->login_user] = $record->login_user;
		}
		return $toReturn;
	}

	/**
	 * Supprime le contenu du log pour le profil demandé
	 *
	 * @param string $pProfile Nom du profil
	 */
	public function delete ($pProfile) {
		// recherche des actions qui ne sont liées qu'au profil à supprimer
		$query = '
			SELECT id_action FROM cms_actions_profiles WHERE id_action IN (
				SELECT id_action FROM cms_actions_profiles GROUP BY id_action HAVING COUNT(*) = 1
			) AND id_profile = :profile
		';
		$binds = array (':profile' => $pProfile);
		$actions = array ();
		$results = _doQuery ($query, $binds);
		if ($results > 0) {
			foreach ($results as $record) {
				$actions[] = $record->id_action;
			}
			$sp = _daoSP ()->addCondition ('id_action', '=', $actions);
			// suppression des extras
			DAOcms_actions_extras::instance ()->deleteBy ($sp);
			// suppression ds utilisateurs
			DAOcms_actions_users::instance ()->deleteBy ($sp);
			// suppression des actions
			DAOcms_actions::instance ()->deleteBy ($sp);
		}

		// suppression du profil
		DAOcms_actions_profiles::instance ()->deleteBy (_daoSP ()->addCondition ('id_profile', '=', $pProfile));
	}

	/**
	 * Retourne le nombre d'éléments
	 *
	 * @param string $pProfile Nom du profil
	 * @return int
	 */
	public function count ($pProfile) {
		return DAOcms_actions_profiles::instance ()->countBy (_daoSP ()->addCondition ('id_profile', '=', $pProfile));
	}
}