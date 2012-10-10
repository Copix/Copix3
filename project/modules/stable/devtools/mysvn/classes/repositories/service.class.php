<?php
/**
 * Gestion des dépots
 */
class RepositoriesService {
	/**
	 * Profil de connexion à utiliser, null pour le profil par défaut
	 * 
	 * @var string
	 */
	private static $_dbProfile = null;

	/**
	 * Retourne un record avec un objet
	 * 
	 * @param RepositoriesRepository $pObject Repository
	 * @return DAORecordmysvn_repositories
	 */
	private static function _getRecord ($pObject) {
		$toReturn = new DAORecordmysvn_repositories ();
		$toReturn->id_repository = $pObject->getId ();
		$toReturn->caption_repository = $pObject->getCaption ();
		$toReturn->url_repository = $pObject->getUrl ();
		return $toReturn;
	}

	/**
	 * Retourne un objet avec un record
	 * 
	 * @param DAORecordmysvn_repositories $pRecord Record
	 * @return RepositoriesRepository
	 */
	private static function _getObject ($pRecord) {
		$toReturn = new RepositoriesRepository ();
		$toReturn->setId ($pRecord->id_repository);
		$toReturn->setCaption ($pRecord->caption_repository);
		$toReturn->setUrl ($pRecord->url_repository);
		return $toReturn;
	}

	/**
	 * Retourne un objet vierge
	 * 
	 * @return RepositoriesRepository
	 */
	public static function create () {
		return new RepositoriesRepository ();
	}

	/**
	 * Retourne le nombre d'éléments en base
	 * 
	 * @return int
	 */
	public static function count () {
		return DAOmysvn_repositories::instance (self::$_dbProfile)->countBy (_daoSP ());
	}

	/**
	 * Retourne la liste des éléments
	 * 
	 * @param int $pOffset Index du premier élément à retourner
	 * @param int $pCount Nombre d'élément à retourner, null pour tous
	 * @return RepositoriesRepository[]
	 */
	public static function getList ($pOffset = null, $pCount = null) {
		$toReturn = array ();
		$sp = _daoSP ();
		if ($pOffset != null) {
			$sp->setOffset ($pOffset);
		}
		if ($pCount != null) {
			$sp->setCount ($pCount);
		}
		foreach (DAOmysvn_repositories::instance (self::$_dbProfile)->findBy ($sp) as $record) {
			$toReturn[$record->id_repository] = self::_getObject ($record);
		}
		return $toReturn;
	}

	/**
	 * Retourne l'élément demandé
	 * 
	 * @param int $pId Identifiant
	 * @return RepositoriesRepository
	 */
	public static function get ($pId) {
		$record = DAOmysvn_repositories::instance (self::$_dbProfile)->get ($pId);
		if ($record === false) {
			throw new RepositoriesException (_i18n ('mysvn|repositories.service.error.notFound', $pId));
		}
		return self::_getObject ($record);
	}

	/**
	 * Indique si l'élément demandé existe
	 * 
	 * @param int $pId Identifiant
	 * @return boolean
	 */
	public static function exists ($pId) {
		try {
			$this->get ($pId);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Ajoute l'élément en base
	 * 
	 * @param RepositoriesRepository $pObject Repository
	 */
	public static function insert ($pObject) {
		_currentUser ()->assertCredential ('basic:admin');

		// vérification de la validité des données
		$errors = $pObject->isValid ();
		if ($errors instanceof CopixErrorObject) {
			throw new RepositoriesException (_i18n ('mysvn|repositories.service.error.insertInvalidObject'), RepositoriesException::VALIDATOR_ERRORS, array (), $errors->asArray ());
		}

		// insertion en base
		$record = self::_getRecord ($pObject);
		$record->id_repository = null;
		DAOmysvn_repositories::instance (self::$_dbProfile)->insert ($record);
		$pObject->setId ($record->id_repository);
	}

	/**
	 * Modifie l'élément en base
	 * 
	 * @param RepositoriesRepository $pObject Repository
	 */
	public static function update ($pObject) {
		_currentUser ()->assertCredential ('basic:admin');

		// vérification de la validité des données
		$errors = $pObject->isValid ();
		if ($errors instanceof CopixErrorObject) {
			throw new RepositoriesException (_i18n ('mysvn|repositories.service.error.updateInvalidObject'), RepositoriesException::VALIDATOR_ERRORS, array (), $errors->asArray ());
		}

		// modification en base
		DAOmysvn_repositories::instance (self::$_dbProfile)->update (self::_getRecord ($pObject));
	}

	/**
	 * Supprime l'élément en base
	 * 
	 * @param int $pId Identifiant
	 */
	public static function delete ($pId) {
		_currentUser ()->assertCredential ('basic:admin');

		// permet de vérifier l'existance de l'élément
		$element = self::get ($pId);

		DAOmysvn_repositories::instance (self::$_dbProfile)->delete ($pId);
	}
}