<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Gérald Croës
 * @copyright	2000-2006 CopixTeam
 * @link			http://www.copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */
/**
* @package		copix
* @subpackage	taglib
*/
class TemplateTagEscape extends CopixTemplateTag {
	public function process ($pContent=null){
		$pParams = $this->getParams ();
		if (is_array ($pParams)){
			return _copix_utf8_htmlentities (isset ($pParams['value']) ? $pParams['value'] : '');
		}
		return _copix_utf8_htmlentities ($pParams);
	}
}