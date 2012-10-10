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
 * Filtres pour récupérer une chaine en supprimant les caractères supperflus en début et fin de chaine
 * 
 * @package		copix
 * @subpackage	filter
 */
class CopixFilterTrim extends CopixAbstractFilter {
	/**
	 * Supprime les caractères inutiles en début et fin de chaine
	 *
	 * @param string $pValue
	 * @return string
	 */
	public function get ($pValue){
		//on fait le if car si on passe effectivement null en deuxième paramètre à charlist, rien n'est "trimé".
		if (($charList = $this->getParam ('charList', null, 'string')) !== null){
			return trim (_toString ($pValue), $charList);
		}else{
			return trim (_toString ($pValue));
		}
	}
}