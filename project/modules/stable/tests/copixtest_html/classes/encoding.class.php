<?php
/**
 * @package standard
 * @subpackage copixtest_html
 * @author		Julien Alexandre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

class Encoding {
	/**
	 * Méthode de vérification de l'encodage à partir d'un corps HTTP et de l'en-tête.
	 * Elle retourne le corps HTTP sans modification si la page est déjà encodée
	 * en UTF-8 ou elle encode en UTF-8 le corps et elle le retourne.
	 * La vérification se base sur une recherche de l'encodage UTF-8 dans l'en-tête
	 * HTTP du document.
	 *
	 * @param String $body
	 * @param $array $header
	 * @return String $body
	 */
	public static function checkEncoding ($body , $header) {	
			$find = new stdClass ();
			$find = false;
		foreach ($header as $value) {
			if (str_replace("UTF-8", " ", $value) !== $value) {
				$find = true;
				}
		}
			if ($find === true) {
				return $body;
			} else {
				return utf8_encode ($body);
			}
	}
}
?>