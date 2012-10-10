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
	/**
	 * Valide le tableau en fonction des options qui ont été passées au constructeur
	 * 
	 * Vérifie en premier lieu que $pValue est un tableau 
	 * $options['contains'] vérifie que le tableau contient une valeur donnée
	 * $options['maxSize']  vérifie que le tableau ne dépasse pas une taille donnée
	 * $options['minSize']  vérifie que le tableau est d'une taille minimale donnée
	 * $options['size']     vérifie que le tableau est d'une taille donnée
	 */
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
		
		if ($maxSize = $this->getParam ('maxSize', null, _validator ('numeric', array ('min'=>0)))){
			if (count ($pValue) > $maxSize){
				$toReturn[] = _i18n ('copix:copixvalidator.array.maxSize', array ($maxSize, count ($pValue)));
			}
		}

		if ($minSize = $this->getparam ('minSize', null, _validator ('numeric', array ('min'=>0)))){
			if (count ($pValue) < $minSize){
				$toReturn[] = _i18n ('copix:copixvalidator.array.minSize', array ($minSize, count ($pValue)));
			}
		}
		
		if ($size = $this->getparam ('size', null, _validator ('numeric', array ('min'=>0)))){
			if (count ($pValue) != $size){
				$toReturn[] = _i18n ('copix:copixvalidator.array.size', array ($size, count ($pValue)));
			}
		}

		return empty ($toReturn) ? true : $toReturn;
	}
}