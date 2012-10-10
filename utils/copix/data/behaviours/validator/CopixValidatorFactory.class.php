<?php
/**
 * @package copix
 * @subpackage validator
 * @author Gérald Croës, Salleyron Julien
 * @copyright CopixTeam
 * @link http://copix.org
 * @license	 http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Fabrique de validateurs
 *
 * @package copix
 * @subpackage validator
 */
class CopixValidatorFactory {
	/**
	 * Création d'un validateur
	 * 
	 * @param string $pName Nom du validateur à créer (peut correspondre à module|classe pour des validateurs personnels)
	 * @param array $pParams Options a passer au validateur
	 * @param string $pMessage Message d'erreur à afficher par le validateur en cas d'échec
	 * @return ICopixValidator
	 * @throws CopixException Si le validateur n'existe pas ou si le validateur ne respecte pas l'interface ICopixValidator
	 */
	public static function create ($pName, $pParams = array (), $pMessage = null) {
		$className = CopixSelectorFactory::purge ($pName);

		// validateur copix
		if (strpos ($pName, '|') === false && CopixAutoloader::canAutoload ('CopixValidator' . $pName)) {
			$className = 'CopixValidator' . $pName;
		}
		$toReturn = new $className ($pParams, $pMessage);

		//On vérifie que c'est bien un ICopixValidator
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
	public static function createObject ($pMessage = null){
		return new CopixObjectValidator ($pMessage);
	}
	
	/**
	 * Création d'un validateur de tableau (CopixComplexeTypeValidator)
	 * @param string $pMessage Le message d'erreur à afficher en cas d'échec de validation
	 * @return CopixArrayValidator
	 */
	public static function createArray ($pMessage = null){
		return new CopixArrayValidator ($pMessage);
	}
	
	/**
	 * Création d'un validateur de type complexe
	 * @param string $pMessage Le message d'erreur à afficher en cas d'échec de validation
	 * @return ICopixComplexeTypeValidator
	 */
	public static function createComplexType ($pMessage = null){
		return new CopixComplexTypeValidator ($pMessage);
	}
}