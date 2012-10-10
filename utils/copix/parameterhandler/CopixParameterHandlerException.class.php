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
 * Exception levée dans CopixParameterHandler
 * @package copix
 * @subpackage utils
 */
class CopixParameterHandlerException extends CopixException {
	public function __construct ($pMessage, $pType = null){
		parent::__construct ($pMessage, $pType);		
	}
}