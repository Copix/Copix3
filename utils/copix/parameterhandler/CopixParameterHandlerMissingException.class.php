<?php
/**
 * @package    copix
 * @subpackage utils
 * @author     Croës Gérald
 * @copyright  CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Exception levée lorsqu'un paramètre requis est manquant
 * @package copix
 * @subpackage utils
 */
class CopixParameterHandlerMissingException extends CopixParameterHandlerException {
	public function __construct ($pElement, $pType = null){
		if (is_array ($pElement)){
			$pElement = implode ('-', $pElement);
		}
		parent::__construct ($pElement, $pType);
	}
}