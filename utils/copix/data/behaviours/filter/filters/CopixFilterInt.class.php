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
	 * Filtre numérique utilisé en interne
	 *
	 * @var CopixFilterNumeric
	 */
	private $_numericFilter;
	
	/**
	 * Constructeur
	 *
	 * @param array $pParams paramètre passés au filtre
	 */
	public function __construct ($pParams = array ()){
		$this->_numericFilter = new CopixFilterNumeric (array ('withComma'=>true));
		parent::__construct ($pParams);
	}
	
	/**
	 * Récupération d'un entier à partir de la variable
	 */	
	public function get ($pValue){
		return intval ($this->_numericFilter->get ($pValue));		
	} 
}