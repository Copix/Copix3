<?php
/**
 * Gestion des commentaires
 */
class CommentsService {
	/**
	 * Profil de connexion à utiliser, null pour le profil par défaut
	 * 
	 * @var string
	 */
	private static $_dbProfile = null;

	/**
	 * Retourne un record avec un objet
	 * 
	 * @param CommentsComment $pObject Comment
	 * @return DAORecordcomments
	 */
	private static function _getRecord ($pObject) {
		$toReturn = DAORecordcomments::create ();
		$toReturn->id_comment = $pObject->getId ();
		$toReturn->id_group = $pObject->getGroup ()->getId ();
		$toReturn->author_comment = $pObject->getAuthor ();
		$toReturn->website_comment = $pObject->getWebsite ();
		$toReturn->email_comment = $pObject->getEmail ();
		$toReturn->value_comment = $pObject->getComment ();
		$toReturn->date_comment = $pObject->getDate ('YmdHis');
		return $toReturn;
	}

	/**
	 * Retourne un objet avec un record
	 * 
	 * @param DAORecordcomments $pRecord Record
	 * @return CommentsComment
	 */
	private static function _getObject ($pRecord) {
		$toReturn = self::create ();
		$toReturn->setId ($pRecord->id_comment);
		$toReturn->setAuthor ($pRecord->author_comment);
		$toReturn->setWebsite ($pRecord->website_comment);
		$toReturn->setEmail ($pRecord->email_comment);
		$toReturn->setComment ($pRecord->value_comment);
		$toReturn->setDate ($pRecord->date_comment);
		$toReturn->setGroup (CommentsGroupsService::get ($pRecord->id_group));
		return $toReturn;
	}

	/**
	 * Retourne un objet vierge
	 * 
	 * @return CommentsComment
	 */
	public static function create () {
		return new CommentsComment ();
	}

	/**
	 * Retourne le nombre d'éléments en base
	 *
	 * @param string $pGroup Identifiant du groupe
	 * @return int
	 */
	public static function count ($pGroup = null) {
		$sp = _daoSP ();
		if ($pGroup != null) {
			$sp->addCondition ('id_group', '=', $pGroup);
		}
		return DAOcomments::instance (self::$_dbProfile)->countBy ($sp);
	}

	/**
	 * Retourne la liste des éléments
	 *
	 * @param string $pGroup Identifiant du groupe
	 * @param int $pOffset Index du premier élément à retourner
	 * @param int $pCount Nombre d'élément à retourner, null pour tous
	 * @return CommentsComment[]
	 */
	public static function getList ($pGroup = null, $pOffset = null, $pCount = null) {
		$toReturn = array ();
		$sp = _daoSP ()->orderBy (array ('date_comment', 'DESC'));
		if ($pOffset != null) {
			$sp->setOffset ($pOffset);
		}
		if ($pCount != null) {
			$sp->setCount ($pCount);
		}
		if ($pGroup != null) {
			$sp->addCondition ('id_group', '=', $pGroup);
		}
		foreach (DAOcomments::instance (self::$_dbProfile)->findBy ($sp) as $record) {
			$toReturn[$record->id_comment] = self::_getObject ($record);
		}
		return $toReturn;
	}

	/**
	 * Retourne l'élément demandé
	 * 
	 * @param int $pId Identifiant
	 * @return CommentsComment
	 */
	public static function get ($pId) {
		$record = DAOcomments::instance (self::$_dbProfile)->get ($pId);
		if ($record === false) {
			throw new CommentsException (_i18n ('comments|comments.service.error.notFound', $pId));
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
			self::get ($pId);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Ajoute l'élément en base
	 * 
	 * @param CommentsComment $pObject Comment
	 */
	public static function insert ($pObject) {
		_currentUser ()->assertCredential ('basic:admin');

		// vérification de la validité des données
		$errors = $pObject->isValid ();
		if ($errors instanceof CopixErrorObject) {
			throw new CommentsException (_i18n ('comments|comments.service.error.insertInvalidObject'), CommentsException::VALIDATOR_ERRORS, array (), $errors->asArray ());
		}

		// insertion en base
		$record = self::_getRecord ($pObject);
		$record->id_comment = null;
		DAOcomments::instance (self::$_dbProfile)->insert ($record);
		$pObject->setId ($record->id_comment);
	}

	/**
	 * Modifie l'élément en base
	 * 
	 * @param CommentsComment $pObject Comment
	 */
	public static function update ($pObject) {
		_currentUser ()->assertCredential ('basic:admin');

		// vérification de la validité des données
		$errors = $pObject->isValid ();
		if ($errors instanceof CopixErrorObject) {
			throw new CommentsException (_i18n ('comments|comments.service.error.updateInvalidObject'), CommentsException::VALIDATOR_ERRORS, array (), $errors->asArray ());
		}

		// modification en base
		DAOcomments::instance (self::$_dbProfile)->update (self::_getRecord ($pObject));
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

		DAOcomments::instance (self::$_dbProfile)->delete ($pId);
	}

	/**
	 * Supprime tous les commentaires du groupe donné
	 *
	 * @param string $pGroup Identifiant du groupe
	 */
	public static function deleteByGroup ($pGroup) {
		_currentUser ()->assertCredential ('basic:admin');
		DAOcomments::instance (self::$_dbProfile)->deleteBy (_daoSP ()->addCondition ('id_group', '=', $pGroup));
	}
}