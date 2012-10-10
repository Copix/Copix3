<?php
class ActionGroupDefault extends CopixActionGroup {
	
	/**
	 * Page d'accueil du module
	 * 
	 */
	public function processDefault() {
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('repository.title.welcome');
		return _arPPO ($ppo, 'welcome.php');
	}
}
?>