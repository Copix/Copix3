<?php
/**
 * @package		copix
 * @subpackage	filter
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Filtres pour récupérer des données numériques
 * @package		copix
 * @subpackage	filter
 */
class CopixFilterNumeric extends CopixAbstractFilter {
	/**
	 * Récupération d'un numérique à partir de la variable
	 * @param 	mixed	$pNumeric	la variable à récupérer sous la forme d'un numérique
	 * @param	boolean	$pWithComma	si l'on souhaite inclure les virgules et points dans l'élément
	 * @return numeric
	 */
	public function get ($pValue){
		if ($this->getParam ('withComma', false)){
			$value = preg_replace ('/[^\d.-]/', '', str_replace (',', '.', _toString ($pValue)));
		}else{
			$value = preg_replace ('/[^\d-]/', '', _toString ($pValue));
		}
		return $value;
	}	
}