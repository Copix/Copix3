<?php
/**
 * @package		copix
 * @subpackage	filter
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Filtres pour récupérer des données booléennes
 * 
 * @package		copix
 * @subpackage	filter
 */
class CopixFilterBoolean extends CopixAbstractFilter {
	
	/**
	 * Les valeurs considérées comme "vraies"
	 *
	 * @var array
	 */
	protected $_true = array ();

	/**
	 * Les valeurs considérées comme "fausses"
	 *
	 * @var unknown_type
	 */
	protected $_false = array ();

	/**
	 * Initialisation du filtre, permet de rajouter des valeurs à considérer comme true / false 
	 */
	public function __construct ($pParams = array ()){
		parent::__construct ($pParams);
		$this->_true  = array ('yes', 'y', 'o', 'oui', 'true', true, 'enable', 'enabled');
		$this->_false = array ('no', 'n', 'n', 'non', 'false', false, 'disable', 'disabled');

		//Ajoute les options considérées comme "vraie" passées au constructeur
		if (count ($true = $this->getParam ('true', array (), new CopixValidatorArray ()))){
			if ($this->getParam ('replaceTrueValues', false)){
				$this->_true = $true;
			}else{
				$this->_true = array_merge ($this->_true, $true);
			}
		}

		//Ajoute les options considérées comme "false" passées au constructeur
		if (count ($false = $this->getParam ('false', array (), new CopixValidatorArray ()))){
			if ($this->getParam ('replaceFalseValues', false)){
				$this->_false = $false;
			}else{
				$this->_false = array_merge ($this->_false, $false);
			}
		}
	}

	/**
	 * Récupération d'un boolean a partir d'une chaine
	 * 
	 * @param mixed $pValue la valeur à tester 
	 * @return boolean
	 */
	public function get ($pValue){
		//On met en minuscules uniquement si c'est une chaine de caractères
		if (is_string ($pValue)){
			$pValue = strtolower ($pValue);
		}

		//Dans les valeurs vraies ? alors ok
		if (in_array ($pValue, $this->_true, true)){
			return true;
		}

		//Dans les valeurs false ? alors ko		
		if (in_array ($pValue, $this->_false, true)){
			return false;
		}
		
		//Si l'utilisateur souhaite retourner false dans l'optique ou la valeur n'est pas trouvée dans le $
		//tableau des valeurs "vraies" (quand bien même la valeur n'est pas trouvée dans les valeurs false)
		if ($this->getParam ('defaultIsFalse', false)){
			return false;
		}

		//En dernier lieu, si on a rien trouvé, on converti en entier et on retourne 
		// vrai si la valeur convertie est supérieure à 0, false dans le cas contraire
		$filterInt = new CopixFilterInt ();
		return $filterInt->get ($pValue) > 0; 
	}
}