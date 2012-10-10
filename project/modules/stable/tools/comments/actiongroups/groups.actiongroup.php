<?php
/**
 * Administration des groupes de commentaire
 */
class ActionGroupGroups extends CopixActionGroup {
	/**
	 * Appelée avant toute action
	 * 
	 * @param string $pAction Nom de l'action
	 */
	protected function _beforeAction ($pAction) {
		CopixPage::add ()->setIsAdmin (true);
		_notify ('breadcrumb', array ('path' => array ('comments|groups|' => 'Groupes de commentaires')));
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
		$page->setId ('comments|groups|' . $pId);
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
		$ppo = $this->_setPage ('default', _i18n ('comments|commentsgroups.admin.list.title'));
		$ppo->highlight = _request ('highlight');
		$ppo->countPerPage = 20;
		$ppo->page = _request ('page', 1);
		$ppo->elements = CommentsGroupsService::getList (($ppo->page - 1) * $ppo->countPerPage, $ppo->countPerPage);
		$ppo->countElements = CommentsGroupsService::count ();
		return _arPPO ($ppo, 'groups/admin.list.php');
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
			$ppo->element = CopixSession::get (_request ('sessionObject') . '_element', 'comments|groups');
			$ppo->errors = CopixSession::get (_request ('sessionObject') . '_errors', 'comments|groups');
			$id = $ppo->element->getId ();
		}

		// ajout d'un élément
		if ($id == null || !CommentsGroupsService::exists ($id)) {
			$breadcrumb = array ('comments|groups|edit' => _i18n ('comments|commentsgroups.admin.add.breadcrumb'));
			$ppo->mode = 'add';
			$ppo->TITLE_PAGE = _i18n ('comments|commentsgroups.admin.add.title');
			if ($ppo->element == null) {
				$ppo->element = CommentsGroupsService::create ();
			}

		// modification d'un élément
		} else {
			$breadcrumb = array (_url ('comments|groups|edit', array ('id' => $id)) => _i18n ('comments|commentsgroups.admin.edit.breadcrumb'));
			$ppo->mode = 'edit';
			$ppo->TITLE_PAGE = _i18n ('comments|commentsgroups.admin.edit.title');
			if ($ppo->element == null) {
				$ppo->element = CommentsGroupsService::get ($id);
			}
		}

		$this->_setPage ($ppo->mode, $ppo->TITLE_PAGE, $breadcrumb);
		return _arPPO ($ppo, 'groups/admin.edit.php');
	}

	/**
	 * Effectue l'édition d'un élément
	 * 
	 * @return CopixActionReturn
	 */
	public function processDoEdit () {
		$mode = _request ('mode');
		$this->_setPage ('do' . strtoupper ($mode), _i18n ('comments|commentsgroups.admin.doEdit.title'));
		$element = ($mode == 'add') ? CommentsGroupsService::create () : CommentsGroupsService::get (_request ('id'));

		if ($mode == 'add') {
			$element->setId (_request ('id'));
		}
		$element->setCaption (_request ('caption'));
		$element->setIsAuthorRequired (_request ('authorRequired'));
		$element->setIsWebsiteRequired (_request ('websiteRequired'));
		$element->setIsEmailRequired (_request ('emailRequired'));

		try {
			if ($mode == 'add') {
				CommentsGroupsService::insert ($element);
			} else {
				CommentsGroupsService::update ($element);
			}
		} catch (CommentsGroupsException $e) {
			if ($mode == 'add' && $e->getCode () == CommentsGroupsException::ID_EXISTS) {
				$element->setId (null);
			}
			$sessionId = uniqid ('element');
			CopixSession::set ($sessionId . '_element', $element, 'comments|groups');
			CopixSession::set ($sessionId . '_errors', $e->getErrors (), 'comments|groups');
			return _arRedirect (_url ('comments|groups|edit', array ('errors' => 'true', 'sessionObject' => $sessionId)));
		}

		$params = array (
			'title' => _i18n ('comments|commentsgroups.admin.doEdit.confirmTitle'),
			'redirect_url' => _url ('comments|groups|', array ('highlight' => $element->getId ())),
			'message' => _i18n ('comments|commentsgroups.admin.doEdit.confirmMessage', $element->getCaption ()),
			'links' => array (
				_url ('comments|groups|edit', array ('id' => $element->getId ())) => _i18n ('comments|commentsgroups.admin.doEdit.linkEdit'),
				_url ('comments|groups|', array ('highlight' => $element->getId ())) => _i18n ('comments|commentsgroups.admin.doEdit.linkList'),
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
		$this->_setPage ('delete', _i18n ('comments|commentsgroups.admin.delete.title'));
		CopixRequest::assert ('id');

		$element = CommentsGroupsService::get (_request ('id'));
		$params = array (
			'message' => _i18n ('comments|commentsgroups.admin.delete.confirmMessage', $element->getCaption ()),
			'confirm' => _url ('comments|groups|doDelete', array ('id' => $element->getId ())),
			'cancel' => _url ('comments|groups|')
		);
		return CopixActionGroup::process ('generictools|Messages::getConfirm', $params);
	}

	/**
	 * Effectue la suppression d'un élément
	 * 
	 * @return CopixActionReturn
	 */
	public function processDoDelete () {
		$this->_setPage ('doDelete', _i18n ('comments|commentsgroups.admin.doDelete.title'));
		CopixRequest::assert ('id');

		$element = CommentsGroupsService::get (_request ('id'));
		CommentsGroupsService::delete ($element->getId ());

		$params = array (
			'title' => _i18n ('comments|commentsgroups.admin.doDelete.confirmTitle'),
			'message' => _i18n ('comments|commentsgroups.admin.doDelete.confirmMessage', $element->getCaption ()),
			'redirect_url' => _url ('comments|groups|'),
			'links' => array (_url ('comments|groups|') => _i18n ('comments|commentsgroups.admin.doDelete.linkList'))
		);
		return CopixActionGroup::process ('generictools|messages::getInformation', $params);
	}
}