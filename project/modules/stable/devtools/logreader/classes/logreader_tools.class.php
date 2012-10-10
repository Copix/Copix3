<?php
/**
 * Diverses méthodes
 */
class LogReaderTools {
	/**
	 * Constantes pour définir les pages
	 */
	const PAGE_LIST = 'logreader|admin|list';
	const PAGE_ADD = 'logreader|admin|add';
	const PAGE_DO_ADD = 'logreader|admin|doadd';
	const PAGE_EDIT = 'logreader|admin|edit';
	const PAGE_DO_EDIT = 'logreader|admin|doedit';
	const PAGE_DELETE = 'logreader|admin|delete';
	const PAGE_DO_DELETE = 'logreader|admin|dodelete';
	const PAGE_SHOW = 'logreader|admin|show';

	/**
	 * Page ouverte
	 *
	 * @var string
	 */
	private static $_page = null;

	/**
	 * Définition de la page ouverte
	 *
	 * @param string $pPage Page, utiliser LogReaderTools::PAGE_X
	 */
	public static function setPage ($pPage) {
		self::$_page = $pPage;
		$page = CopixPage::add ();
		$page->setModule ('logreader');
		$page->setId ($pPage);
		$page->setIsAdmin (true);

		$breadcrumb = array (
			_url ('admin||') => 'Administration',
			_url ('admin||', array ('modules' => 'logreader')) => 'Fichiers de log'
		);

		if (_request ('file') != null) {
			$file = LogReaderServices::get (_request ('file'));
		}

		switch ($pPage) {
			case self::PAGE_LIST :
				$title = 'Liste des fichiers de log';
				$breadcrumb[_url ('logreader||')] = 'Liste';
				break;

			case self::PAGE_ADD :
				$title = 'Ajouter un fichier de log';
				$breadcrumb['logreader||'] = 'Liste';
				$breadcrumb['logreader||edit'] = 'Ajouter';
				break;

			case self::PAGE_DO_ADD :
				$title = 'Ajoute un fichier de log';
				$breadcrumb['logreader||'] = 'Liste';
				$breadcrumb['logreader||edit'] = 'Ajouter';
				$breadcrumb['#'] = 'Ajoute';
				break;

			case self::PAGE_EDIT :
				$title = 'Modifier un fichier de log';
				$breadcrumb['logreader||'] = 'Liste';
				$breadcrumb[_url ('logreader||edit', array ('file' => $file->getId ()))] = $file->getFileName ();
				break;

			case self::PAGE_DO_EDIT :
				$title = 'Modifier un fichier de log';
				$breadcrumb['logreader||'] = 'Liste';
				$breadcrumb[_url ('logreader||edit', array ('file' => $file->getId ()))] = $file->getFileName ();
				$breadcrumb['#'] = 'Modifie';
				break;

			case self::PAGE_DELETE :
				$title = 'Supprimer un fichier de log';
				$breadcrumb['logreader||'] = 'Liste';
				$breadcrumb[_url ('logreader||edit', array ('file' => $file->getId ()))] = $file->getFileName ();
				$breadcrumb[_url ('logreader||delete', array ('file' => $file->getId ()))] = 'Supprimer';
				break;

			case self::PAGE_DO_DELETE :
				$title = 'Supprime un fichier de log';
				$breadcrumb['logreader||'] = 'Liste';
				$breadcrumb[_url ('logreader||edit', array ('file' => $file->getId ()))] = $file->getFileName ();
				$breadcrumb[_url ('logreader||delete', array ('file' => $file->getId ()))] = 'Supprimer';
				break;

			case self::PAGE_SHOW :
				$title = 'Contenu du log';
				$breadcrumb['logreader||'] = 'Liste';
				$breadcrumb[_url ('logreader||edit', array ('file' => $file->getId ()))] = $file->getFileName ();
				$breadcrumb[_url ('logreader||show', array ('file' => $file->getId ()))] = 'Contenu';
				break;
		}

		_notify ('breadcrumb', array ('path' => $breadcrumb));
		$page->setTitle ($title);
		return new CopixPPO (array ('TITLE_PAGE' => $title));
	}

	/**
	 * Retourne la page ouverte
	 *
	 * @return string
	 */
	public static function getPage () {
		return self::$_page;
	}
}
