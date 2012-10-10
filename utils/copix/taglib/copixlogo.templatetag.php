<?php
/**
* @package		copix
* @subpackage	taglib
* @author		Gérald Croës
* @copyright	2000-2006 CopixTeam
* @link			http://www.copix.org
* @license 		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package		copix
* @subpackage	taglib
*/
class TemplateTagCopixLogo extends CopixTemplateTag {
    
    public function process($pParams) {
    extract ($pParams);
    if (empty ($type) || $type == 'small') {
        return '<!-- made with Copix, http://copix.org -->';
    }else{
        return '<!-- made with
    ______    ___     ___  _   _  __      __
   /     /  /    \   /  __  \ / \ \ \    / / 
  / /      |  -   |  |    | | \_/  \ \  /  
 / /       | |  | |  | |_  _/  _    \  /   
 \ \____   |  _ | |  | |      | |    \ \/   
  \     \   \___ /   | |      | |    / /\  
                                |   /  \ \  
 _______________________________|__/_/  \_\___
|Open Source Framework for PHP                |
|_____________________________________________|
http://copix.org
-->';
    }
  }
}
?>