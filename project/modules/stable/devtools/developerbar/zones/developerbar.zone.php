<?php
/**
 * Retourne la barre d'informations
 */
class ZoneDeveloperBar extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn Contenu à retourner
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn){
		$tpl = new CopixTPL ();

		// on prend tous les paramètres passés, et on les donne au template, histoire de pas avoir 20 lignes inutiles
		foreach ($this->getParams () as $name => $value) {
			$tpl->assign ($name, $value);
		}
		
		// configurations diverses
		$tpl->assign ('idBar', DeveloperBar::getId ());
		$tpl->assign ('vars_enabled', CopixUserPreferences::get ('developerbar|varsEnabled'));
		$tpl->assign ('timers_enabled', CopixUserPreferences::get ('developerbar|timersEnabled'));
		$tpl->assign ('memory_enabled', CopixUserPreferences::get ('developerbar|memoryEnabled'));
		$tpl->assign ('querys_enabled', CopixUserPreferences::get ('developerbar|querysEnabled'));
		$tpl->assign ('logs_enabled', CopixUserPreferences::get ('developerbar|logsEnabled'));
		$tpl->assign ('errors_enabled', CopixUserPreferences::get ('developerbar|errorsEnabled'));

		// position de la barre
		$tpl->assign ('positionX', CopixUserPreferences::get ('developerbar|positionX', 5));
		$tpl->assign ('positionY', CopixUserPreferences::get ('developerbar|positionY', 5));

		$pToReturn = $tpl->fetch ('developerbar|developerbar.php');
		return true;
	} 
}