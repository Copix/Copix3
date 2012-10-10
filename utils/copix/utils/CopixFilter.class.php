<?php
/**
* @package		copix
* @subpackage	core
* @author		Croës Gérald
* @copyright	CopixTeam
* @link 		http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Filtres pour récupérer des données sous une certaine forme
 * @package		copix
 * @subpackage	utils
 */
class CopixFilter {
	/**
	 * Récupération d'un entier à partir de la variable
	 * @param 	mixed	$pInt	la variable à récupérer sous la forme d'un entier
	 * @return int
	 */
	public static function getInt ($pInt){
        return intval (self::getNumeric ($pInt, true));		
	}
	
	/**
	 * Récupération d'un numérique à partir de la variable
	 * @param 	mixed	$pNumeric	la variable à récupérer sous la forme d'un numérique
	 * @param	boolean	$pWithComma	si l'on souhaite inclure les virgules et points dans l'élément
	 * @return numeric
	 */
	public static function getNumeric ($pNumeric, $pWithComma = false){
		if ($pWithComma){
           return preg_replace('/[^\d.]/', '', str_replace (',', '.', $pNumeric));
		}else{
           return preg_replace('/[^\d]/', '', $pNumeric);
		}
	}
	
	/**
	 * Récupération des caractères d'une chaine
	 * @param	string	$pAlpha	chaine de caractère à traiter
	 * @return string
	 */
	public static function getAlpha ($pAlpha, $pWithSpaces=true){
		if ($pWithSpaces){
			return preg_replace('/[^a-zA-ZàâäéèêëîïÿôöùüçñÀÂÄÉÈÊËÎÏŸÔÖÙÜÇÑ ]/', '', $pAlpha);
		}else{
			return preg_replace('/[^a-zA-ZàâäéèêëîïÿôöùüçñÀÂÄÉÈÊËÎÏŸÔÖÙÜÇÑ]/', '', $pAlpha);
		}
	}
	
	/**
	 * Récupération d'une chaine alphanumérique
	 * @param 	string	$pAlphaNum	la chaine ou l'on va récupérer les éléments
	 * @param 	boolean 
	 * @return string
	 */
	public static function getAlphaNum ($pAlphaNum, $pWithSpaces=true){
		// \w <=> [a-zA-Z0-9_] et a-z contient les accent si système est en fr.
		// \W tout ce qui n'est pas \w
		if ($pWithSpaces){
 	       return preg_replace('/[^a-zA-Z0-9àâäéèêëîïÿôöùüçñÀÂÄÉÈÊËÎÏŸÔÖÙÜÇÑ ]/', '', $pAlphaNum);
		}else{
			return preg_replace('/[^a-zA-Z0-9àâäéèêëîïÿôöùüçñÀÂÄÉÈÊËÎÏŸÔÖÙÜÇÑ]/', '', $pAlphaNum);
		}		
	}
	
	/**
	 * Retourne une décimal à partir d'une entrée
	 * @param	mixed	$pFloat	l'élément à transformer
	 * @return float
	 */
	public static function getFloat ($pFloat){
 		return floatval (str_replace (',', '.', self::getNumeric ($pFloat, true)));
	} 
} 
?>