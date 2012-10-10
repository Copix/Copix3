<?php
/**
 * Gestion du browser de répertoires et fichiers
 */
class ActionGroupBrowser extends CopixActionGroup {
	/**
	 * Appelée avant toute action
	 *
	 * @param string $pAction Nom de l'action
	 */
	protected function _beforeAction ($pAction) {
		_currentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Retourne les répertoires et fichiers du path demandé
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$ppo = _ppo ();
		$ppo->MAIN = CopixZone::process ('backup|browser', array ('path' => _request ('path')));
		return _arDirectPPO ($ppo, 'generictools|blanknohead.tpl');
	}
}