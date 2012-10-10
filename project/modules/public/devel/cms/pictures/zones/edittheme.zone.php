<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage pictures
* Zone d'affichage pour la proposition d'image.
*/
class ZoneEditTheme extends CopixZone {
   function _createContent (&$toReturn){
      $tpl = & new CopixTpl ();
      
      $tpl->assign ('showErrors',$this->_params['e']);
      //dao error or something else
      if ($this->_params['e']) {
         if (isset($this->_params['toEdit']->errors)) {
            $tpl->assign ('errors' ,$this->_params['toEdit']->errors);
         }else{
         	$dao = & CopixDAOFactory::getInstanceOf ('picturesthemes');
            $tpl->assign ('errors' ,$dao->check ($this->_params['toEdit']));
         }
      }
      
      $tpl->assign ('toEdit' ,$this->_params['toEdit']);

      $toReturn = $tpl->fetch ('themes.edit.tpl');
      return true;
   }
}
?>