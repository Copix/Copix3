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
* Zone d'affichage de l'administration des categories.
*/
class ZoneEditProperties extends CopixZone {
    function _createContent (&$toReturn){
        $tpl = & new CopixTpl ();

        $tpl->assign('formatList'   ,explode (';', CopixConfig::get ('pictures|format')));
        $tpl->assign('showErrors'   ,$this->_params['e']);
        $dao = & CopixDAOFactory::getInstanceOf ('picturesheadings');        
        if (($errors = $dao->check ($this->_params['toEdit'])) != true) {
            $tpl->assign('errors', $errors);
        }else{
        	$tpl->assign ('errors', array ());
        }
        $tpl->assign('toEdit'       , $this->_params['toEdit']);
        $tpl->assign('invalidParams', isset ($this->_params['toEdit']->invalidParams) ? $this->_params['toEdit']->invalidParams : null);
        $tpl->assign('editCatFormat', explode (';',$this->_params['toEdit']->format_cpic));
        $tpl->assign('heading'      , $this->_params['heading']);

        $toReturn = $tpl->fetch ('properties.edit.tpl');
        return true;
    }
}
?>