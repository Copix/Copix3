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
	/**
	 * Vérifie que la valeur est inférieure à attendu
	 *
	 * @param mixed $pValue la valeur à vérifier
	 * @return boolean
	 */
	protected function _validate ($pValue) {
		return $pValue <= $this->getParam ('value');
	}

	/**
	 * Construction
	 * 
	 * L'option value est supportée (pour indiquer la valeur maximale ou égale)
	 * Il est possible de donner une valeur directe (et non sous forme de tableau) pour la valeur maximale.
	 */
	public function __construct ($pParams = array (), $pMessage = null) {
		if (! is_array ($pParams)) {
			$pParams = array ('value'=>$pParams); 
		}
		parent::__construct ($pParams, $pMessage);
	}	
}