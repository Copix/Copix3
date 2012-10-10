<?php
/**
 * Administration des commentaires
 */
class ActionGroupComments extends CopixActionGroup {
	/**
	 * Appelée avant toute action
	 * 
	 * @param string $pAction Nom de l'action
	 */
	protected function _beforeAction ($pAction) {
		CopixPage::add ()->setIsAdmin (true);
		_notify ('breadcrumb', array ('path' => array ('comments|comments|' => 'Commentaires')));
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
		$page->setId ('comments|comments|' . $pId);
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
		$ppo = $this->_setPage ('default', _i18n ('comments|comments.admin.list.title'));
		$ppo->highlight = _request ('highlight');
		$ppo->countPerPage = 20;
		$ppo->page = _request ('page', 1);
		$ppo->elements = CommentsService::getList (null, ($ppo->page - 1) * $ppo->countPerPage, $ppo->countPerPage);
		$ppo->countElements = CommentsService::count ();
		return _arPPO ($ppo, 'comments/admin.list.php');
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
			$ppo->element = CopixSession::get (_request ('sessionObject') . '_element', 'comments|admin');
			$ppo->errors = CopixSession::get (_request ('sessionObject') . '_errors', 'comments|admin');
			$id = $ppo->element->getId ();
		}

		// ajout d'un élément
		if ($id == null) {
			$breadcrumb = array ('comments|comments|edit' => _i18n ('comments|comments.admin.add.breadcrumb'));
			$ppo->mode = 'add';
			$ppo->TITLE_PAGE = _i18n ('comments|comments.admin.add.title');
			if ($ppo->element == null) {
				$ppo->element = CommentsService::create ();
			}

		// modification d'un élément
		} else {
			$breadcrumb = array (_url ('comments|comments|edit', array ('id' => $id)) => _i18n ('comments|comments.admin.edit.breadcrumb'));
			$ppo->mode = 'edit';
			$ppo->TITLE_PAGE = _i18n ('comments|comments.admin.edit.title');
			if ($ppo->element == null) {
				$ppo->element = CommentsService::get ($id);
			}
		}

		$this->_setPage ($ppo->mode, $ppo->TITLE_PAGE, $breadcrumb);
		return _arPPO ($ppo, 'comments/admin.edit.php');
	}

	/**
	 * Effectue l'édition d'un élément
	 * 
	 * @return CopixActionReturn
	 */
	public function processDoEdit () {
		$mode = _request ('mode');
		$this->_setPage ('do' . strtoupper ($mode), _i18n ('comments|comments.admin.doEdit.title'));
		$element = ($mode == 'add') ? CommentsService::create () : CommentsService::get (_request ('id'));

		$element->setAuthor (_request ('author'));
		$element->setWebsite (_request ('website'));
		$element->setEmail (_request ('email'));
		$element->setComment (_request ('value'));

		try {
			if ($mode == 'add') {
				CommentsService::insert ($element);
			} else {
				CommentsService::update ($element);
			}
		} catch (CommentsException $e) {
			$sessionId = uniqid ('element');
			CopixSession::set ($sessionId . '_element', $element, 'comments|admin');
			CopixSession::set ($sessionId . '_errors', $e->getErrors (), 'comments|admin');
			return _arRedirect (_url ('comments|comments|edit', array ('errors' => 'true', 'sessionObject' => $sessionId)));
		}

		$params = array (
			'title' => _i18n ('comments|comments.admin.doEdit.confirmTitle'),
			'redirect_url' => _url ('comments|comments|', array ('highlight' => $element->getId ())),
			'message' => _i18n ('comments|comments.admin.doEdit.confirmMessage', $element->getAuthor ()),
			'links' => array (
				_url ('comments|comments|edit', array ('id' => $element->getId ())) => _i18n ('comments|comments.admin.doEdit.linkEdit'),
				_url ('comments|comments|', array ('highlight' => $element->getId ())) => _i18n ('comments|comments.admin.doEdit.linkList'),
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
		$this->_setPage ('delete', _i18n ('comments|comments.admin.delete.title'));
		CopixRequest::assert ('id');

		$element = CommentsService::get (_request ('id'));
		$params = array (
			'message' => _i18n ('comments|comments.admin.delete.confirmMessage', $element->getAuthor ()),
			'confirm' => _url ('comments|comments|doDelete', array ('id' => $element->getId ())),
			'cancel' => _url ('comments|comments|')
		);
		return CopixActionGroup::process ('generictools|Messages::getConfirm', $params);
	}

	/**
	 * Effectue la suppression d'un élément
	 * 
	 * @return CopixActionReturn
	 */
	public function processDoDelete () {
		$this->_setPage ('doDelete', _i18n ('comments|comments.admin.doDelete.title'));
		CopixRequest::assert ('id');

		$element = CommentsService::get (_request ('id'));
		CommentsService::delete ($element->getId ());

		$params = array (
			'title' => _i18n ('comments|comments.admin.doDelete.confirmTitle'),
			'message' => _i18n ('comments|comments.admin.doDelete.confirmMessage', $element->getAuthor ()),
			'redirect_url' => _url ('comments|comments|'),
			'links' => array (_url ('comments|comments|') => _i18n ('comments|comments.admin.doDelete.linkList'))
		);
		return CopixActionGroup::process ('generictools|messages::getInformation', $params);
	}
}