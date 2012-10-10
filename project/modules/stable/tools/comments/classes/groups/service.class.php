<?php
/**
 * Gestion des groupes de commentaire
 */
class CommentsGroupsService {
	/**
	 * Profil de connexion à utiliser, null pour le profil par défaut
	 * 
	 * @var string
	 */
	private static $_dbProfile = null;

	/**
	 * Retourne un record avec un objet
	 * 
	 * @param CommentsGroupsGroup $pObject Group
	 * @return DAORecordcomments_groups
	 */
	private static function _getRecord ($pObject) {
		$toReturn = new DAORecordcomments_groups ();
		$toReturn->id_group = $pObject->getId ();
		$toReturn->caption_group = $pObject->getCaption ();
		$toReturn->author_required_group = ($pObject->isAuthorRequired () ? 1 : 0);
		$toReturn->website_required_group = ($pObject->isWebsiteRequired () ? 1 : 0);
		$toReturn->email_required_group = ($pObject->isEmailRequired () ? 1 : 0);
		return $toReturn;
	}

	/**
	 * Retourne un objet avec un record
	 * 
	 * @param DAORecordcomments_groups $pRecord Record
	 * @return CommentsGroupsGroup
	 */
	private static function _getObject ($pRecord) {
		$toReturn = new CommentsGroupsGroup ();
		$toReturn->setId ($pRecord->id_group);
		$toReturn->setCaption ($pRecord->caption_group);
		$toReturn->setIsAuthorRequired ($pRecord->author_required_group);
		$toReturn->setIsWebsiteRequired ($pRecord->website_required_group);
		$toReturn->setIsEmailRequired ($pRecord->email_required_group);
		return $toReturn;
	}

	/**
	 * Retourne un objet vierge
	 * 
	 * @return CommentsGroupsGroup
	 */
	public static function create () {
		return new CommentsGroupsGroup ();
	}

	/**
	 * Retourne le nombre d'éléments en base
	 * 
	 * @return int
	 */
	public static function count () {
		return DAOcomments_groups::instance (self::$_dbProfile)->countBy (_daoSP ());
	}

	/**
	 * Retourne la liste des éléments
	 * 
	 * @param int $pOffset Index du premier élément à retourner
	 * @param int $pCount Nombre d'élément à retourner, null pour tous
	 * @return CommentsGroupsGroup[]
	 */
	public static function getList ($pOffset = null, $pCount = null) {
		$toReturn = array ();
		$sp = _daoSP ()->orderBy ('caption_group');
		if ($pOffset != null) {
			$sp->setOffset ($pOffset);
		}
		if ($pCount != null) {
			$sp->setCount ($pCount);
		}
		foreach (DAOcomments_groups::instance (self::$_dbProfile)->findBy ($sp) as $record) {
			$toReturn[$record->id_group] = self::_getObject ($record);
		}
		return $toReturn;
	}

	/**
	 * Retourne l'élément demandé
	 * 
	 * @param string $pId Identifiant
	 * @return CommentsGroupsGroup
	 */
	public static function get ($pId) {
		$record = DAOcomments_groups::instance (self::$_dbProfile)->get ($pId);
		if ($record === false) {
			throw new CommentsGroupsException (_i18n ('comments|commentsgroups.service.error.notFound', $pId));
		}
		return self::_getObject ($record);
	}

	/**
	 * Indique si l'élément demandé existe
	 * 
	 * @param string $pId Identifiant
	 * @return boolean
	 */
	public static function exists ($pId) {
		try {
			self::get ($pId);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Ajoute l'élément en base
	 * 
	 * @param CommentsGroupsGroup $pObject Group
	 */
	public static function insert ($pObject) {
		_currentUser ()->assertCredential ('basic:admin');

		// vérification de la validité des données
		if (self::exists ($pObject->getId ())) {
			throw new CommentsGroupsException ('L\'identifiant de groupe "' . $pObject->getId () . '" existe déja.', CommentsGroupsException::ID_EXISTS);
		}
		$errors = $pObject->isValid ();
		if ($errors instanceof CopixErrorObject) {
			throw new CommentsGroupsException (_i18n ('comments|commentsgroups.service.error.insertInvalidObject'), CommentsGroupsException::VALIDATOR_ERRORS, array (), $errors->asArray ());
		}

		// insertion en base
		$record = self::_getRecord ($pObject);
		DAOcomments_groups::instance (self::$_dbProfile)->insert ($record);
	}

	/**
	 * Modifie l'élément en base
	 * 
	 * @param CommentsGroupsGroup $pObject Group
	 */
	public static function update ($pObject) {
		_currentUser ()->assertCredential ('basic:admin');

		// vérification de la validité des données
		$errors = $pObject->isValid ();
		if ($errors instanceof CopixErrorObject) {
			throw new CommentsGroupsException (_i18n ('comments|commentsgroups.service.error.updateInvalidObject'), CommentsGroupsException::VALIDATOR_ERRORS, array (), $errors->asArray ());
		}

		// modification en base
		DAOcomments_groups::instance (self::$_dbProfile)->update (self::_getRecord ($pObject));
	}

	/**
	 * Supprime l'élément en base
	 * 
	 * @param string $pId Identifiant
	 */
	public static function delete ($pId) {
		_currentUser ()->assertCredential ('basic:admin');

		// permet de vérifier l'existance de l'élément
		$element = self::get ($pId);

		CommentsService::deleteByGroup ($pId);
		DAOcomments_groups::instance (self::$_dbProfile)->delete ($pId);
	}
}