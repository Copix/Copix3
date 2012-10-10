<?php
class HeadingActionData extends CopixLogData {
	/**
	 * Actions
	 *
	 * @var CopixLogData[]
	 */
	private $_actions = array ();

	/**
	 * Identifiant de la page
	 *
	 * @var string
	 */
	private $_pageId = null;

	/**
	 * Définit l'identifiant de la page
	 *
	 * @param string $pId
	 */
	public function setPageId ($pId) {
		$this->_pageId = $pId;
	}

	/**
	 * Retourne l'identifiant de la page
	 *
	 * @return string
	 */
	public function getPageId () {
		return $this->_pageId;
	}

	/**
	 * Ajoute une action
	 *
	 * @param CopixLogData $pAction
	 */
	public function addAction (CopixLogData $pAction) {
		$this->_actions[] = $pAction;
	}

	/**
	 * Retourne les actions
	 *
	 * @return CopixLogData[]
	 */
	public function getActions () {
		return $this->_actions;
	}
}