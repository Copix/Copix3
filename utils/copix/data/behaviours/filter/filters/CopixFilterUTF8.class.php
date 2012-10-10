<?php
/**
 * @package copix
 * @subpackage filter
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link  http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Retourne une chaine dont l'encodage sera forcément UTF-8
 * 
 * @package copix
 * @subpackage filter
 */
class CopixFilterUTF8 extends CopixAbstractFilter {
	/**
	 * Récupération d'un boolean a partir d'une chaine
	 * 
	 * @param mixed $pValue la valeur à tester 
	 * @return boolean
	 */
	public function get ($pValue) {
		// si la chaine est trop longue, preg_match vide le buffer de sortie et arrête le traitement sans générer d'erreur
		// en attendant de trouver mieux ...
		$values = array ();
		$length = strlen ($pValue);
		if ($length > 100) {
			$values = array ();
			for ($x = 0; $x < ceil ($length / 100); $x++) {
				$values[] = substr ($pValue, ($x * 100), 100);
			}
		} else {
			$values = array ($pValue);
		}
		foreach ($values as $value) {
			if (!preg_match ('/^(?:[\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3})*$/', $value)) {
				return utf8_encode ($pValue);
			}
		}
		
		return $pValue;
	}
}