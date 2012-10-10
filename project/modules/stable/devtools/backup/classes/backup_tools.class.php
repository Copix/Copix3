<?php
/**
 * Divers outils pour le module backup
 */
class BackupTools {
	/**
	 * Constantes pour setPage
	 */
	const PAGE_PROFILES_LIST = 'backup|profiles|admin|list';
	const PAGE_PROFILES_ADD = 'backup|profiles|admin|add';
	const PAGE_PROFILES_DO_ADD = 'backup|profiles|admin|doAdd';
	const PAGE_PROFILES_EDIT = 'backup|profiles|admin|edit';
	const PAGE_PROFILES_DO_EDIT = 'backup|profiles|admin|doEdit';
	const PAGE_PROFILES_DELETE = 'backup|profiles|admin|delete';
	const PAGE_PROFILES_DO_DELETE = 'backup|profiles|admin|doDelete';

	const PAGE_BACKUP = 'backup|doBackup';

	const PAGE_RESTORE = 'backup|restore';
	const PAGE_RESTORE_INFOS = 'backup|restore|infos';
	const PAGE_DO_RESTORE = 'backup|restore|do';

	/**
	 * Définit la page ouverte
	 *
	 * @param string $pPage Identifiant de la page, utiliser BackupTools::PAGE_XX
	 * @return CopixPPO
	 */
	public static function setPage ($pPage) {
		$breadcrumb = array ();
		$toReturn = _ppo ();

		if (strpos ($pPage, '|restore') === false) {
			$breadcrumb[_url ('backup|profiles|')] = 'Profils de sauvegarde';
		}
		if (_request ('profile') != null) {
			$profile = BackupProfileServices::get (_request ('profile'));
			$breadcrumb[_url ('backup|profiles|edit', array ('profile' => $profile->getId ()))] = $profile->getCaption ();
		}

		switch ($pPage) {
			case self::PAGE_BACKUP :
				$toReturn->TITLE_PAGE = 'Sauvegarde';
				$breadcrumb[_url ('backup||', $profile->getId ())] = 'Sauvegarder';
				break;

			case self::PAGE_RESTORE :
				$toReturn->TITLE_PAGE = 'Restaurer une sauvegarde';
				$breadcrumb['backup||restore'] = 'Restaurer une sauvegarde';
				break;

			case self::PAGE_RESTORE_INFOS :
				$toReturn->TITLE_PAGE = 'Restaurer une sauvegarde';
				$breadcrumb['backup||restore'] = 'Restaurer une sauvegarde';
				$breadcrumb['backup||restoreinfos'] = 'Informations';
				break;

			case self::PAGE_DO_RESTORE :
				$toReturn->TITLE_PAGE = 'Restaurer une sauvegarde';
				$breadcrumb['backup||restore'] = 'Restaurer une sauvegarde';
				$breadcrumb['backup||restoreinfos'] = 'Informations';
				$breadcrumb['backup||doRestore'] = 'Restauration terminée';
				break;

			case self::PAGE_PROFILES_LIST :
				$toReturn->TITLE_PAGE = 'Profils de sauvegarde';
				break;

			case self::PAGE_PROFILES_ADD :
			case self::PAGE_PROFILES_DO_ADD :
				$breadcrumb[_url ('backup|profiles|edit')] = 'Ajouter';
				$toReturn->TITLE_PAGE = 'Ajouter un profil';
				break;

			case self::PAGE_PROFILES_EDIT :
			case self::PAGE_PROFILES_DO_EDIT :
				$toReturn->TITLE_PAGE = 'Modifier un profil';
				break;

			case self::PAGE_PROFILES_DELETE :
			case self::PAGE_PROFILES_DO_DELETE :
				$breadcrumb[_url ('backup|profiles|delete', array ('profile' => $profile->getId ()))] = 'Supprimer';
				$toReturn->TITLE_PAGE = 'Supprimer un profil';
				break;
		}
		
		$page = CopixPage::add ();
		$page->setIsAdmin (true);
		$page->setModule ('backup');
		$page->setTitle ($toReturn->TITLE_PAGE);
		$page->setId ($pPage);
		
		_notify ('breadcrumb', array ('path' => $breadcrumb));

		return $toReturn;
	}
}