<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 */

/**
 * Affichage des actions faites sur le CMS, loguées via le profil cms_actions
 *
 * @package cms
 * @subpackage heading
 */
class ActionGroupActionsLogs extends CopixActionGroup {
	const PROFILE = 'cms_actions';

	/**
	 * Appelée avant toute action
	 *
	 * @param string $pAction Nom de l'action
	 */
	protected function _beforeAction ($pAction) {
		CopixPage::add ()->setIsAdmin (true);
		if (CopixConfig::get('heading|usecmstheme') && CopixTheme::getInformations(CopixConfig::get('heading|cmstheme')) !==false){
        	CopixTPL::setTheme (CopixConfig::get('heading|cmstheme'));
        } 
		_notify ('breadcrumb', array ('path' => array ('heading|actionslogs|' => 'Actions du CMS')));
		_currentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Affichage des actions
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		if (!in_array ('cms_actions', CopixConfig::instance ()->copixlog_getRegistered ())) {
			return _arRedirect (_url ('heading|actionslogs|createLog'));
		}
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = 'Historique des événements';
		$ppo->page = _request ('page', 1);
		$ppo->countPerPage = 20;
		$ppo->action_element = null;

		// paramètres de recherche
		$ppo->search = _ppo ();
		$ppo->search->public_id_hei = _request ('public_id_hei', null);
		$ppo->search->users = _request ('users', array ());
		$ppo->search->types = _request ('types', array ());
		$ppo->search->date_from = _request ('date_from', null);
		$ppo->search->date_to = _request ('date_to', null);
		$ppo->search->hierarchy_hei = _request ('hierarchy_hei', null);
		if ($ppo->search->hierarchy_hei <= 0) {
			$ppo->search->hierarchy_hei = null;
		}

		$service = _ioClass ('HeadingActionsService');
		$ppo->count = $service->countBy (self::PROFILE, $ppo->search);
		$ppo->logs = $service->search (self::PROFILE, $ppo->search, ($ppo->page - 1) * $ppo->countPerPage, $ppo->countPerPage);
		$ppo->users = $service->getUsers (self::PROFILE);
		$ppo->types = $service->getTypes ();
		$ppo->elementsTypes = _ioClass ('HeadingElementType')->getList ();


		return _arPPO ($ppo, 'actionslogs/viewlogs.php');
	}

	/**
	 * Définit les paramètres de recherche
	 *
	 * @return CopixActionReturn
	 */
	public function processSetSearchParams () {
		return _arRedirect (_url ('heading|actionslogs|'));
	}

	/**
	 * Formulaire de création du profil de log
	 *
	 * @return CopixActionReturn
	 */
	public function processCreateLog () {
		_notify ('breadcrumb', array ('path' => array ('Création du profil de log')));
		return _arPPO (_ppo (array ('TITLE_PAGE' => 'Profil de log')), 'actionslogs/createlog.php');
	}

	/**
	 * Création du profil de log
	 *
	 * @return CopixActionReturn
	 */
	public function processDoCreateLog () {
		$profile = array (
			'name' => 'cms_actions',
			'enabled' => true,
			'handle' => array ('cms_action'),
			'strategy' => 'heading|headingactionsservice',
			'level' => array (CopixLog::VERBOSE, CopixLog::INFORMATION, CopixLog::NOTICE, CopixLog::WARNING, CopixLog::ERROR, CopixLog::EXCEPTION, CopixLog::FATAL_ERROR)
		);
		$errors = CopixLogConfigFile::add ($profile);
		if ($errors instanceof CopixErrorObject) {
			throw new CopixException (implode (', ', $errors->asArray ()));
		}
		return _arRedirect (_url ('heading|actionslogs|'));
	}
}