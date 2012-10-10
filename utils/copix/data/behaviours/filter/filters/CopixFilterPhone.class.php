<?php
/**
 * @package		copix
 * @subpackage	filter
 * @author		Selvi ARIK
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Filtres pour récupérer des numéros de téléphone
 * @package		copix
 * @subpackage	filter
 */
class CopixFilterPhone extends CopixAbstractFilter {
	/**
	 * @param 	string  $pValue	la variable à récupérer sous la forme d'un numéro de téléphone
	 * @param	string  $pSeparator	caractère de séparation entre les chiffres
	 * @return  string
	 */
	public function get ($pValue){
		$pSeparator = $this->getParam ('separator', '');
		return preg_replace ('/[^\+?\d'.$pSeparator.']/', '', $pValue);
	}	
}