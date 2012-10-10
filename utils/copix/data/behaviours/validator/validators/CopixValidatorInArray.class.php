<?php
/**
 * @package     copix
 * @subpackage  validator
 * @author      Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file 
 */

/**
 * Validateur qui permet de vérifier qu'une valeur est comprise dans un tableau donné
 * @package copix
 * @subpackage validator
 */
class CopixValidatorInArray extends CopixAbstractValidator {
	/**
	 * Vérifie que la valeur donnée fait parti d'un ensemble de valeurs
	 * 
	 * L'ensemble de valeur possible est passé dans l'option "values" dans 
	 * le processus de création du validateur
	 */
	protected function _validate ($pValue) {
		if ($this->getParam ('useArrayValues', false)){
			//S'il faut utiliser chaque valeur de $pValue (si c'est un tableau) dans la validation
			if (is_array ($pValue)){
				foreach ($pValue as $value){
					if (! in_array ($pValue, 
						$this->getParam ('values', array (), 'array'), 
						_filter ('boolean')->get ($this->getParam ('strict', false)))){
							return false;
					}
				}
				return true;
			}
		}

		//On utilise $pValue tel quel
		return in_array ($pValue, 
					$this->getParam ('values', array (), 'array'), 
					_filter ('boolean')->get ($this->getParam ('strict', false)));
	}
}