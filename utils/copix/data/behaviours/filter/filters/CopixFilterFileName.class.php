<?php
/**
 * @package		copix
 * @subpackage	filter
 * @author		Cro�s G�rald
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Supression des caract�res qui ne rentrent pas dans la composition des noms de fichiers
 * 
 * @package		copix
 * @subpackage	filter
 */
class CopixFilterFileName extends CopixAbstractFilter {

	/**
	 * R�cup�ration des caract�res alphanum�riques d'une chaine
	 */
	public function get ($pValue){
		$pValue = preg_replace('/[^a-zA-Z0-9������������������������ϟ������ _.]/', '', _toString ($pValue));
		//on laisse un seul point cons�cutif si jamais ils ont �t�s doubl�s apr�s traitement
		return preg_replace('/\.+/', '.', $pValue);
	}	
}