<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe qui gère le parsing des module.xml pour enregistrer les URLHandler
 *
 * @package copix
 * @subpackage url
 */
class CopixURLHandlerParser {
	/**
	 * Parse les handlers déclarés
	 *
	 * @param mixed $pXmlNode Node xml des urlHandler
	 * @return array Un tableau d'identifiants de URLHandler
	 */
	public static function parse ($pXmlNode) {
		$toReturn = array ();
		foreach ($pXmlNode as $module => $urlHandlers) {
			foreach ($urlHandlers as $urlHandler) {
				if (!isset ($toReturn[(string)$module])){
					$toReturn[(string)$module] = array ();
				}
				$toReturn[(string)$module][] = (string)$module . '|' . $urlHandler['name'];
			}
		}
		return $toReturn;
	}
}