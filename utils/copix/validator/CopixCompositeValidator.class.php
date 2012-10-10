<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */


class CopixCompositeValidator extends CopixAbstractValidator implements ICopixCompositeValidator {
	private $_validators = array ();

	public function __construct ($pMessage = null){
		parent::__construct (array (), $pMessage);
	}
	protected function _getMessage ($pValue, $pErrors){
		if ($this->_message === null){
			return $pErrors;
		}
		return $this->_message; 
	}

	public function attach (ICopixValidator $pValidator){
		$this->_validators[] = $pValidator;
		return $this;
	}
	
	public function assert ($pValue){
		foreach ($this->_validators as $validator){
			$validator->assert ($pValue);
		}
	}
	/**
	 * La surcharge de check la rend inutile, 
	 * c'est peu élégant mais cela évite de redevoir implémenter le reste
	 */
	protected function _validate ($pValue){
		$toReturn = new CopixErrorObject ();
		foreach ($this->_validators as $validator){
			if (($result = $validator->check ($pValue)) !== true){
				$toReturn->addErrors ($result, true);
			}
		}
		return $toReturn->isError () ? new CopixErrorObject ($this->_getMessage ($pValue, $toReturn)) : true;
	}
}
?>