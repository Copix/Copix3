<?php
/**
 * @package standard
 * @subpackage admin
 * @author Croës Gérald, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion des profils de log
 *
 * @package standard
 * @subpackage admin
 */
class ActionGroupLog extends CopixActionGroup {
	/**
	 * Appelée avant toute action
	 *
	 * @param string $pActionName Nom de l'action
	 */
	protected function _beforeAction ($pActionName) {
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
		// suppression des erreurs en session, pour éviter de la surcharger inutilement
		if (!in_array (strtolower ($pActionName), array ('edit', 'doedit'))) {
			CopixSession::destroyNamespace ('admin|logs|errors');
		}
	}

	/**
	 * Retourne un tableau contenant les informations sur le profil
	 *
	 * @param string $pName Nom du profil
	 * @throws ModuleAdminException
	 */
	private function _getProfile ($pName) {
		$profile = CopixConfig::instance ()->copixlog_getProfile ($pName);
		if ($profile == null) {
			throw new ModuleAdminException (_i18n ('admin|logs.error.profileNotFound', $pName), ModuleAdminException::LOG_PROFILE_NOT_FOUND);
		}
		return $profile;
	}
	
	/**
	 * Liste des profils de log
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_LOG_LIST);
		
		// si on a indiqué un ordre de tri
		if (($order = _request ('order')) != null) {
			CopixSession::set ('log|list|order', $order, 'admin');
		}
		$ppo->order = CopixSession::get ('log|list|order', 'admin', 'profile_asc');
		// on veut plus d'infos sur les profils que celles renvoyées par CopixConfig
		$ppo->profiles = CopixConfig::instance ()->copixlog_getRegisteredProfiles ();
		$profilesConfigFile = array_keys (CopixLogConfigFile::getList ());
		foreach ($ppo->profiles as &$profile) {
			// on transforme le profile en CopixPPO pour ne pas avoir de notices sur les propriétés qu'on ne pourra pas remplir
			$profile = new CopixPPO ($profile);
			$strategy = CopixLog::getStrategyDescription ($profile['strategy']);
			$profile['strategyCaption'] = $strategy->getCaption ();
			$profile['isReadable'] = CopixLog::isReadable ($profile['name']);
			$profile['isEditable'] = in_array ($profile['name'], $profilesConfigFile);
			if ($profile['isReadable']) {
				$profile['count'] = CopixLog::count ($profile['name']);
				$profile['size'] = CopixLog::getSize ($profile['name']);
			}
		}

		// tri des profils
		$unsorted = array ();
		list ($orderName, $orderType) = explode ('_', $ppo->order);
		foreach ($ppo->profiles as &$profile) {
			switch ($orderName) {
				case 'profile' :
					$key = $profile['name'];
					break;
				case 'type' :
					$key = $profile['handle'][0] . '_' . $profile['name'];
					break;
				case 'strategy' :
					$key = $profile['strategy'] . '_' . $profile['name'];
					break;
				case 'count' :
					$key = $profile['count'] . '_' . $profile['name'];
					break;
				case 'size' :
					$key = $profile['size'] . '_' . $profile['name'];
					break;
				default :
					throw new ModuleAdminException (_i18n ('commons.error.invalidOrder', $ppo->order), ModuleAdminException::LOG_INVALID_ORDER);
			}
			$unsorted[$key] = $profile;
		}
		if ($orderType == 'asc') {
			ksort ($unsorted);
		} else {
			krsort ($unsorted);
		}
		// utilisation de array_values pour ne pas garder les clefs de $unsorted, générées pour trier facilement
		$ppo->profiles = array_values ($unsorted);
		
		$ppo->highlight = _request ('profile');
		$ppo->types = CopixLog::getTypes ();
		
		return _arPPO ($ppo, 'log/list.php');
	}
	
	/**
	 * Active un profil de log
	 *
	 * @return CopixActionReturn
	 */
	public function processEnable () {
		$profile = _request ('profile');
		ToolsAdmin::setPage (ToolsAdmin::PAGE_LOG_EDIT, array ('profile' => $profile));
		CopixLogConfigFile::enable ($profile);
		return _arRedirect (_url ('admin|log|', array ('profile' => $profile)));
	}
	
