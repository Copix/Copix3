<?php
/**
* @package	cms
* @subpackage cms_portlet_schedule
* @author	Bertrand Yan, Ferlet Patrice see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
$edit             = & new CopixAction ('SchedulePortlet', 'getEdit');
$validedit        = & new CopixAction ('SchedulePortlet', 'doValidEdit');
$valid            = & new CopixAction ('SchedulePortlet', 'doValid');

$selectPage       = new CopixAction ('SchedulePortlet', 'doSelectPage');
$fromPageUpdate	= & new CopixAction ('SchedulePortlet', 'doSelectPage');
$selectHeading    = & new CopixAction ('SchedulePortlet', 'doSelectHeading');
?>
