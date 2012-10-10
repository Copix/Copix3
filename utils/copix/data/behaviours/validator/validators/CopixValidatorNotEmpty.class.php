<?php
/**
 * @package     copix
 * @subpackage  validator
 * @author 	    Croës Gérald
 * @copyright   CopixTeam
 * @link	    http://copix.org
 * @license	    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Validateur qui permet de vérifier qu'une valeur n'est pas vide (is_empty)
 * @package copix
 * @subpackage validator
 * @since 3.0.4 
 */
class CopixValidatorNotEmpty extends CopixAbstractValidator {
	/**
	 * 
	 *
	 * @param array $pParams
	 * @param string $pMessage
	 */
	public function __construct ($pParams = array (), $pMessage = null){
		if (!is_array ($pParams)){
			$pParams = array ('trim'=>$pParams);
		}
		if (!isset ($pParams['trim'])){
			$pParams['trim'] = true;
		}

		parent::__construct ($pParams, $pMessage);
	}

	/**
	 * Validation de la valeur
	 *
	 * @param mixed $pValue la valeur qui ne doit pas être nulle
	 * @return boolean / array
	 */
	protected function _validate ($pValue){
		$pValue = (is_string ($pValue) && $this->getParam ('trim', true)) ? trim ($pValue) : $pValue;		

		if (empty ($pValue)){
			return _i18n ('copix:copixvalidator.notempty');
		}
		
		return true;
	}
}