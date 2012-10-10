<?php
/**
 * @package devtools
 * @subpackage developerbar
 * @author Nicolas BASTIEN, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Plugin de la barre de développement
 *
 * @package default
 * @subpackage developerbar
 */
class PluginDeveloperBar extends CopixPlugin implements ICopixBeforeSessionStartPlugin, ICopixBeforeProcessPlugin, ICopixBeforeDisplayPlugin, ICopixAfterProcessPlugin {
	/**
	 * Retourne la description
	 *
	 * @return string
	 */
	public function getDescription () {
		return 'Barre d\'outils pour les développeurs';
	}

	/**
	 * Retourne le libellé
	 *
	 * @return string
	 */
	public function getCaption () {
		return 'Barre d\'outils pour les développeurs';
	}

	/**
	 * Appelée avant que la session ne soit commencée
	 */
	public function beforeSessionStart () {
		DeveloperBar::startGlobalTimer ();
		DeveloperBar::startLog ();
	}
	
	/**
	 * Appelée avant l'exécution de l'action demandée
	 * 
	 * @param string $pAction Nom de l'action
	 */
	public function beforeProcess (&$pAction) {
		DeveloperBar::startActionTimer ();
	}

	/**
	 * Appelée après l'exécution de l'action
	 * 
	 * @param CopixActionReturn $pActionReturn Retour de l'action
	 */
	public function afterProcess ($pActionReturn) {
		DeveloperBar::endActionTimer ();
	}
	
	/**
	 * Appelée avant l'affichage de l'HTML
	 *
	 * @param string $pContent Contenu à renvoyer au navigateur
	 */
	public function beforeDisplay (&$pContent) {
		$credential = CopixConfig::get ('developerbar|credentialShow');
		if ($credential == null || ($credential != null && _currentUser ()->testCredential ($credential))) {
			DeveloperBar::endGlobalTimer ();
			if (!CopixRequest::isAJAX ()) {
				$html = DeveloperBar::getHTML ();
				$pContent = str_replace ('</body>', $html . '</body>', $pContent);
			} else {
				/*DeveloperBar::getHTML (false);
				header ('X-Copix-DeveloperBar-Id: ' . DeveloperBar::getId ());
				if (CopixUserPreferences::get ('developerbar|timersEnabled')) {
					header ('X-Copix-DeveloperBar-Timers-Global: ' . DeveloperBar::getGlobalTime ());
					header ('X-Copix-DeveloperBar-Timers-Copix: ' . DeveloperBar::getCopixTime ());
					header ('X-Copix-DeveloperBar-Timers-Action: ' . DeveloperBar::getActionTime ());
				}*/
			}
		}
    }	
}