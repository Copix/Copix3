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
 * Filtres pour récupérer des données entières
 * @package		copix
 * @subpackage	filter
 */
class CopixFilterInt extends CopixAbstractFilter {
	/**
	 * Récupération d'un entier à partir de la variable
	 */	
	public function get ($pValue){
		return intval (_filter ('numeric', array ('withComma'=>true))->get ($pValue));		
	} 
}