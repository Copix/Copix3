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
 * Récupération des caractères alphanumériques d'une chaine
 * 
 * @package		copix
 * @subpackage	filter
 */
class CopixFilterAlphaNum extends CopixAbstractFilter {
	/**
	 * Récupération des caractères alphanumériques d'une chaine
	 */
	public function get ($pValue){
        $replacePattern = '/[^a-zA-Z0-9àâäéèêëîïÿôöùüçñÀÂÄÉÈÊËÎÏŸÔÖÙÜÇÑ';

		// \w <=> [a-zA-Z0-9_] et a-z contient les accent si système est en fr.
		// \W tout ce qui n'est pas \w
		if ($this->getParam ('allowSpaces', true)){
			$replacePattern .= ' ';
		}

        if ($this->getParam ('allowUnderscores', false)){
            $replacePattern .= '_';

        }
		return preg_replace($replacePattern.']/', '', _toString ($pValue));
	}	
}