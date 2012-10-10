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
 * Filtre pour avoir une valeur par défaut
 * 
 * @package copix
 * @subpackage filter
 */
class CopixFilterDefault extends CopixAbstractFilter {
	/**
	 * Filtre boolean utilisé en interne
	 *
	 * @var CopixFilterBoolean
	 */
	private $_booleanFilter;
	
	/**
	 * Constructeur qui accepte un paramètre de type autre qu'un tableau comme paramètre "default".
	 */
	public function __construct ($pParams = array ()){
		$this->_booleanFilter = new CopixFilterBoolean ();

		$newParams = array ();
		if (!is_array ($pParams)){
 			$newParams['default'] = $pParams;
			parent::__construct ($newParams);
		}elseif (!array_key_exists ('default', $pParams)){
			$newParams['default'] = $pParams;
			parent::__construct ($newParams);			
		}else{
			parent::__construct ($pParams);
		}
	}

	/**
	 * Si $pValue vaut null, retourne la valeur par défaut 
	 *
	 * @param mixed $pValue
	 * @return mixed
	 */
	public function get ($pValue){
		//Si la valeur est nulle, alors on retourne la valeur par défaut spécifiée
		if ($pValue === null){
			return $this->requireParam ('default');
		}

		//Si la valeur est vide et que l'on a demandé a considérer les valeurs vides comme défaut, 
		//alors on retourne la valeur spécifiée par défaut
		if ($this->_booleanFilter->get ($this->getParam ('empty'))){
			if (empty ($pValue)){
				return $this->requireParam ('default');
			}
		}

		//Si on a passé un validateur et que ce dernier indique une erreur,  
		//alors on retourne la valeur spécifiée par défaut
		if (($pvalidator = $this->getParam ('validator', null)) !== null){
			if (is_string ($pvalidator)){
				$validator = _validator ($pvalidator);
			}elseif ($pvalidator instanceof ICopixValidator){
				$validator = $pvalidator;
			}else{
				throw new CopixException ('[FilterDefault] Le validateur passé est incorrect');
			}
			if ($validator->check ($pValue) !== true){
				return $this->requireParam ('default');
			}
		}

		//Dans tous les autres cas, c'est ok, on retourne la valeur "normale"
		return $pValue;
	}	
}