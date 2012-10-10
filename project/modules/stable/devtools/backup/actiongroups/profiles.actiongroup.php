<?php
/**
 * Administration des profils de sauvegarde
 */
class ActionGroupProfiles extends CopixActionGroup {
	/**
	 * Appelée avant toute action
	 *
	 * @param string $pAction Nom de l'action
	 */
	protected function _beforeAction ($pAction) {
		_currentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Liste des profils
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$ppo = BackupTools::setPage (BackupTools::PAGE_PROFILES_LIST);
		$ppo->profiles = BackupProfileServices::getList ();
		$ppo->highlight = _request ('highlight');
		return _arPPO ($ppo, 'backup|profiles/admin.list.php');
	}

	/**
	 * Edition d'un profil
	 *
	 * @return CopixActionReturn
	 */
	public function processEdit () {
		$mode = (_request ('profile') == null) ? 'add' : 'edit';
		$idErrors = _request ('idErrors');

		if ($mode == 'add') {
			$ppo = BackupTools::setPage (BackupTools::PAGE_PROFILES_ADD);
			$ppo->profile = ($idErrors == null) ? BackupProfileServices::create () : CopixSession::get ('profile_edit_' . $idErrors, 'backup');
		} else {
			$ppo = BackupTools::setPage (BackupTools::PAGE_PROFILES_EDIT);
			$ppo->profile = ($idErrors == null) ? BackupProfileServices::get (_request ('profile')) : CopixSession::get ('profile_edit_' . $idErrors, 'backup');
		}
		$ppo->mode = $mode;
		$ppo->types = BackupTypeServices::getList ();
		foreach (CopixConfig::instance ()->copixdb_getProfiles () as $profile) {
			$ppo->dbprofiles[$profile] = $profile;
		}
		$ppo->saveDb = (($mode == 'add' && $idErrors == null) || ($ppo->profile->saveAllTables () || count ($ppo->profile->getTables ()) > 0));

		if ($idErrors != null) {
			$ppo->errors = $ppo->profile->isValid ()->asArray ();
		}

		return _arPPO ($ppo, 'backup|profiles/admin.edit.php');
	}

	/**
	 * Effectue la modification
	 *
	 * @return CopixActionReturn
	 */
	public function processDoEdit () {
		$idProfile = _request ('profile');
		($idProfile == null) ? BackupTools::setPage (BackupTools::PAGE_PROFILES_DO_ADD) : BackupTools::setPage (BackupTools::PAGE_PROFILES_DO_EDIT);
		$profile = ($idProfile == null) ? BackupProfileServices::create () : BackupProfileServices::get ($idProfile);
		$profile->setCaption (_request ('caption'));
		$profile->setFileName (_request ('fileName'));
		$profile->setIdType (_request ('type'));
		$profile->getType ()->setFromArray (CopixRequest::asArray ());
		$profile->clearTables ();
		$profile->setDbProfile (_request ('dbprofile'));
		$profile->setSaveAllTables (_request ('saveAllTables') == 1);
		if (!$profile->saveAllTables ()) {
			if (is_array (_request ('tables'))) {
				foreach (_request ('tables') as $table) {
					$profile->addTable ($table);
				}
			}
		}
		$profile->clearFiles ();
		$profile->setFilesPath (_request ('filesPath'));
		foreach (_request ('files', array ()) as $file) {
			$profile->addFile ($file);
		}

		// validation des données
		if ($profile->isValid () !== true) {
			$idErrors = (_request ('idErrors') != null) ? _request ('idErrors') : uniqid ();
			CopixSession::set ('profile_edit_' . $idErrors, $profile, 'backup');
			return _arRedirect (_url ('backup|profiles|edit', array ('profile' => $profile->getId (), 'idErrors' => $idErrors)));
		}

		$profile = BackupProfileServices::save ($profile);
		// si on veut ajouter
		if ($idProfile == null) {
			$title = 'Ajout d\'un profil';
			$message = 'Le profil "' . $profile->getCaption () . '" a été ajouté.';
		// si on veut modifier
		} else {
			$title = 'Modification d\'un profil';
			$message = 'Le profil "' . $profile->getCaption () . '" a été modifié.';
		}

		$params = array (
			'title' => $title,
			'redirect_url' => _url ('backup|profiles|', array ('highlight' => $profile->getId ())),
			'message' => $message,
			'links' => array (
				_url ('backup|profiles|', array ('highlight' => $profile->getId ())) => 'Retour à la liste des profils',
				_url ('backup|profiles|edit', array ('profile' => $profile->getId ())) => 'Retour à l\'édition du profil'
			)
		);
		return CopixActionGroup::process ('generictools|messages::getInformation', $params);
	}

	/**
	 * Demande confirmation de suppression
	 *
	 * @return CopixActionReturn
	 */
	public function processDelete () {
		BackupTools::setPage (BackupTools::PAGE_PROFILES_DELETE);
		$id = _request ('profile');
		$profile = BackupProfileServices::get ($id);

		return CopixActionGroup::process (
			'generictools|Messages::getConfirm',
			array (
				'message' => 'Êtes vous sûr de vouloir supprimer le profil de sauvegarde "' . $profile->getCaption () . '" ?',
				'confirm' => _url ('backup|profiles|doDelete', array ('profile' => $id)),
				'cancel' => _url ('backup|profiles|'),
				'title' => 'Confirmation de suppression'
			)
		);
	}

	/**
	 * Effectue la suppression d'une nouveauté
	 *
	 * @return CopixActionReturn
	 */
	public function processDoDelete () {
		BackupTools::setPage (BackupTools::PAGE_PROFILES_DO_DELETE);
		$profile = BackupProfileServices::get (_request ('profile'));
		BackupProfileServices::delete ($profile->getId ());
		return CopixActionGroup::process ('generictools|Messages::getInformation',
			array (
				'title' => 'Suppression effectuée',
				'message' => 'La suppression du profil de sauvegarde "' . $profile->getCaption () . '" a été effectuée.',
				'links' => array (_url ('backup|profiles|') => 'Retour à la liste des profils'),
				'redirect_url' => _url ('backup|profiles|')
			)
		);
	}

	/**
	 * Retourne uniquement les champs de configuration d'un type de sauvegarde
	 *
	 * @return CopixActionReturn
	 */
	public function processGetTypeOptionsEditor () {
		$ppo = new CopixPPO (array ('MAIN' => CopixZone::process ('backup|TypeOptionsEditor', array ('idType' => _request ('type')))));
		return _arDirectPPO ($ppo, 'generictools|blank.tpl');
	}

	/**
	 * Retourne uniquement les champs de configuration d'un type de sauvegarde
	 *
	 * @return CopixActionReturn
	 */
	public function processGetTableList () {
		$ppo = new CopixPPO (array ('MAIN' => CopixZone::process ('backup|ListTables', array ('dbprofile' => _request ('dbprofile')))));
		return _arDirectPPO ($ppo, 'generictools|blank.tpl');
	}
}