<?php
/**
* @package	cms
* @subpackage schedule
* @version	$Id: scheduleservice.class.php,v 1.1 2007/04/08 18:08:14 gcroes Exp $
* @author	Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam

* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage schedule
* ScheduleService
*/
class ScheduleService {
   function getLevel($id_head){
		$DAOnews = & CopixDAOFactory::create ('schedule|ScheduleEvents');
		return $DAOnews->getCountHeading($id_head);
	}
}
?>