<?php
/**
 * @package standard
 * @subpackage default
 * @author Croës Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Change le template principal si on a passé toPrint=1 dans l'url (configurable)
 *
 * @package standard
 * @subpackage default
 */
class PluginPrint extends CopixPlugin {
	/**
	 * Retourne le libellé
	 *
	 * @return string
	 */
	public function getCaption () {
		return 'Change le main.php si on passe toPrint=1';
	}

	/**
	 * Retourne la description
	 *
	 * @return string
	 */
	public function getDescription () {
		return 'Change le template principal en "default|main.print.tpl" si on passe toPrint=1 dans l\'adresse.';
	}

	/**
	 * Change le template principal si on a passé toPrint=1 dans l'url (configurable)
	 */
	public function beforeSessionStart () {
		if ($this->_shouldPrint ()) {
			CopixConfig::instance ()->mainTemplate = $this->config->templatePrint;
		}
	}
	
	/**
	 * Indique si les paramètres passés demandent à imprimer
	 *
	 * @return bool
	 */
	private function _shouldPrint () {
		foreach ($this->config->runPrintUrl as $name => $value) {
			if (_request ($name) != $value) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Retourne l'adresse de la page courante, avec les paramètres indiquant qu'on veut imprimer
	 *
	 * @return string
	 */
	public function getPrintableUrl () {
		return _url ('#', $this->config->runPrintUrl);
	}
}