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
 * Validateur composite (qui est composé de plusieurs validateurs)
 * @package copix
 * @subpackage validator
 */
class CopixCompositeValidator extends CopixAbstractValidator implements ICopixCompositeValidator {
	/**
	 * La liste des validateurs compris dans le validateur
	 * @var array
	 */
	private $_validators = array ();

	/**
	 * Construction
	 * @param string $pMessage le message d'erreur qu'on souhaite afficher en cas de non validation
	 */
	public function __construct ($pMessage = null){
		parent::__construct (array (), $pMessage);
	}
	
	/**
	 * Récupération d'un message d'erreur en cas d'échec de contrôle pour $pValue avec 
	 *  le résultat du valdiateur concret $pErrors
	 * @param mixed $pValue La valeur qui n'a pas passé la validation
	 * @param mixed $pErrors Ce qui a été retourné par le validateur qui a échoué sur $pValue
	 * @return mixed 
	 */
	protected function _getMessage ($pValue, $pErrors){
		if ($this->_message === null){
			return $pErrors;
		}
		return $this->_message; 
	}

	/**
	 * Attache un validateur à la liste des validateurs déja enregistrés
	 * @param ICopixValidator $pValidator Le validateur à ajouter a la liste
	 * @return ICopixCompositeValidator (this) pour pouvoir enchainer les appels
	 */
	public function attach (ICopixValidator $pValidator){
		$this->_validators[] = $pValidator;
		return $this;
	}
	
	/**
	 * Lance la vérification de $pValue avec l'ensemble des validateurs déja enregistrés.
	 * Lance une exception si $pValue ne respecte pas les validateurs enregistrés.
	 * @param mixed $pValue la valeur à contrôller
	 * @throws CopixValidatorException
	 */
	public function assert ($pValue){
		foreach ($this->_validators as $validator){
			$validator->assert ($pValue);
		}
	}
	
	/**
	 * Lancement des validations de la série de validateurs enregistrés pour la valeur
	 * @param mixed $pValue la valeur à controller
	 * @return true si ok, CopixErrorObject sinon
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