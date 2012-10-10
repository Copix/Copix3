<?
/**
* @package	cms
* @subpackage schedule
* @version	$Id: admineventlist.zone.php,v 1.1 2007/04/08 18:08:13 gcroes Exp $
* @author	Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam

* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage schedule
* ZoneAdminEventList
*/
class ZoneAdminEventList extends CopixZone {
    function _createContent (&$toReturn) {
        $tpl = & new CopixTpl ();

        $sp = & CopixDAOFactory::createSearchParams ();
        $sp->addCondition ('id_evtc', '=', $this->_params['id_evtc']);
        $dao = CopixDAOFactory::create ('ScheduleEvents');
        $results = $dao->findBy ($sp);
        $tpl->assign ('tabEvents', $results);

        // retour de la fonction :
        if (isset ($this->_params['template'])){
            $toReturn = $tpl->fetch ($this->_params['template']);
        } else {
            $toReturn = $tpl->fetch ('schedule|eventslist.admin.tpl');
        }
        return true;
    }
}
?>