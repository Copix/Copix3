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
 * Supprime les slashes ajoutés par PHP si magic_quotes est activé.
 * Il est recommandé pour plus de performances de ne pas enregistrer ce plugin et de désactiver l'option magic_quotes dans le PHP.ini
 *
 * @package standard
 * @subpackage default
 */
class PluginMagicQuotes extends CopixPlugin implements ICopixBeforeSessionStartPlugin {
	/**
	 * Retourne le libellé
	 *
	 * @return string
	 */
	public function getCaption () {
		return 'Gestion des conflits avec magic_quotes';
	}

	/**
	 * Retourne la description
	 *
	 * @return string
	 */
	public function getDescription () {
		return 'Apelle stripSlashes sur tous les paramètres si magic_quotes est activé.';
	}

	/**
	 * Passe tous les paramètres de CopixRequest dans stripSlashes si magic_quotes est activé
	 */
	public function beforeSessionStart () {
		if (get_magic_quotes_gpc ()) {
			foreach (CopixRequest::asArray () as $key => $elem) {
				CopixRequest::set ($key, $this->_stripSlashes ($elem));
			}
		}
	}

	/**
	 * Passe toutes les valeur dans stripSlashes
	 * 
	 * @param string/array $string
	 * @return string/array
	 */
	private function _stripSlashes ($string) {
		if (is_array ($string)) {
			$toReturn = array ();
			// c'est un tableau, on traite un à un tout les elements du tableau
			foreach ($string as $key => $elem) {
				$toReturn[$key] = $this->_stripSlashes ($elem);
			}
			return $toReturn;
		} else {
			return stripSlashes ($string);
		}
	}
}