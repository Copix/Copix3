<?php
/**
 * Gestion des types de sauvegarde
 */
class BackupTypeServices {
	/**
	 * Retourne la liste des types
	 *
	 * @return array
	 */
	public static function getList () {
		return array (
			'email' => 'E-mail',
			'download' => 'Téléchargement'
		);
	}

	/**
	 * Retourne le type de sauvegarde par défaut
	 *
	 * @return string
	 */
	public static function getDefault () {
		return 'email';
	}

	/**
	 * Retourne le type demandé
	 *
	 * @param string $pId Identifiant de type (email, file, ftp)
	 * @param int $pIdProfile Identifiant de profil si on en a un
	 * @return BackupType
	 */
	public static function get ($pId, $pIdProfile = null) {
		if (!in_array ($pId, array_keys (self::getList ()))) {
			throw new BackupException ('Le type "' . $pId . '" est inconnu.');
		}
		$className = 'BackupType' . $pId;
		$toReturn = new $className ();

		if ($pIdProfile != null) {
			$toReturn->setIdProfile ($pIdProfile);
			$toReturn->load ();
		}

		return $toReturn;
	}

	/**
	 * Sauvegarde le type
	 *
	 * @param BackupProfile $pProfile Profil à sauvegarder
	 */
	public static function save ($pProfile) {
		// suppression de toutes les sauvegardes de type
		// évite d'avoir à chercher si on doit faire un update ou un insert, et permet de supprimer les configs obsolètes lors du changement de type pour un profil
		self::delete ($pProfile->getId ());
		$pProfile->getType ()->save ();
	}

	/**
	 * Supprime la configuration des types
	 *
	 * @param int $pIdProfile Identifiant du profil
	 */
	public static function delete ($pIdProfile) {
		foreach (self::getList () as $id => $caption) {
			self::get ($id, $pIdProfile)->delete ();
		}
	}
}