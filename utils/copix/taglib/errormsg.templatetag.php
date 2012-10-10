<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
* @package		copix
* @subpackage	taglib
*/
class TemplateTagErrorMsg {
    public function process($pParams) {
        extract($pParams);

        if (empty ($message)){
            //if message isNull or empty, nothing to do.
            $output = '';
        } else {
            //process the output
            $output  = '<p';
            if (isset ($class)){
                $output .= ' class="'.$class.'"';
            }else{
                $output .= ' style="color: #FF2222;font-weight:bold;"';
            }
            $output.= '>'.$message.'</p>';
        }
        return $output;
    }
}

?>