<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author		Gérald Croës, Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe abstraite d'un validateur.
 * @package copix
 * @subpackage validator
 */
abstract class CopixAbstractValidator extends CopixParameterHandler implements ICopixValidator  {
	
	/**
	 * Le message d'erreur à utiliser. 
	 * Si pas définit, générera un message d'erreur par défaut
	 *
	 * @var string
	 */
	protected $_message = null;
	
	/**
	 * Tableaux des paramètres
	 *
	 * @var array
	 */
	protected $_params;
	
	/**
	 * Fonction protégée permettant de retourné le message d'erreur
	 *
	 * @param string $pValue valeur de la variable
	 * @param bool $pResult résultat de la validation 
	 * @return string
	 */
	protected function _getMessage ($pValue, $pResult){
		return $this->_message !== null ? $this->_message : ($pResult === false ? ($pValue . ' est une valeur incorrecte pour '.$this->_getName ()) : $pResult);
	}

	/**
	 * Récupération du nom du validateur
	 *
	 * @return string
	 */
	private function _getName (){
		return get_class ($this);
	}
	
	/**
	 * Contructeur de la classe
	 *
	 * @param array   $pParams  tableau des paramètres	
	 * @param string  $pMessage le message d'erreur que l'on souhaite afficher en cas d'échec 
	 */
	public function __construct ($pParams = array (), $pMessage = null){
		$this->setParams ($pParams);
		$this->_message = $pMessage;
	}

	/**
	 * Lance la vérification du validateur. 
	 *
	 * @param	mixed 	$pValue	La valeur à tester
	 * @return 	true en cas de succès. CopixValidatorErrorCollection en cas d'échec
	 */
	public function check ($pValue){
		if (($result = $this->_validate ($pValue)) !== true){
			return new CopixErrorObject ($this->_getMessage ($pValue, $result));
		}
		return true;
	}

	/**
	 * Lance la vérification du validateur. S'il existe un échec, lève une exception de type CopixValidatorException
	 *
	 * @param mixed $pValue la valeur à vérifier par le validateur
	 * @throws CopixValidatorException
	 */
	public function assert ($pValue){
		if (($result = $this->check ($pValue)) !== true){
			throw new CopixValidatorException (new CopixErrorObject ($this->_getMessage ($pValue, $result)));
		}
	}

	/**
	 * Fonction a implémenter par les descendants, qui devra retourner true en cas de succès, 
	 *  et tout autre valeur en cas d'échec.
	 *
	 * @param mixed $pValue la valeur à vérifier
	 */
	abstract protected function _validate ($pValue);

	/**
	 * Affichage des messages d'erreurs si les paramètres donnés ne sont pas corrects pour le validateur
	 * @see   CopixParameterHandler
	 * @param array $pErrors tableau es messages d'erreurs
	 * @throws CopixException
	 */
	protected function _reportErrors ($pErrors){
    	$errors = array();
    	if(isset($pErrors['missing'])) {
    		$errors[] = _i18n('copix:copix.error.tag.missingParameters', implode(",", array_keys($pErrors['missing'])));
    	}
    	if(isset($pErrors['unknown'])) {
    		$errors[] = _i18n('copix:copix.error.tag.unknownParameters', implode(",", array_keys($pErrors['unknown'])));
    	}
    	if(isset($pErrors['invalid'])) {
    		$errors[] = _i18n('copix:copix.error.tag.invalidValues', implode(",", array_keys($pErrors['invalid'])));
    	}
    	throw new CopixException('[Validator '.$this->_getName ().']: '.implode("; ", $errors));
	}
}