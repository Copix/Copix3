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
class TemplateTagJSSubmitForm {
    public function process($params) {
        static $_init = false;

        if (!isset ($params['href'])){
            throw new CopixTemplateTagException("[smarty jssubmitform] Missing href parameter");
            return;
        }

        if (!isset ($params['form'])){
            throw new CopixTemplateTagException("[smarty jssubmitform] Missing form parameter");
            return;
        }

        if (! $_init){
            $jsCode = 'function doSubmitForm (pUrl, formId) {
                     var myForm = document.getElementById(formId);
                     myForm.action = pUrl;
                     myForm.submit ();
                     return false;
                  }';
            CopixHtmlHeader::addJsCode ($jsCode);
            $_init = true;
        }

        $toReturn = 'return doSubmitForm(\''.$params['href'].'\', \''.$params['form'].'\')';
        return $toReturn;
    }
}

?>