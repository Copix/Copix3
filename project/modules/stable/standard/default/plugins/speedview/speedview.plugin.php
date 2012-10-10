<?php
/**
 * @package standard
 * @subpackage default
 * @author Croes Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Affiche le temps de calcul de la page
 *
 * @package standard
 * @subpackage default
 */
class PluginSpeedView extends CopixPlugin implements ICopixBeforeSessionStartPlugin, ICopixBeforeDisplayPlugin {
	/**
	 * Retourne le libellé
	 *
	 * @return string
	 */
	public function getCaption () {
		return 'Affiche le temps de calcul de la page';
	}

	/**
	 * Retourne la description
	 *
	 * @return string
	 */
	public function getDescription () {
		return 'Permet de calculer le temps de génération de la page. Diverses façons de retourner ce calcul sont disponibles :<ul><li>Affichage directement en bas de page</li><li>Mise en commentaire après &lt;head&gt;</li><li>Log de type "speedview", et de niveau INFORMATION</li></ul>';
	}

	/**
	 * Timer utilisé pour le calcul des temps
	 * 
	 * @var int
	 */
	private $_timer = 0;

	/**
	 * Indique si l'on souhaite calculer les temps
	 *
	 * @var boolean
	 */
	private $_speedprocess = false;

	/**
	 * Démarre le compteur de temps
	 */
	public function beforeSessionStart () {
		$this->_timer = new CopixTimer ();
		$this->_timer->start ();
	}

	/**
	 * Affiche le temps d ecalcul
	 * 
	 * @param string $pContent Contenu à afficher
	 */
	public function beforeDisplay (&$pContent) {
		$elapsedTime = $this->_timer->stop ();
		switch ($this->config->trigger) {
			case 'url' :
				if (CopixRequest::get ('SpeedView') == 'show') {
					$this->_speedprocess = true;
				}
				break;
			case 'display' :
				$this->_speedprocess = true;
				break;
		}

		if ($this->_speedprocess) {
			switch ($this->config->target) {
				case 'comment' :
					$pContent = str_replace ('<head>', '<head><!-- '.$elapsedTime.' -->', $pContent);
					break;
				case 'display' :
					$pContent = str_replace ('</body>', $elapsedTime.'</body>', $pContent);
					break;
				case 'log' :
					_log ($elapsedTime, 'speedview', CopixLog::INFORMATION);
					break;
			}
		}
	}
}