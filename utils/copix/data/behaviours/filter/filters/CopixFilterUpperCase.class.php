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
 * Filtre qui permet de transformer un élément en chaine de caractères en majuscules
 *  
 * @package		copix
 * @subpackage	filter  
 */
class CopixFilterUpperCase extends CopixAbstractFilter {
	/**
	 * Transforme la valeur en majuscule
	 *
	 * @param string $pValue
	 * @return string
	 */
	public function get ($pValue){
		if (extension_loaded ('mbstring')){
			return mb_strtoupper (_toString ($pValue), CopixI18N::getCharset ());
		}
		return strtoupper (_toString ($pValue));
	}
}