	/**
	 * Désactive un profil de log
	 *
	 * @return CopixActionReturn
	 */
	public function processDisable () {
		$profile = _request ('profile');
		ToolsAdmin::setPage (ToolsAdmin::PAGE_LOG_EDIT, array ('profile' => $profile));
		CopixLogConfigFile::disable ($profile);
		return _arRedirect (_url ('admin|log|', array ('profile' => $profile)));
	}
	
	/**
	 * Formulaire d'édition d'un profil
	 *
	 * @return CopixActionReturn
	 */
	public function processEdit () {
		$profile = _request ('profile');
		$mode = ($profile == null) ? 'add' : 'edit';
		$ppo = ($mode == 'add') ? ToolsAdmin::setPage (ToolsAdmin::PAGE_LOG_ADD) : ToolsAdmin::setPage (ToolsAdmin::PAGE_LOG_EDIT, array ('profile' => $profile));
		$ppo->mode = $mode;
		$ppo->canShow = false;
		
		// si on a des erreurs à afficher
		if (_request ('errors') == 'true') {
			$ppo->profile = CopixSession::get ('profile', 'admin|log|errors');
			$ppo->errors = CopixSession::get ('errors', 'admin|log|errors');
			// ne pas vider la session, sinon, on perd les infos lors des rafraichissements

		// mode ajout
		} else if ($mode == 'add') {
			$ppo->profile = array (
				'name' => '',
			    'enabled' => true,
			    'handle' => array (),
			    'strategy' => 'db',
			    'level' => array (CopixLog::VERBOSE, CopixLog::INFORMATION, CopixLog::NOTICE, CopixLog::WARNING, CopixLog::ERROR, CopixLog::EXCEPTION, CopixLog::FATAL_ERROR)
			);
			
		// mode edition
		} else {
			$ppo->profile = $this->_getProfile ($profile);
		}

		if ($ppo->mode == 'edit') {
			$ppo->isReadable = CopixLog::isReadable ($profile);
		}

		$ppo->configEditor = CopixLog::getConfigEditor ($ppo->profile);
		$ppo->strategies = CopixLog::getStrategies ();
		$ppo->levels = CopixLog::getLevels ();
		$ppo->types = array ();
		foreach (CopixLog::getTypes () as $type) {
			$key = ($type->isFromCopix ()) ? 'Copix' : $type->getModule ();
			if (!array_key_exists ($key, $ppo->types)) {
				$ppo->types[$key] = array ();
			}
			$ppo->types[$key][] = $type;
		}
		$ppo->saved = _request ('saved') == 'true';
		return _arPPO ($ppo, 'log/edit.php');
	}
	
