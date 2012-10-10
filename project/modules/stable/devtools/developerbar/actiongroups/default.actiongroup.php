<?php
/**
 * Gestion de la barre des développeurs
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Appelée lorsqu'on arrête de déplacer la barre
	 *
	 * @return CopixActionReturn
	 */
	public function processOnDrop () {
		CopixUserPreferences::set ('developerbar|positionX', _request ('x'));
		CopixUserPreferences::set ('developerbar|positionY', _request ('y'));
		return _arNone ();
	}

	/**
	 * Appelée lors de l'affichage d'un contenu
	 *
	 * @return CopixActionReturn
	 */
	public function processOnShow () {
		if (_request ('show') == 'true') {
			DeveloperBar::setShow (_request ('content'));
		} else {
			DeveloperBar::setShow (null);
		}
		return _arNone ();
	}

	/**
	 * Retourne les valeurs de la variable demandée
	 *
	 * @return CopixActionReturn
	 */
	public function processGetValues () {
		CopixRequest::assert ('idBar', 'type');
		$ppo = new CopixPPO ();
		$params = array ('idBar' => _request ('idBar'), 'type' => _request ('type'));
		$ppo->MAIN = CopixZone::process ('developerbar|DeveloperBarValues', $params);
		return _arDirectPPO ($ppo, 'generictools|blank.tpl');
	}
}