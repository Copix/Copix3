<?php
/**
* @package		simplehelp
* @author		Audrey Vassal
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @licence		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * 
 */
class ZoneSimpleHelpEdit extends CopixZone {
   function _createContent (&$toReturn) {
      $tpl = & new CopixTpl ();
      
      $dao = _ioDao ('simplehelp');
      
      $tpl->assign ('showErrors', $this->getParam('e', null, null));
      $tpl->assign ('toEdit'    , $this->getParam ('toEdit', null, null));
      $tpl->assign ('errors'    , $dao->check($this->getParam ('toEdit', null, null)));
      
      $toReturn = $tpl->fetch ('simplehelp.edit.tpl');
      return true;
   }
}
?>