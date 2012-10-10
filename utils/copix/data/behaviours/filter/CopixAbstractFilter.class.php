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
 * Classe de base pour les filtres Copix
 * 
 * @package		copix
 * @subpackage	filter
 */
abstract class CopixAbstractFilter implements ICopixFilter {
	protected $_params;

	/**
	 * Construction de l'objet filtre
	 *
	 * @param unknown_type $pParams
	 */
	public function __construct ($pParams = array ()){
		$this->_params = new CopixParameterHandler ($pParams);
		$this->_params->setParams ($pParams);
	}

	/**
	 * Modification de $pValue avec le filtre.
	 * 
	 * @param  mixed $pValue la valeur à modifier avec le filtre
	 * @return mixed la valeur modifiée  
	 */
	public function update (& $pValue){
		return $pValue = $this->get ($pValue);
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
}