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
 * Ecran de confirmation
 * @package		standard
 * @subpackage	generictools
 */
class ZoneConfirmDialog extends CopixZone {
    function _createContent (& $toReturn){
        $tpl = new CopixTpl ();
        $tpl->assign ('title', isset ($this->_params['title']) ? $this->_params['title'] : CopixI18N::get ('messages.titlePage.confirm'));

        if (isset ($this->_params['message'])){
            $tpl->assign ('message', $this->_params['message']);
        }

        $tpl->assign ('confirm', $this->_params['confirm']);
        $tpl->assign ('cancel', $this->_params['cancel']);
        $toReturn = $tpl->fetch ('generictools|messages.confirm.tpl');

        return true;
    }
}
?>