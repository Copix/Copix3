<?php
/**
 * @package standard
 * @subpackage admin
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Outils diverses pour le module admin
 *
 * @package standard
 * @subpackage admin
 */
class ToolsAdmin {
	/**
	 * Page actuellement ouverte
	 *
	 * @var string
	 */
	private static $_page = null;

	/**
	 * Constantes pour la gestion des pages
	 */
	const PAGE_LOG_LIST = 'admin|log|list';
	const PAGE_LOG_ADD = 'admin|log|add';
	const PAGE_LOG_EDIT = 'admin|log|edit';
	const PAGE_LOG_DELETE = 'admin|log|delete';
	const PAGE_LOG_DELETE_CONTENT = 'admin|log|deleteContent';
	const PAGE_LOG_SHOW = 'admin|log|show';

	const PAGE_PLUGIN_LIST = 'admin|plugin|list';
	const PAGE_PLUGIN_EDIT = 'admin|plugin|edit';
	const PAGE_PLUGIN_INFORMATIONS = 'admin|plugin|informations';

	const PAGE_PARAMETERS_LIST = 'admin|parameters|list';
	const PAGE_PARAMETERS_EDIT = 'admin|parameters|edit';
	const PAGE_PARAMETERS_DO_EDIT = 'admin|parameters|doEdit';
	const PAGE_PARAMETERS_COPIX = 'admin|parameters|copix';
	const PAGE_PARAMETERS_WEBSERVER = 'admin|parameters|webserver';
	const PAGE_PARAMETERS_DBSERVER = 'admin|parameters|dbserver';

	const PAGE_USERPREFERENCES_USERS = 'admin|userpreferences|users';
	const PAGE_USERPREFERENCES_MODULES = 'admin|userpreferences|modules';
	const PAGE_USERPREFERENCES_EDIT = 'admin|userpreferences|edit';
	const PAGE_USERPREFERENCES_DO_EDIT = 'admin|userpreferences|doEdit';

	const PAGE_GROUPPREFERENCES_GROUPS = 'admin|grouppreferences|groups';
	const PAGE_GROUPPREFERENCES_MODULES = 'admin|grouppreferences|modules';
	const PAGE_GROUPPREFERENCES_EDIT = 'admin|grouppreferences|edit';
	const PAGE_GROUPPREFERENCES_DO_EDIT = 'admin|grouppreferences|doEdit';

