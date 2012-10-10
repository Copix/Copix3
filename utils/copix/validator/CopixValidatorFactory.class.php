<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

interface ICopixValidator {
	public function check ($pValue);
	public function assert ($pValue);
}

interface ICopixCompositeValidator extends ICopixValidator {
	public function attach (ICopixValidator $pValidator);
}

interface ICopixComplexTypeValidator extends ICopixValidator {
	public function attachTo (ICopixValidator $pValidator, $pPropertyPath);
	public function required ($pPropertyPath);
}

/**
 * Exception de validator
 * @package		copix
 * @subpackage	validator
 */
class CopixValidatorException extends CopixException {
	
	//Tableau d'erreurs
	private $_errors;
	
	/**
	 * Constructeur qui génère le message de l'exception
	 *
	 * @param array $pErrors Tableau d'erreur
	 */
	public function __construct ($pErrors) {
		$this->_errors = $pErrors;
		parent::__construct (_toString ($pErrors));
	}
	
	/**
	 * Renvoi les erreurs de l'exception
	 *
	 * @return array
	 */
	public function getErrorObject () {
		return $this->_errors;
	}
	
}

class CopixValidatorFactory {
	public static  function create ($pName, $pParams = array (), $pMessage = null){

		$className = 'CopixValidator'.$pName;
		if (class_exists ($className)){
			return new $className ($pParams, $pMessage);
		}
		try {
			$toReturn = _class ($pName, array ($pParams, $pMessage));
		}catch (Exception $e){
			throw new CopixException (_i18n ('copix:copixvalidator.composite.maynotimplement', array ($pName, $e->getMessage ())));			
		}

		if ($toReturn instanceof ICopixValidator){
			return $toReturn;
		}
		throw new CopixException (_i18n ('copix:copixvalidator.composite.notimplement', $pName));
	}
	
	public static function createComposite ($pMessage = null){
			return new CopixCompositeValidator ($pMessage);
	}
	
	public  static function createObject ($pMessage = null){
		return new CopixObjectValidator ($pMessage);
	}
	
	public  static function createArray ($pMessage = null){
		return new CopixArrayValidator ($pMessage);
	}
	
	public  static function createComplexType ($pMessage = null){
		return new CopixComplexTypeValidator ($pMessage);
	}
}
?>