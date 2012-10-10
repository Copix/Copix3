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
 * Validateur qui permet de vérifier qu'une valeur est supérieure à une autre
 * @package copix
 * @subpackage validator
 */
class CopixValidatorGT extends CopixAbstractValidator {
	/**
	 * Vérifie que la valeur est supérieure à attendu
	 *
	 * @param mixed $pValue la valeur à vérifier
	 * @return boolean
	 */
	protected function _validate ($pValue) {
		return $pValue >= $this->getParam ('value');
	}

	/**
	 * Construction
	 * 
	 * L'option value est supportée (pour indiquer la valeur minimale ou égale)
	 * Il est possible de donner une valeur directe (et non sous forme de tableau) pour la valeur minimale.
	 */
	public function __construct ($pParams = array (), $pMessage = null) {
		if (! is_array ($pParams)) {
			$pParams = array ('value'=>$pParams); 
		}
		parent::__construct ($pParams, $pMessage);
	}	
}