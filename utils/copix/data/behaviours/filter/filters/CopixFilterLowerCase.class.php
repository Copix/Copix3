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
 * Filtre qui permet de transformer un élément en chaine en minuscules
 *  
 * @package copix
 * @subpackage filter 
 */
class CopixFilterLowerCase extends CopixAbstractFilter {
	/**
	 * Récupère la variable transformée
	 *
	 * @param string $pValue
	 * @return string
	 */	
	public function get ($pValue){
		if (extension_loaded ('mbstring')){
			return mb_strtolower (_toString ($pValue), CopixI18N::getCharset ());
		}
		return strtolower (_toString ($pValue));
	}
}