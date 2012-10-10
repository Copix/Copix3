<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author		Gérald Croës, Salleyron Julien
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Interface respectée par tous les validateurs
 * @package copix
 * @subpackage validator
 */
interface ICopixValidator {
	public function check ($pValue);
	public function assert ($pValue);
}

/**
 * Interface respectée par les validateur composés
 * @package copix
 * @subpackage validator
 */
interface ICopixCompositeValidator extends ICopixValidator {
	public function attach (ICopixValidator $pValidator);
}

/**
 * Interface respectée par les validateurs de type complexes
 * @package copix
 * @subpackage validator
 */
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
	
	/**
	 * Erreurs qui ont donné lieu à l'exception
	 * @var CopixErrorObject
	 */
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
	 * @return CopixErrorObject
	 */
	public function getErrorObject () {
		return $this->_errors;
	}
	
}

/**
 * Fabrique de validateurs
 * @package copix
 * @subpackage validator
 */
class CopixValidatorFactory {
	/**
	 * Création d'un validateur
	 * @param string $pName    le nom du validateur à créer (peut correspondre à module|classe pour des validateurs personnels)
	 * @param array  $pParams  Un tableau d'options a passer au validateur
	 * @param string $pMessage Le message d'erreur a afficher par le validateur en cas d'échec
	 * @return ICopixValidator
	 * @throws CopixException si le validateur n'existe pas ou si le validateur ne respecte pas l'interface ICopixValidator
	 */
	public static  function create ($pName, $pParams = array (), $pMessage = null){
		$className = 'CopixValidator'.$pName;
		if (class_exists ($className, (strpos ($pName, '|') === false && strpos ($pName, ':') === false))){
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
	
	/**
	 * Création d'un validateur composé
	 * @param string $pMessage le message d'erreur a afficher en cas d'échec de validation
	 * @return ICopixCompositeValidator
	 */
	public static function createComposite ($pMessage = null){
			return new CopixCompositeValidator ($pMessage);
	}
	
	/**
	 * Création d'un validateur d'objet (CopixComplexeTypeValidator)
	 * @param string $pMessage Le message d'erreur à afficher en cas d'échec de validation
	 * @return CopixObjectValidator
	 */
	public  static function createObject ($pMessage = null){
		return new CopixObjectValidator ($pMessage);
	}
	
	/**
	 * Création d'un validateur de tableau (CopixComplexeTypeValidator)
	 * @param string $pMessage Le message d'erreur à afficher en cas d'échec de validation
	 * @return CopixArrayValidator
	 */
	public  static function createArray ($pMessage = null){
		return new CopixArrayValidator ($pMessage);
	}
	
	/**
	 * Création d'un validateur de type complexe
	 * @param string $pMessage Le message d'erreur à afficher en cas d'échec de validation
	 * @return ICopixComplexeTypeValidator
	 */
	public  static function createComplexType ($pMessage = null){
		return new CopixComplexTypeValidator ($pMessage);
	}
}