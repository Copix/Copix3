<?php
/**
* @package	cms
* @subpackage cms_portlet_survey
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
$edit           = & new CopixAction ('SurveyPortlet', 'getEdit');
$valid          = & new CopixAction ('SurveyPortlet', 'doValid');
$selectHeading  = & new CopixAction ('SurveyPortlet', 'doSelectHeading');
$fromPageUpdate = & new CopixAction ('SurveyPortlet', 'doSelectPage');

$default        = & $edit;
?>
