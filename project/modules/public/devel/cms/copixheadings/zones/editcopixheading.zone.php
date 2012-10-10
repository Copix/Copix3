<?php
/**
* @package	 cms
* @subpackage copixheadings
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	 cms
* @subpackage copixheadings
* ZoneEditCopixHeading
 */
class ZoneEditCopixHeading extends CopixZone {
    /**
   * edit zone for a CopixHeading
   * @param object toEdit the CopixHeadingRecord
   * @param boolean displayErrors if we want to display the errors
   */
    function _createContent (& $toReturn){
        $tpl = & new CopixTpl ();
        $dao = & CopixDAOFactory::getInstanceOf ('CopixHeadings');

        $tpl->assignByRef ('toEdit', $this->_params['toEdit']);
        if ($this->_params['displayErrors']){
        	$dao = CopixDAOFactory::getInstanceOf ('CopixHeadings');
            $tpl->assign ('errors', $dao->check ($this->_params['toEdit']));
        }else{
            $tpl->assign ('errors', array ());
        }

        $tpl->assign ('isNew', $this->_params['toEdit']->isNew ());

        $tpl->assignByRef ('arHeadings', $dao->findAll ());
        $toReturn = $tpl->fetch ('copixheadings.edit.tpl');
    }
}
?>