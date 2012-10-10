<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Salleyron Julien
 * @copyright	2000-2006 CopixTeam
 * @link			http://www.copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagCopixTips extends CopixTemplateTag {
    public function process ($pParams) {
        extract ($pParams);
        if ((!isset ($tips)) && (!isset ($warning))) {
            return '';
        }
        $toReturn = '';
        if (isset ($warning)) {
            foreach ($warning as $warn) {
                $toReturn .= '<li style="color: #FF2222;font-weight:bold;">'.$warn.'</li>';
            }
        }

        if (isset ($tips)) {
            foreach ($tips as $tip) {
                $toReturn .= '<li>'.$tip.'</li>';
            }
        }

        if (strlen ($toReturn)){
        	$toReturn = '<ul>'.$toReturn.'</ul>';        	
        	if (isset ($title) ){
            	$toReturn = '<h2><img src="' . _resource ('img/icons/help.gif') . '" /> ' . $title.'</h2>'.$toReturn;
        	}elseif (isset ($titlei18n)){
        		$toReturn = '<h2><img src="' . _resource ('img/icons/help.gif') . '" /> ' . _i18n($titlei18n).'</h2>'.$toReturn;
        	}
		}
        return $toReturn;
    }
}
?>