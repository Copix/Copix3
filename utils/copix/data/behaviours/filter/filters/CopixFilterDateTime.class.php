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
 * Filtre pour avoir une valeur par d�faut
 * 
 * @package copix
 * @subpackage filter
 */
class CopixFilterDate extends CopixAbstractFilter {
	public function __construct ($pParams = array ()){
		parent::__construct ($newParams);			
	}

	/**
	 * Si $pValue vaut null, retourne la valeur par défaut 
	 *
	 * @param mixed $pValue
	 * @return mixed
	 */
	public function get ($pValue){
		$from = $this->getParam ('from', 'yyyymmddHHIISS');
		$to = $this->getParam ('to', 'datetime');

		$value = $pValue;
		return CopixDateTime::yyyymmddhhiissToDateTime ($value);
	}	
}