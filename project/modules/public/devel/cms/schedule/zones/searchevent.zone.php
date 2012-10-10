<?php
/**
* @package	cms
* @subpackage schedule
* @version	$Id: searchevent.zone.php,v 1.1 2007/04/08 18:08:13 gcroes Exp $
* @author	Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam

* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package	cms
* @subpackage schedule
* ZoneSearchEvent 
*/
class ZoneSearchEvent extends CopixZone {
    function _createContent (&$toReturn) {
        $tpl = & new CopixTpl ();

        //on assigne des résultats probable de recherche
        if(isset($this->_params['evts']) and count($this->_params['evts'])>0){
            $tpl->assign('evts',$this->_params['evts']);
        }

        //on ne perd pas les paramètres de recherche
        if(isset($this->_params['searchParams'])){
            $tpl->assign('searchparams',$this->_params['searchParams']);
        }

        $toReturn = $tpl->fetch('schedule.search.admin.tpl');
        return true;
    }
}
?>