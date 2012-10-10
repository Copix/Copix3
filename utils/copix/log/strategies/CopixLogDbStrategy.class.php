<?php
/**
 * @package copix
 * @subpackage log
 * @author Landry Benguigui, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Log en base de données
 *
 * @package copix
 * @subpackage log
 */
class CopixLogDbStrategy extends CopixLogAbstractStrategy {
	/**
	 * Objet DAO de la table principale
	 * 
	 * @var DAOCopixLog[]
	 */
	private $_dao = array ();

	/**
	 * Objet DAO de la table des extras
	 *
	 * @var DAOCopixLogExtras[]
	 */
	private $_daoExtras = array ();
	
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
		// informations générales
		$log = new DAORecordCopixLog ();
		$log->type_log = $pType;
		$log->date_log = $pDate;
		$log->profile_log = $pProfile;
		$log->level_log = $pLevel;
		$timer = CopixTimer::get ('dbstrategy' . uniqid ());
		$log->message_log = $this->_getStringValue ($pMessage);
		$this->_getDAO ($pProfile)->insert ($log);

		// informations supplémentaires
		// patch spécial mysql, pour faire un seul insert étendu et gagner beaucoup de temps
		$profile = CopixConfig::instance ()->copixdb_getProfile (CopixConfig::instance ()->copixdb_getDefaultProfileName ());
		// on ne sauvegarde pas l'identifiant du log
		unset ($pExtras['id']);
		if ($profile->getDriverName () == 'mysql' || $profile->getDriverName () == 'pdo_mysql') {
			$query = 'INSERT INTO copixlogextras (id_log, key_extra, value_extra) VALUES ';
			$values = array ();
			$binds = array ();
			foreach ($pExtras as $key => $value) {
				$id = uniqid ('_');
				$values[] = '(:id' . $id . ', :key' . $id . ', :value' . $id . ')';
				$binds[':id' . $id] = $log->id_log;
				$binds[':key' . $id] = $key;
				$binds[':value' . $id] = $this->_getStringValue ($value);
			}
			_doQuery ($query . implode (', ', $values), $binds, $this->_getDBProfile ($pProfile));
		} else {
			$extra = new DAORecordCopixLogExtras ();
			foreach ($pExtras as $key => $value) {
				$extra->id_log = $log->id_log;
				$extra->key_extra = $key;
				$extra->value_extra = $this->_getStringValue ($value);
				$this->_getDAOExtras ($pProfile)->insert ($extra);
			}
		}
	}
	
	/**
	 * Supprime tous les logs de ce profil et retourne le nombre d'éléments supprimés
	 * 
	 * @param string $pProfile Nom du profil
	 * @return int 
	 */
	public function delete ($pProfile) {
		// requête en dur parceque les des DAO ne permet pas de faire ce genre de choses rapidement (rapide au niveau base de données)
		_doQuery ('DELETE FROM copixlogextras WHERE id_log IN (SELECT id_log FROM copixlog WHERE profile_log = :profile)', array (':profile' => $pProfile));
		return $this->_getDAO ($pProfile)->deleteBy (_daoSP ()->addCondition ('profile_log', '=', $pProfile));
	}
	
	/**
	 * Retourne les logs
	 *
	 * @param string $pProfile Nom du profil
	 * @param int $pStart Index du premier élément à retourner
	 * @param int $pCount Nombre d'éléments à retourner, null pour tous
	 * @return CopixLogData[]
	 */
	public function get ($pProfile, $pStart = 0, $pCount = null) {
		$toReturn = array ();
		$daoSP = _daoSP ()->addCondition ('profile_log', '=', $pProfile)->orderBy (array ('id_log', 'DESC'));

		// gestion des paramètres de recherche
		if ($pStart > 0) {
			$daoSP->setOffset ($pStart);
		}
		if ($pCount != null) {
			$daoSP->setCount ($pCount);
		}

		$results = $this->_getDAO ($pProfile)->findBy ($daoSP);
		foreach ($results as $result) {
			$extras = array ();
			foreach ($this->_getDAOExtras ($pProfile)->findBy (_daoSP ()->addCondition ('id_log', '=', $result->id_log)) as $extra) {
				$extras[$extra->key_extra] = $extra->value_extra;
			}
			$toReturn[] = new CopixLogData ($result->profile_log, $result->type_log, $result->level_log, $result->date_log, $result->message_log, $extras);
		}
		return $toReturn;
	}

	/**
	 * Retourne le nombre d'éléments
	 *
	 * @param string $pProfile Nom du profil
	 * @return int
	 */
	public function count ($pProfile) {
		$result = _doQuery ('SELECT COUNT(id_log) count FROM copixlog WHERE profile_log = :profile_log', array ('profile_log' => $pProfile), $this->_getDBProfile ($pProfile));
		return (count ($result) == 1) ? $result[0]->count : null;
	}

	/**
	 * Retourne l'HTML pour la configuration des informations spécifiques à la stratégie
	 *
	 * @param string $pProfile Nom du profil
	 * @return string
	 */
	public function getConfigEditor ($pProfile) {
		$tpl = new CopixTPL ();
		$tpl->assign ('profile', $this->_getDBProfile ($pProfile));
		$tpl->assign ('profiles', CopixConfig::instance ()->copixdb_getProfiles ());
		return $tpl->fetch ('copix:templates/logs/dbstrategyeditor.php');
	}

	/**
	 * Retourne le nom du profil de connexion à la base de données à utiliser
	 *
	 * @param string $pProfile Nom du profil de log
	 * @return string
	 */
	private function _getDBProfile ($pProfile) {
		return $this->_getConfig ($pProfile, 'profile', CopixConfig::instance ()->copixdb_getDefaultProfileName ());
	}

	/**
	 * Retourne le DAO à utiliser
	 *
	 * @param string $pProfile Nom du profil
	 */
	private function _getDAO ($pProfile) {
		if (!array_key_exists ($pProfile, $this->_dao)) {
			$this->_dao[$pProfile] = _dao ('copix:copixlog', $this->_getDBProfile ($pProfile));
		}
		return $this->_dao[$pProfile];
	}

	/**
	 * Retourne le DAO à utliser pour les extras
	 *
	 * @param string $pProfile Nom du profil
	 */
	private function _getDAOExtras ($pProfile) {
		if (!array_key_exists ($pProfile, $this->_daoExtras)) {
			$this->_daoExtras[$pProfile] = _dao ('copix:copixlogextras', $this->_getDBProfile ($pProfile));
		}
		return $this->_daoExtras[$pProfile];
	}
}