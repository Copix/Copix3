<?php
/**
 * @package standard
 * @subpackage admin
 * @copyright CopixTeam
 * @author Steevan BARBOYON
 */

/**
 * Formulaire d'édition des préférences
 * 
 * @package standard
 * @subpackage admin
 */
class ZoneUserPreferences extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn HTML à retourner
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$paramPreferences = $this->getParam ('preferences');
		$onlyDefined = $this->getParam ('onlyDefined', false);
		$ppo = new CopixPPO ();
		$ppo->preferences = array ();

		// préférence d'un user / userhandler en particulier
		$ppo->user = $this->getParam ('user', null);
		$ppo->userhandler = $this->getParam ('userhandler', null);
		// vérification des modules installés
		$ppo->modulePref = array ();
		$modulePref = $this->getParam ('modulePref', array ());
		if (!is_array ($modulePref)) {
			$modulePref = array ($modulePref);
		}
		foreach ($modulePref as $moduleName) {
			if (CopixModule::isEnabled ($moduleName)) {
				$ppo->modulePref[] = $moduleName;
			}
		}
		$ppo->ajaxSave = $this->getParam ('ajaxSave', true);
		$ppo->uniqId = uniqid ('userpref');
		$ppo->showGroups = $this->getParam ('showGroups', true);
		$ppo->tabs = $this->getParam ('tabs', false);
		$ppo->fixed = $this->getParam ('fixed', true);
		$ppo->redirect = $this->getParam ('redirect', CopixURL::getCurrentUrl (true));
		$ppo->confirmMessage = $this->getParam ('confirmMessage', false);
		$ppo->mode = $this->getParam ('mode', 'window');
		$ppo->width = $this->getParam ('width');
		if ($ppo->mode == 'full') {
			$ppo->ajaxSave = false;
		}
		$ppo->caption = $this->getParam ('caption');

		// préférences passées en paramètre
		if (is_array ($paramPreferences)) {
			$groups = CopixUserPreferences::getList (null, $onlyDefined, $ppo->user, $ppo->userhandler);
			foreach ($groups as $group) {
				foreach ($group->getList () as $pref) {
					if (!in_array ($pref->getName (), $paramPreferences)) {
						$group->delete ($pref->getName ());
					} else {
						unset ($paramPreferences[array_search ($pref->getName (), $paramPreferences)]);
					}
				}
			}

			if (count ($paramPreferences) > 0) {
				$groups['default'] = new CopixModulePreferencesGroup ('default', _i18n ('copix:modules.group.default'));
				foreach ($paramPreferences as $name) {
					$newPref = new CopixModulePreference ($name);
					$groups['default']->add ($newPref);
				}
			}

			$ppo->preferences = array ();
			foreach ($groups as $group) {
				if (count ($group->getList ()) > 0) {
					$ppo->preferences[$group->getId ()] = $group;
				}
			}

		// toutes les préférences d'un module, ou toutes les préférences
		} else {
			$ppo->preferences = CopixUserPreferences::getList ($ppo->modulePref, $onlyDefined, $ppo->user, $ppo->userhandler);
		}

		$defaultTab = $this->getParam ('defaultTab');
		if ($defaultTab == null) {
			foreach ($ppo->preferences as $group) {
				if ($defaultTab == null) {
					$defaultTab = $group->getId ();
					break;
				}
			}
		}
		$ppo->defaultTab = $defaultTab;

		$content = $this->_usePPO ($ppo, 'userpreferences/edit.content.php');

		// mode fenêtré
		if ($ppo->mode == 'window') {
			$ppo->content = $content;
			$ppo->img = _resource ($this->getParam ('img', 'img/tools/config.png'));
			$ppo->title = $this->getParam ('title', _i18n ('admin|userpreferences.title.preferences'));
			$ppo->clicker = $this->getParam ('clicker');
			$pToReturn = $this->_usePPO ($ppo, 'userpreferences/edit.window.php');

		// zone normale
		} else {
			$pToReturn = $content;
		}

		return true;
	} 
}