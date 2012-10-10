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
 * Validateur qui permet de vérifier qu'une valeur est un objet avec un certain nombre d'options
 * @package copix
 * @subpackage validator
 */
class CopixValidatorObject extends CopixAbstractValidator {
	protected function _validate ($pValue) {
		if (!is_object ($pValue)){
			return _i18n ('copix:copixvalidator.object.object', $pValue);			
		}
		$toReturn = array ();

		if ($interface = $this->getParam ('implements', null)){
			if (! $pValue instanceof $interface){
				$toReturn[] = _i18n ('copix:copixvalidator.object.mustImplements', $interface);
			}
		}
		
		return empty ($toReturn) ? true : $toReturn;		
	}
}
?>