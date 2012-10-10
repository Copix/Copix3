<?php
/**
 * Gestion des commits
 */
class ActionGroupCommits extends CopixActionGroup {
	/**
	 * Appelée avant toute action
	 *
	 * @param string $pAction
	 */
	protected function _beforeAction ($pAction) {
		CopixPage::add ()->setIsAdmin (true);
		_currentUser ()->assertCredential ('basic:admin');
		$repository = RepositoriesService::get (_request ('repository'));
		_notify ('breadcrumb', array ('path' => array (
			'mysvn|repositories|' => 'Dépots SVN',
			_url ('mysvn|repositories|edit', array ('id' => $repository->getId ())) => $repository->getCaption ())
		));
	}

	/**
	 * Liste des commits
	 */
	public function processDefault () {
		$repository = RepositoriesService::get (_request ('repository'));
		_notify ('breadcrumb', array ('path' => array (_url ('mysvn|commits|', array ('repository' => $repository->getId ())) => 'Commits')));

		$ppo = _ppo (array ('TITLE_PAGE' => 'Commits'));
		$ppo->repository = $repository;
		return _arPPO ($ppo, 'repositories/admin.commits.php');
	}
}