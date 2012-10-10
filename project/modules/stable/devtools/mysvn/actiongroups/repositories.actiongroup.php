<?php
/**
 * Administration des dépots
 */
class ActionGroupRepositories extends CopixActionGroup {
	/**
	 * Appelée avant toute action
	 * 
	 * @param string $pAction Nom de l'action
	 */
	protected function _beforeAction ($pAction) {
		CopixPage::add ()->setIsAdmin (true);
		_notify ('breadcrumb', array ('path' => array ('mysvn|repositories|' => 'Dépots SVN')));
		_currentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Définit des informations sur la page
	 * 
	 * @param string $pId Identifiant de la page
	 * @param string $pTitle Titre
	 * @param array $pBreadcrumb Fil d'ariane
	 * @return CopixPPO
	 */
	private function _setPage ($pId, $pTitle, $pBreadcrumb = array ()) {
		$page = CopixPage::get ();
		$page->setId ('mysvn|repositories|' . $pId);
		$page->setTitle ($pTitle);
		if (count ($pBreadcrumb) > 0) {
			_notify ('breadcrumb', array ('path' => $pBreadcrumb));
		}
		return new CopixPPO (array ('TITLE_PAGE' => $pTitle));
	}

	/**
	 * Liste des éléments
	 * 
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$ppo = $this->_setPage ('default', _i18n ('mysvn|repositories.admin.list.title'));
		$ppo->highlight = _request ('highlight');
		$ppo->countPerPage = 20;
		$ppo->page = _request ('page', 1);
		$ppo->elements = RepositoriesService::getList (($ppo->page - 1) * $ppo->countPerPage, $ppo->countPerPage);
		$ppo->countElements = RepositoriesService::count ();
		return _arPPO ($ppo, 'repositories/admin.list.php');
	}

	/**
	 * Edition d'un élément
	 * 
	 * @return CopixActionReturn
	 */
	public function processEdit () {
		$id = _request ('id', null);
		$ppo = new CopixPPO ();

		// objet passé via la session pour affichage d'erreurs
		if (_request ('errors') == 'true') {
			$ppo->element = CopixSession::get (_request ('sessionObject') . '_element', 'mysvn|repositories');
			$ppo->errors = CopixSession::get (_request ('sessionObject') . '_errors', 'mysvn|repositories');
			$id = $ppo->element->getId ();
		}

		// ajout d'un élément
		if ($id == null) {
			$breadcrumb = array ('mysvn|repositories|edit' => _i18n ('mysvn|repositories.admin.add.breadcrumb'));
			$ppo->mode = 'add';
			$ppo->TITLE_PAGE = _i18n ('mysvn|repositories.admin.add.title');
			if ($ppo->element == null) {
				$ppo->element = RepositoriesService::create ();
			}

		// modification d'un élément
		} else {
			$breadcrumb = array (_url ('mysvn|repositories|edit', array ('id' => $id)) => _i18n ('mysvn|repositories.admin.edit.breadcrumb'));
			$ppo->mode = 'edit';
			$ppo->TITLE_PAGE = _i18n ('mysvn|repositories.admin.edit.title');
			if ($ppo->element == null) {
				$ppo->element = RepositoriesService::get ($id);
			}
		}

		$this->_setPage ($ppo->mode, $ppo->TITLE_PAGE, $breadcrumb);
		return _arPPO ($ppo, 'repositories/admin.edit.php');
	}

	/**
	 * Effectue l'édition d'un élément
	 * 
	 * @return CopixActionReturn
	 */
	public function processDoEdit () {
		$mode = _request ('mode');
		$this->_setPage ('do' . strtoupper ($mode), _i18n ('mysvn|repositories.admin.doEdit.title'));
		$element = ($mode == 'add') ? RepositoriesService::create () : RepositoriesService::get (_request ('id'));

		$element->setCaption (_request ('caption'));
		$element->setUrl (_request ('url'));

		try {
			if ($mode == 'add') {
				RepositoriesService::insert ($element);
			} else {
				RepositoriesService::update ($element);
			}
		} catch (RepositoriesException $e) {
			$sessionId = uniqid ('element');
			CopixSession::set ($sessionId . '_element', $element, 'mysvn|repositories');
			CopixSession::set ($sessionId . '_errors', $e->getErrors (), 'mysvn|repositories');
			return _arRedirect (_url ('mysvn|repositories|edit', array ('errors' => 'true', 'sessionObject' => $sessionId)));
		}

		$params = array (
			'title' => _i18n ('mysvn|repositories.admin.doEdit.confirmTitle'),
			'redirect_url' => _url ('mysvn|repositories|', array ('highlight' => $element->getId ())),
			'message' => _i18n ('mysvn|repositories.admin.doEdit.confirmMessage', $element->getCaption ()),
			'links' => array (
				_url ('mysvn|repositories|edit', array ('id' => $element->getId ())) => _i18n ('mysvn|repositories.admin.doEdit.linkEdit'),
				_url ('mysvn|repositories|', array ('highlight' => $element->getId ())) => _i18n ('mysvn|repositories.admin.doEdit.linkList'),
			)
		);
		return CopixActionGroup::process ('generictools|messages::getInformation', $params);
	}

	/**
	 * Demande confirmation de suppression d'un élément
	 * 
	 * @return CopixActionReturn
	 */
	public function processDelete () {
		$this->_setPage ('delete', _i18n ('mysvn|repositories.admin.delete.title'));
		CopixRequest::assert ('id');

		$element = RepositoriesService::get (_request ('id'));
		$params = array (
			'message' => _i18n ('mysvn|repositories.admin.delete.confirmMessage', $element->getCaption ()),
			'confirm' => _url ('mysvn|repositories|doDelete', array ('id' => $element->getId ())),
			'cancel' => _url ('mysvn|repositories|')
		);
		return CopixActionGroup::process ('generictools|Messages::getConfirm', $params);
	}

	/**
	 * Effectue la suppression d'un élément
	 * 
	 * @return CopixActionReturn
	 */
	public function processDoDelete () {
		$this->_setPage ('doDelete', _i18n ('mysvn|repositories.admin.doDelete.title'));
		CopixRequest::assert ('id');

		$element = RepositoriesService::get (_request ('id'));
		RepositoriesService::delete ($element->getId ());

		$params = array (
			'title' => _i18n ('mysvn|repositories.admin.doDelete.confirmTitle'),
			'message' => _i18n ('mysvn|repositories.admin.doDelete.confirmMessage', $element->getCaption ()),
			'redirect_url' => _url ('mysvn|repositories|'),
			'links' => array (_url ('mysvn|repositories|') => _i18n ('mysvn|repositories.admin.doDelete.linkList'))
		);
		return CopixActionGroup::process ('generictools|messages::getInformation', $params);
	}
}