<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		VUIDART Sylvain
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

/**
 * Champ non modifiable : affiche la valeur + le champ hidden correspondant
 *
 */
class CopixFieldUnEditable extends CopixFieldVarchar  {
	
	public function getHTML ($pName, $pValue, $pMode = 'edit') {
		return $pValue;
	}
		
	/**
	 * Affichage de l'élement
	 * @param $pName
	 * @param $pValue
	 * @return string code html
	 */
	public function getHTMLFieldEdit ($pName, $pValue) {
		return '<input type="hidden" name="'.$pName.'" value="'.$pValue.'" /><label for="'.$pName.'">'.$pValue.'</label>';
	}
}