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
           return preg_replace('/[-+]?[^\d.]/', '', str_replace (',', '.', _toString($pNumeric)));
		}else{
           return preg_replace('/[-+]?[^\d]/', '', _toString($pNumeric));
		}
	}
	
	/**
	 * Récupération des caractères d'une chaine
	 * @param	string	$pAlpha	chaine de caractère à traiter
	 * @return string
	 */
	public static function getAlpha ($pAlpha, $pWithSpaces=true){
		if ($pWithSpaces){
			return preg_replace('/[^a-zA-ZàâäéèêëîïÿôöùüçñÀÂÄÉÈÊËÎÏŸÔÖÙÜÇÑ ]/', '', _toString($pAlpha));
		}else{
			return preg_replace('/[^a-zA-ZàâäéèêëîïÿôöùüçñÀÂÄÉÈÊËÎÏŸÔÖÙÜÇÑ]/', '', _toString($pAlpha));
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
 	       return preg_replace('/[^a-zA-Z0-9àâäéèêëîïÿôöùüçñÀÂÄÉÈÊËÎÏŸÔÖÙÜÇÑ ]/', '', _toString($pAlphaNum));
		}else{
			return preg_replace('/[^a-zA-Z0-9àâäéèêëîïÿôöùüçñÀÂÄÉÈÊËÎÏŸÔÖÙÜÇÑ]/', '', _toString($pAlphaNum));
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
	
	/**
	 * Retourne un booléen à partir d'une entrée.
	 * 
	 * Evalue les chaînes suivantes comme vrai : yes, true, enable, enabled, 1.
	 * Evalue les chaînes suivantes comme faux:  no, false, disable, disabled, 0.
	 * Si cela ne colle pas, transforme la chaîne en entier, 0 s'évalue comme faux et tout le reste comme vrai. 
	 *
	 * @param mixed $pBoolean L'élément à transformer.
	 * @return boolean
	 */
	public static function getBoolean ($pBoolean) {
		switch(strtolower(_toString ($pBoolean))) {
			case 'yes': case 'true': case 'enable': case 'enabled': case '1':
				return true;
			case 'no': case 'false': case 'disable': case 'disabled': case '0': case '':
				return false;
			default:
				return self::getInt($pBoolean) != 0;
		}
	}
} 
?>