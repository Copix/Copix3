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
 * Récupération des caractères de l'alphabet uniquement (avec les accents)
 * 
 * @package		copix
 * @subpackage	filter
 */
class CopixFilterAlpha extends CopixAbstractFilter {
	/**
	 * Récupération des caractères d'une chaine
	 */
	public function get ($pValue){
		if ($this->getParam ('allowSpaces', true)){
			return preg_replace('/[^a-zA-ZàâäéèêëîïÿôöùüçñÀÂÄÉÈÊËÎÏŸÔÖÙÜÇÑ ]/', '', _toString ($pValue));
		}
		return preg_replace('/[^a-zA-ZàâäéèêëîïÿôöùüçñÀÂÄÉÈÊËÎÏŸÔÖÙÜÇÑ]/', '', _toString ($pValue));
	}	
}