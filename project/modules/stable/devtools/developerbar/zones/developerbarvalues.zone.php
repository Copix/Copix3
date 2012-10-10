<?php
/**
 * Retourne l'html pour une information donnée
 */
class ZoneDeveloperBarValues extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn Contenu à retourner
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$type = $this->getParam ('type');
		$tpl = new CopixTPL ();
		$idBar = $this->getParam ('idBar');
		$tpl->assign ('idBar', $idBar);

		if (($values = $this->getParam ('values')) === null) {
			// le type vars contient get, post, cookie, session et server
			if ($type == 'vars') {
				$values = array ();
				$values['get'] = DeveloperBar::getCacheVar ($idBar, '_developerbar_get');
				$values['post'] = DeveloperBar::getCacheVar ($idBar, '_developerbar_post');
				$values['cookie'] = DeveloperBar::getCacheVar ($idBar, '_developerbar_cookie');
				$values['session'] = DeveloperBar::getCacheVar ($idBar, '_developerbar_session');
				$values['server'] = DeveloperBar::getCacheVar ($idBar, '_developerbar_server');
			} else {
				$values = DeveloperBar::getCacheVar ($idBar, '_developerbar_' . $type);
			}
		}
		// les requêtes ne sont pas formatées dans le cache, pour ne pas utiliser le cpu si on ne les affiche pas
		if ($type == 'querys' && CopixUserPreferences::get ('developerbar|querysFormat')) {
			$values = DeveloperBar::formatQuerys ($values);
		}
		$tpl->assign ('values', $values);
		$pToReturn = $tpl->fetch ('developerbar|zones/developerbar' . $type . '.zone.php');
		return true;
	} 
}