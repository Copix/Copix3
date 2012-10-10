<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Cro�s G�rald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe qui g�re le parsing des module.xml pour enregistrer les URLHandler
 *
 * @package copix
 * @subpackage url
 */
class CopixURLHandlerParser {
	/**
	 * Parse les handlers d�clar�s
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