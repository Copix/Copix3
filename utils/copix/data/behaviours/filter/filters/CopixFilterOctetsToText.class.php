<?php
/**
 * @package copix
 * @subpackage filter
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Filtres qui transforme une donnée exprimée en octets en donnée exprimée en Ko, Mo, etc
 *
 * @package copix
 * @subpackage filter
 */
class CopixFilterOctetsToText extends CopixAbstractFilter {
	/**
	 * Retourne la valeur "chaine" de la taille en octets
	 *
	 * @param mixed $pValue Taille en octets
	 * @return string
	 */
	public function get ($pValue) {
        $b = (int)$pValue;
        $s = array ('o', 'Ko', 'Mo', 'Go', 'To');
        if ($b < 0) {
            return '0 ' . $s[0];
        }
        $e = (int)(log ($b, 1024));
        return number_format ($b / pow (1024, $e), 2, ',', '.') . ' ' . $s[$e];
	}
}