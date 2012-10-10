<?php
/**
* @package	cms
* @subpackage schedule
* @version	$Id: admin.desc.php,v 1.1 2007/04/08 18:08:14 gcroes Exp $
* @author	Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam

* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
$create           = & new CopixAction ('AdminSchedule', 'doCreateEvnt');
$prepareEdit      = & new CopixAction ('AdminSchedule', 'doPrepareEditEvnt');
$editEvnt         = & new CopixAction ('AdminSchedule', 'getEditEvnt');
$validEvnt        = & new CopixAction ('AdminSchedule', 'doValidEvnt');
$cancelEditEvnt   = & new CopixAction ('AdminSchedule', 'doCancelEditEvnt');

$delete           = & new CopixAction ('AdminSchedule', 'doDeleteEvnt');

$statusPublish    = & new CopixAction ('AdminSchedule', 'doStatusPublish');
$statusValid      = & new CopixAction ('AdminSchedule', 'doStatusValid');
$statusPropose    = & new CopixAction ('AdminSchedule', 'doStatusPropose');
$statusRefuse     = & new CopixAction ('AdminSchedule', 'doStatusRefuse');
$statusTrash      = & new CopixAction ('AdminSchedule', 'doStatusTrash');
$statusDraft      = & new CopixAction ('AdminSchedule', 'doStatusDraft');

//Cut and paste tools.
$cut           = & new CopixAction ('AdminSchedule', 'doCut');
$paste         = & new CopixAction ('AdminSchedule', 'doPaste');
?>
