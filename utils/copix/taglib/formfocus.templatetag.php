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
class TemplateTagFormFocus extends CopixTemplateTag {
    public function process ($pParams) {
        extract ($pParams);
        if (empty ($id)){
            throw new CopixTemplateTagException('[formfocus] missing id parameter');
        }
        return '<script type="text/javascript" defer="1">
		 //<![CDATA[
		 var formElement'.$id.' = document.getElementById (\''.$id.'\');
		 if (formElement'.$id.'){
		   formElement'.$id.'.focus ();
		 }
		 //]]>
		 </script>';
    }
}
?>