	/**
	 * Définit la page ouverte
	 *
	 * @param string $pPage Pave ouverte, utiliser les constantes ToolsAdmin::PAGE_X
	 * @param array $pExtras Informations supplémentaires
	 */
	public static function setPage ($pPage, $pExtras = array ()) {
		self::$_page = $pPage;

		// fil d'ariane
		$path = array ();

		// section logs
		if (strpos ($pPage, '|log|') !== false) {
			$path['admin|log|'] = _i18n ('logs.breadcrumb.list');
			if ($pPage != self::PAGE_LOG_LIST && $pPage != self::PAGE_LOG_ADD) {
				$path[_url ('admin|log|edit', array ('profile' => $pExtras['profile']))] = $pExtras['profile'];
			}

		// section configuration
		} else if (strpos ($pPage, '|parameters|') !== false) {
			$path['admin|parameters|'] = _i18n ('params.breadcrumb.list');

		// section préférences
		} else if (strpos ($pPage, '|userpreferences|') !== false) {
			$path['admin|userpreferences|'] = _i18n ('preferences.breadcrumb.users');
		}

		$title = null;
		switch ($pPage) {
			case self::PAGE_LOG_LIST :
				$title = _i18n ('logs.page.list');
				break;

			case self::PAGE_LOG_EDIT :
				$title = _i18n ('logs.page.edit', $pExtras['profile']);
				break;

			case self::PAGE_LOG_ADD :
				$path['admin|log|edit'] = 'Ajouter';
				$title = _i18n ('logs.page.add');
				break;

			case self::PAGE_LOG_DELETE :
				$path['#'] = _i18n ('logs.breadcrumb.delete');
				$title = _i18n ('logs.page.delete', $pExtras['profile']);
				break;

			case self::PAGE_LOG_DELETE_CONTENT :
				$path['#'] = _i18n ('logs.breadcrumb.deleteContent');
				break;

			case self::PAGE_LOG_SHOW :
				$path['#'] = _i18n ('logs.breadcrumb.show');
				$title = _i18n ('logs.page.show', $pExtras['profile']);
				break;

			// ----------------------------------

			case self::PAGE_PLUGIN_LIST :
				$path['#'] = _i18n ('plugin.breadcrumb.list');
				$title = _i18n ('plugin.page.list');
				break;

			case self::PAGE_PLUGIN_INFORMATIONS :
				$path['admin|plugin|'] = _i18n ('plugin.breadcrumb.list');
				$path['#'] = $pExtras['plugin'];
				$title = _i18n ('plugin.page.list');
				break;

			// ----------------------------------

			case self::PAGE_PARAMETERS_LIST :
				$title = _i18n ('params.page.list');
				break;

			case self::PAGE_PARAMETERS_EDIT :
				$path[_url ('admin|parameters|list', array ('choixModule' => $pExtras['module']))] = $pExtras['module'];
				$title = _i18n ('params.page.list', $pExtras['module']);
				break;

			case self::PAGE_PARAMETERS_DO_EDIT :
				$path[_url ('admin|parameters|list', array ('choixModule' => $pExtras['module']))] = $pExtras['module'];
				$path['#'] = _i18n ('params.breadcrumb.save');
				$title = _i18n ('params.page.save');
				break;

			case self::PAGE_PARAMETERS_COPIX :
				$path['#'] = _i18n ('params.frameworkConfig');
				$title = _i18n ('copix.titlepage');
				break;

			case self::PAGE_PARAMETERS_WEBSERVER :
				$path['#'] = _i18n ('params.webserver');
				$title = _i18n ('webserver.titlepage');
				break;

			case self::PAGE_PARAMETERS_DBSERVER :
				$path['#'] = $pExtras['profile'];
				$title = _i18n ('dbserver.titlePage', array ($pExtras['profile']));
				break;

			// ----------------------------------

			case self::PAGE_USERPREFERENCES_USERS :
				$title = _i18n ('preferences.page.users');
				break;

			case self::PAGE_USERPREFERENCES_MODULES :
				$url = _url ('admin|userpreferences|modules', array ('user' => $pExtras['user'], 'userhandler' => $pExtras['userhandler']));
				$path[$url] = _i18n ('preferences.breadcrumb.modules');
				$title = _i18n ('preferences.page.modules');
				break;

			case self::PAGE_USERPREFERENCES_EDIT :
				$url = _url ('admin|userpreferences|modules', array ('user' => $pExtras['user'], 'userhandler' => $pExtras['userhandler']));
				$path[$url] = _i18n ('preferences.breadcrumb.modules');
				$url = _url ('admin|userpreferences|edit', array ('user' => $pExtras['user'], 'userhandler' => $pExtras['userhandler'], 'module' => $pExtras['modulePref']));
				$path[$url] = $pExtras['modulePref'];
				$title = _i18n ('preferences.page.edit');
				break;

			// ----------------------------------

			case self::PAGE_GROUPPREFERENCES_GROUPS :
				$path['admin|grouppreferences|'] = _i18n ('grouppreferences.breadcrumb.groups');
				$title = _i18n ('grouppreferences.page.users');
				break;

			case self::PAGE_GROUPPREFERENCES_MODULES :
				$path['admin|grouppreferences|'] = _i18n ('grouppreferences.breadcrumb.groups');
				$url = _url ('admin|grouppreferences|modules', array ('groupName' => $pExtras['groupName'], 'grouphandler' => $pExtras['grouphandler']));
				$path[$url] = _i18n ('preferences.breadcrumb.modules');
				$title = _i18n ('grouppreferences.page.modules');
				break;

			case self::PAGE_GROUPPREFERENCES_EDIT :
				$path['admin|grouppreferences|'] = _i18n ('grouppreferences.breadcrumb.groups');
				$url = _url ('admin|grouppreferences|modules', array ('groupName' => $pExtras['groupName'], 'grouphandler' => $pExtras['grouphandler']));
				$path[$url] = _i18n ('preferences.breadcrumb.modules');
				$url = _url ('admin|grouppreferences|edit', array ('groupName' => $pExtras['groupName'], 'grouphandler' => $pExtras['grouphandler'], 'module' => $pExtras['modulePref']));
				$path[$url] = $pExtras['modulePref'];
				$title = _i18n ('grouppreferences.page.edit');
				break;
		}

		$page = CopixPage::add ();
		$page->setId ($pPage);
		$page->setTitle ($title);
		$page->setIsAdmin (true);
		$page->setExtras ($pExtras);
		
		_notify ('breadcrumb', array ('path' => $path));

		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = $title;
		return $ppo;
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