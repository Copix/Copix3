<?php
/**
* @package	cms
* @subpackage survey
* @author	Bertrand Yan
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
$prepareEdit   = & new CopixAction ('SurveyAdmin', 'doPrepareEdit');
$create        = & new CopixAction ('SurveyAdmin', 'doCreate');
$edit          = & new CopixAction ('SurveyAdmin', 'getEdit');
$cancelEdit    = & new CopixAction ('SurveyAdmin', 'doCancelEdit');
$valid         = & new CopixAction ('SurveyAdmin', 'doValid');
$addOption     = & new CopixAction ('SurveyAdmin', 'doAddOption');
$deleteOption  = & new CopixAction ('SurveyAdmin', 'doDeleteOption');
$delete        = & new CopixAction ('SurveyAdmin', 'doDelete');
$viewResult    = & new CopixAction ('SurveyAdmin', 'viewResult');
?>