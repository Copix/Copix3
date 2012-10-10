<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @deprecated  Utilisez _filter () / CopixFilterFactory::create () a la place
 */

/**
 * Filtres pour récupérer des données sous une certaine forme
 * 
 * @package		copix
 * @subpackage	utils
 * @deprecated
 */
class CopixFilter {
	/**
	 * Récupération d'un entier à partir de la variable
	 * @param 	mixed	$pInt	la variable à récupérer sous la forme d'un entier
	 * @return int
	 * @deprecated 
	 */
	public static function getInt ($pInt){
		return _filter ('int')->get ($pInt);
	}

	/**
	 * Récupération d'un numérique à partir de la variable
	 * @param 	mixed	$pNumeric	la variable à récupérer sous la forme d'un numérique
	 * @param	boolean	$pWithComma	si l'on souhaite inclure les virgules et points dans l'élément
	 * @return numeric
	 * @deprecated 
	 */
	public static function getNumeric ($pNumeric, $pWithComma = false){
		return _filter ('numeric', array ('withComma'=>$pWithComma))->get ($pNumeric);
	}

	/**
	 * Récupération des caractères d'une chaine
	 * @param	string	$pAlpha	chaine de caractère à traiter
	 * @return string
	 * @deprecated 
	 */
	public static function getAlpha ($pAlpha, $pWithSpaces=true){
		return _filter ('alpha', array ('allowSpaces'=>$pWithSpaces))->get ($pAlpha);
	}

	/**
	 * Récupération d'une chaine alphanumérique
	 * @param 	string	$pAlphaNum	la chaine ou l'on va récupérer les éléments
	 * @param 	boolean
	 * @return string
	 * @deprecated 
	 */
	public static function getAlphaNum ($pAlphaNum, $pWithSpaces=true){
		return _filter ('alphaNum', array ('allowSpaces'=>$pWithSpaces))->get ($pAlphaNum);
	}

	/**
	 * Retourne une décimal à partir d'une entrée
	 * @param	mixed	$pFloat	l'élément à transformer
	 * @return float
	 * @deprecated
	 */
	public static function getFloat ($pFloat){
		return _filter ('float')->get ($pFloat);		
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
	 * @deprecated
	 */
	public static function getBoolean ($pBoolean) {
		return _filter ('boolean')->get ($pBoolean);
	}
}