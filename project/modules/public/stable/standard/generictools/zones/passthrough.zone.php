<?php
/**
* @package		standard 
 * @subpackage	generictools
* @author	Croes Gérald
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Zone vide qui se contente de passer ses paramètres à un template.
* @package		standard 
 * @subpackage	generictools
 */
class ZonePassThrough extends CopixZone {
   /**
   * Zone that passes all its parameters to the given template
   * @param: * abstract
   * @param: template string the template name where the datas will be assigned to.
   */
   function _createContent (&$toReturn){
      //we wants to go back to the current context.
      $context = CopixContext::pop ();
      
      $tpl = new CopixTpl ();
      //assign the template variables
      foreach ($this->_params as $var=>$value){
         if ($var !== 'template'){
            $tpl->assign ($var, $value);
         }
      }
      $toReturn = $tpl->fetch ($this->_params['template']);

      //then we wants to bring back the context we poped
      CopixContext::push ($context);
      return true;
   }
}
?>