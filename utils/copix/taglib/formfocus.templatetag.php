<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagFormFocus extends CopixTemplateTag {
    public function process ($pContent = null) {
    	_tag ('mootools');
    	$id = $this->requireParam ('id');
    	CopixHTMLHeader::addJSDOMReadyCode ('$(\''.$id.'\').focus ()');
    }
}