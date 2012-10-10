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
 * Exception levée lorsqu'un paramètre ne respecte pas le format voulu
 * @package copix
 * @subpackage utils 
 */
class CopixParameterHandlerValidationException extends CopixParameterHandlerException  {
	public function __construct ($pMessage, $pType = null){
		if ($pType instanceof ICopixValidator){
			$type = get_class ($pType);
		}else{
			$type = $pType;
		}
		parent::__construct ($pMessage.'['.$type.']');
	}
}