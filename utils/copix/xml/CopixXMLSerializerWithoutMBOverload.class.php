<?php
/**
 * @package   copix
 * @subpackage xml
 * @author    Guillaume Perréal
 * @copyright CopixTeam
 * @link      http://copix.org
 * @license   http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Implémentation de AbstractCopixXMLSerializer fonctionnant sans la surcharge des fonctions standard par mbstring.
 *
 * @package		copix
 * @subpackage	core
 */
class CopixXMLSerializerWithoutMBOverload extends CopixAbstractXMLSerializer {
	/**
	 * Retourne la taille de la chaîne en octets.
	 *
	 * @param string $string La chaîne en question.
	 * @return int Taille de la chaîne en octets.
	 */
	public function strlen($string) {
		return strlen($string);
	}
	
	/**
	 * Retourne une portion d'une chaîne.
	 *
	 * @param string $string Chaîne.
	 * @param int $begin Position du début en octets.
	 * @param int $length Taille de la sous-chaîne en octets.
	 */
	public function substr($string, $begin, $length = NULL) {
		if($length === NULL) {
			$length = $this->strlen($string) - $begin;
		}
		return substr($string, $begin, $length);
	}

	/**
	 * Recherche une chaîne dans une autre.
	 * 
	 * @param string $string La chaîne à observer.
	 * @param string $pattern La sous-chaîne à retrouver.
	 * @pram int $offset Décalage initial.
	 * @return int Position de la sous-chaîne dans la chaîne, en octets, ou FALSE si elle n'a pas été trouvée.
	 */
	public function strpos($string, $pattern, $offset = 0) {
		return strpos($string, $pattern, $offset);
	}
}