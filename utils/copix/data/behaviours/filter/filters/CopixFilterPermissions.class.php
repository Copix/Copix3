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
 * Retourne un droit sous la forme -rw-r--r--
 * 
 * @package copix
 * @subpackage filter
 */
class CopixFilterPermissions extends CopixAbstractFilter {
	/**
	 * Retourne un droit sous la forme -rw-r--r--
	 *
	 * @param string $pValue
	 * @return string
	 */
	public function get ($pValue) {
		// base du code depuis php.net

		// Socket
		if (($pValue & 0xC000) == 0xC000) {
			$toReturn = 's';
		// Lien symbolique
		} else if (($pValue & 0xA000) == 0xA000) {
			$toReturn = 'l';
		// Régulier
		} else if (($pValue & 0x8000) == 0x8000) {
			$toReturn = '-';
		// Block special
		} else if (($pValue & 0x6000) == 0x6000) {
			$toReturn = 'b';
		// Dossier
		} else if (($pValue & 0x4000) == 0x4000) {
			$toReturn = 'd';
		// Caractère spécial
		} else if (($pValue & 0x2000) == 0x2000) {
			$toReturn = 'c';
		// pipe FIFO
		} else if (($pValue & 0x1000) == 0x1000) {
			$toReturn = 'p';
		// Inconnu
		} else {
			$toReturn = 'u';
		}

		// Autres
		$toReturn .= (($pValue & 0x0100) ? 'r' : '-');
		$toReturn .= (($pValue & 0x0080) ? 'w' : '-');
		$toReturn .= (($pValue & 0x0040) ? (($pValue & 0x0800) ? 's' : 'x' ) : (($pValue & 0x0800) ? 'S' : '-'));
		// Groupe
		$toReturn .= (($pValue & 0x0020) ? 'r' : '-');
		$toReturn .= (($pValue & 0x0010) ? 'w' : '-');
		$toReturn .= (($pValue & 0x0008) ? (($pValue & 0x0400) ? 's' : 'x' ) : (($pValue & 0x0400) ? 'S' : '-'));
		// Tout le monde
		$toReturn .= (($pValue & 0x0004) ? 'r' : '-');
		$toReturn .= (($pValue & 0x0002) ? 'w' : '-');
		$toReturn .= (($pValue & 0x0001) ? (($pValue & 0x0200) ? 't' : 'x' ) : (($pValue & 0x0200) ? 'T' : '-'));

		return $toReturn;
	}	
}