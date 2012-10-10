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
abstract class CopixAbstractValidator implements ICopixValidator  {
	/**
	 * Le message d'erreur à utiliser. 
	 * Si pas définit, générera un message d'erreur par défaut
	 *
	 * @var string
	 */
	protected $_message;
	
	/**
	 * Les paramètres du validateur
	 *
	 * @var unknown_type
	 */
	protected $_params;

	/**
	 * Récupération du message d'erreur pour la valeur $pValue avec le résultat de test en paramètre
	 *
	 * @param	mixed $pValue		la valeur qui a généré le message d'erreur 
	 * @param	mixed $pResult	le résultat de "check"
	 * @return  mixed (array, boolean, CopixErrorObject)
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
		$this->_params = new CopixParameterHandler ($pParams);
		$this->_params->setParams ($pParams);
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
	 * Récupère la donnée et applique le validateur en premier lieu
	 *
	 * @param mixed $pValue la valeur a récupérer si elle est valide
	 * @return mixed
	 */
	public function get ($pValue){
		$this->assert ($pValue);
		return $pValue;
	}

	public function getParam ($pName, $pDefault = null, $pType = null, $pDefaultIfNotValidate = false){
		return $this->_params->getParam ($pName, $pDefault, $pType, $pDefaultIfNotValidate);
	}
	public function requireParam ($pName, $pType = null){
		return $this->_params->requireParam ($pName, $pType);
	}
	public function assertParams (){
		return call_user_func_array (array ($this->_params, 'assertParams'), func_get_args ());
	}
	public function getParams (){
		return $this->_params->getParams ();
	}
	/**
	 * Fonction a implémenter par les descendants, qui devra retourner true en cas de succès, 
	 *  et tout autre valeur en cas d'échec.
	 *
	 * @param mixed $pValue la valeur à vérifier
	 */
	abstract protected function _validate ($pValue);
}