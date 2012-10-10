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
 * Retourne un montant formatté
 * 
 * @package copix
 * @subpackage filter
 */
class CopixFilterMoney extends CopixAbstractFilter {
	const EUROS = 1;
	const DOLLARS = 2;

	/**
	 * Retourne le montant formatté
	 *
	 * @param string $pValue
	 * @return string
	 */
	public function get ($pValue) {
		$toReturn = null;
		$kind = $this->getParam ('kind', self::EUROS);
		$after = null;
		$thousands = ' ';
		$dec = ',';
		switch ($kind) {
			case self::DOLLARS :
				$toReturn = '$';
				$dec = '.';
				break;
			case self::EUROS :
				$after = ' €';
		}
		$value = (float)str_replace (',', '.', $pValue);
		$toReturn .= number_format ($value, $this->getParam ('decimals', 2), $dec, $thousands) . $after;
		return $toReturn;
	}	
}