<?php
/**
 * @package		copix
 * @subpackage	filter
 * @author		Sylvain Vuidart
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Filtres qui transforme une donn�e exprim�e en Bytes en donn�e exprim�e en MB / Kb / ...
 * 
 * @package		copix
 * @subpackage	filter
 */
class CopixFilterBytesToText extends CopixAbstractFilter {
	/**
	 * Initialisation du filtre 
	 */
	public function __construct ($pParams = array ()){
		parent::__construct ($pParams);
	}

	/**
	 * R�cup�ration de la valeur exprim�e en MB / Kb / ...
	 * 
	 * @param mixed $pValue la valeur � tester 
	 * @return boolean
	 */
	public function get ($pValue){
        $b = (int) $pValue;
        $s = array ('B', 'KB', 'MB', 'GB', 'TB');
        if ($b < 0){
            return "0 ".$s[0];
        }
        $con = 1024;
        $e = (int) (log ($b, $con));
        return number_format ($b / pow ($con,$e), 2, ',', '.').' '.$s[$e]; 
	}
}