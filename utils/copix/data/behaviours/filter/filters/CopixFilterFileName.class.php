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
 * Supression des caractères qui ne rentrent pas dans la composition des noms de fichiers
 * 
 * @package		copix
 * @subpackage	filter
 */
class CopixFilterFileName extends CopixAbstractFilter {

	/**
	 * Récupération des caractères alphanumériques d'une chaine
	 */
	public function get ($pValue){
		$pValue = preg_replace('/[^a-zA-Z0-9àâäéèêëîïÿôöùüçñÀÂÄÉÈÊËÎÏŸÔÖÙÜÇÑ _.]/', '', _toString ($pValue));
		//on laisse un seul point consécutif si jamais ils ont étés doublés après traitement
		return preg_replace('/\.+/', '.', $pValue);
	}	
}