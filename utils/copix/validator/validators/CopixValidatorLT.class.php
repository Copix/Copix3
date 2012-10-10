<?php
/**
 * @package copix
 * @subpackage validator
 * @author 		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file 
 */

/**
 * Validateur qui permet de vérifier qu'une valeur est inférieure à une autre
 * @package copix
 * @subpackage validator
 */
class CopixValidatorLT extends CopixAbstractValidator {
	protected function _validate ($pValue) {
		return $pValue <= $this->getParam ('value');
	}

	public function __construct ($pParams = array (), $pMessage = null) {
		if (! is_array ($pParams)) {
			$pParams = array ('value'=>$pParams); 
		}
		parent::__construct ($pParams, $pMessage);
	}	
}
?>