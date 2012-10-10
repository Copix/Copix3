<?php
/**
* @package	cms
* @subpackage document
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
$prepareEdit   = & new CopixAction ('DocumentAdmin', 'doPrepareEdit');
$create        = & new CopixAction ('DocumentAdmin', 'doCreate');
$edit          = & new CopixAction ('DocumentAdmin', 'getEdit');
$valid         = & new CopixAction ('DocumentAdmin', 'doValid');
$cancelEdit    = & new CopixAction ('DocumentAdmin', 'doCancelEdit');

$statusPublish = & new CopixAction ('DocumentAdmin', 'doStatusPublish');
$statusValid   = & new CopixAction ('DocumentAdmin', 'doStatusValid');
$statusPropose = & new CopixAction ('DocumentAdmin', 'doStatusPropose');
$statusRefuse  = & new CopixAction ('DocumentAdmin', 'doStatusRefuse');
$statusTrash   = & new CopixAction ('DocumentAdmin', 'doStatusTrash');
$statusDraft   = & new CopixAction ('DocumentAdmin', 'doStatusDraft');
$delete        = & new CopixAction ('DocumentAdmin', 'doDelete');

//Cut and paste tools.
$cut           = & new CopixAction ('DocumentAdmin', 'doCut');
$paste         = & new CopixAction ('DocumentAdmin', 'doPaste');

$viewVersion   = & new CopixAction ('DocumentAdmin',  'getViewVersion');
$onlineDocument= & new CopixAction ('DocumentAdmin', 'getOnlineDocument');

$selectDocument= & new CopixAction ('DocumentAdmin', 'getSelectDocument');

$default       = & $contrib;
?>