	/**
	 * Effectue l'édition du profil
	 *
	 * @return CopixActionReturn
	 */
	public function processDoEdit () {
		$mode = _request ('mode');
		if ($mode == 'add') {
			ToolsAdmin::setPage (ToolsAdmin::PAGE_LOG_ADD);
		} else {
			ToolsAdmin::setPage (ToolsAdmin::PAGE_LOG_EDIT, array ('profile' => _request ('name')));
		}
		
		// création du profil avec les données saisies
		$profile = array ();
		$profile['name'] = _request ('name');
		$profile['enabled'] = (_request ('enabled') == 1);
		$profile['handle'] = array ();
		foreach (explode ('|', _request ('handle')) as $handle) {
			if (!in_array ($handle, $profile['handle']) && $handle != null) {
				$profile['handle'][] = $handle;
			}
		}
		if (count ($profile['handle']) == 0) {
			$profile['handle'] = 'all';
		}
		$profile['strategy'] = _request ('strategy');
		$profile['level'] = (CopixRequest::exists ('level')) ? _request ('level') : array ();

		// configuration spécifique à la stratégie
		$profile['config'] = array ();
		foreach (CopixRequest::asArray () as $key => $value) {
			if (substr ($key, 0, 7) == 'config_') {
				$name = substr ($key, 7);
				$profile['config'][$name] = $value;
			}
		}

		if ($mode == 'add') {
			$errors = CopixLogConfigFile::add ($profile);
		} else {
			$errors = CopixLogConfigFile::edit ($profile);
		}

		// le profil contient des erreurs
		if ($errors instanceof CopixErrorObject) {
			CopixSession::set ('profile', $profile, 'admin|log|errors');
			CopixSession::set ('errors', $errors->asArray (), 'admin|log|errors');
			$params = ($mode == 'add') ? array ('errors' => 'true') : array ('errors' => 'true', 'profile' => _request ('exName'));
			return _arRedirect (_url ('admin|log|edit', $params));
		}

		// le profil ne contient pas d'erreur
		$params = array (
			'title' => _i18n ('logs.title.confirmSaved'),
			'redirect_url' => _url ('admin|log|', array ('profile' => $profile['name'])),
			'message' => _i18n ('logs.message.confirmSaved', $profile['name']),
			'links' => array (
				_url ('admin|log|edit', array ('profile' => $profile['name'])) => _i18n ('logs.action.backToEdit'),
				_url ('admin|log|', array ('profile' => $profile['name'])) => _i18n ('logs.action.backToList')
			)
		);
		return CopixActionGroup::process ('generictools|messages::getInformation', $params);
	}
		
	/**
	 * Affiche les logs
	 *
	 * @return CopixActionReturn
	 */
	public function processShow () {
		$profile = _request ('profile');
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_LOG_SHOW, array ('profile' => $profile));

		$ppo->profile = $profile;
		$ppo->page = _request ('page', 1);
		$ppo->countPerPage = 20;
		$start = ($ppo->page - 1) * $ppo->countPerPage;
		$ppo->logs = CopixLog::get ($profile, $start, $ppo->countPerPage);
		$ppo->count = CopixLog::count ($profile);
		
		return _arPPO ($ppo, 'log/show.php');
	}
	
	/**
	 * Demande confirmation de suppression du profil
	 *
	 * @return CopixActionReturn
	 */
	public function processDelete () {
		$profile = _request ('profile');
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_LOG_DELETE, array ('profile' => $profile));
		return CopixActionGroup::process ('generictools|Messages::getConfirm', array (
			'message' => _i18n ('logs.deleteContent.text', $profile),
			'confirm' => _url ('admin|log|doDelete', array ('profile' => $profile)),
			'cancel' => _url ('admin|log|')
		));
	}
	
	/**
	 * Effectue la suppression du profil de log
	 *
	 * @return CopixActionReturn
	 */
	public function processDoDelete () {
		$profile = _request ('profile');
		ToolsAdmin::setPage (ToolsAdmin::PAGE_LOG_DELETE, array ('profile' => $profile));
		
		// suppression du contenu du log
		CopixLog::delete ($profile);
		// suppression du profil de log
		CopixLogConfigFile::delete ($profile);
		
		return _arRedirect (_url ('admin|log|'));
	}

	/**
	 * Supprime le contenu d'un profil de log
	 *
	 * @return CopixActionReturn
	 */
	public function processDeleteContent () {
		$profile = _request ('profile');
		ToolsAdmin::setPage (ToolsAdmin::PAGE_LOG_DELETE_CONTENT, array ('profile' => $profile));
		CopixLog::delete ($profile);
		return _arRedirect (_url ('admin|log|show', array ('profile' => $profile)));
	}

	/**
	 * Retourne l'éditeur d'option de la stratégie demandée
	 *
	 * @return CopixActionReturn
	 */
	public function processGetConfigEditor () {
		$profile = array ();
		$profile['name'] = 'getConfigEditor';
		$profile['enabled'] = false;
		$profile['handle'] = array ();
		$profile['strategy'] = _request ('strategy');
		$profile['level'] = array ();
		
		$ppo = new CopixPPO ();
		$ppo->MAIN = CopixLog::getConfigEditor ($profile);
		return _arDirectPPO ($ppo, 'generictools|blank.tpl');
	}
}