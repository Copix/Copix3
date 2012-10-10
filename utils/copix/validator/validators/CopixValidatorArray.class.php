<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author 		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file 
 */

/**
 * Validateur qui permet de vérifier qu'une valeur est un tableau et qu'il respecte plusieurs conditions
 * @package copix
 * @subpackage validator
 */
class CopixValidatorArray extends CopixAbstractValidator {
	protected function _validate ($pValue) {
		if (!is_array ($pValue)){
			return _i18n ('copix:copixvalidator.array.array', $pValue);			
		}
		$toReturn = array ();

		if ($search = $this->getParam ('contains', null)){
			if (! in_array ($search, $pValue)){
				$toReturn[] = _i18n ('copix:copixvalidator.array.mustContains', $search);
			}
		}
		
		if ($maxSize = $this->getparam ('maxSize', null)){
			if (count ($pValue) > $maxSize){
				$toReturn[] = _i18n ('copix:copixvalidator.array.maxSize', array ($maxSize, count ($pValue)));
			}
		}

		if ($minSize = $this->getparam ('minSize', null)){
			if (count ($pValue) < $minSize){
				$toReturn[] = _i18n ('copix:copixvalidator.array.minSize', array ($minSize, count ($pValue)));
			}
		}
		
		if ($size = $this->getparam ('size', null)){
			if (count ($pValue) != $size){
				$toReturn[] = _i18n ('copix:copixvalidator.array.size', array ($size, count ($pValue)));
			}
		}

		return empty ($toReturn) ? true : $toReturn;
	}
}
?>