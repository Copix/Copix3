<?php
/**
 * @package    standard
 * @subpackage test
 * @author	   Gérald Croës
 * @copyright  CopixTeam
 * @link       http://copix.org
 * @licence    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Cette classe est la simplement pour vérifier que la factory lance bien une exception sur ce filtre 
 *  car il n'implémente pas ICopixFilter
 * 
 * @package standard
 * @subpackage test
 */
class FilterNoInterface {
	public function __construct ($pParams = array ()){
	}

	public function get ($pValue){
		return 'Foo';
	}
}