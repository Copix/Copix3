<?php
/**
 * @package     copix
 * @subpackage  taglib
 * @author      Gérald Croës
 * @copyright   CopixTeam
 * @link        http://www.copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagCopixLogo extends CopixTemplateTag {
	public function process ($pContent = null) {
		if ($this->getParam ('type', 'small') == 'small') {
			return '<!-- made with Copix, http://copix.org -->';
		}else{
			return '<!-- made with
    ______    ___     ___  _   _  __      __
   /     /  /    \   /  __  \ / \ \ \    / / 
  / /      |  -   |  |    | | \_/  \ \  /  
 / /       | |  | |  | |_  _/  _    \  /   
 \ \____   |  _ | |  | |      | |    \ \/   
  \     \   \___ /   | |      | |    / /\  
 _                              |   /  \ \  
 |______________________________|__/_/  \_\___
 PHP 5 Framework                              
 http://www.copix.org
-->';
		}
	}
}