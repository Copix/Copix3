<?php
/**
* @package	cms
* @subpackage news
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * @ignore
 */
$selectPicture	  = & new CopixAction ('NewsAdmin', 'getSelectPicture');
$editPicture      = & new CopixAction ('NewsAdmin', 'getEditPicture');
$deletePictureNews= & new CopixAction ('NewsAdmin', 'doDeletePictureNews');

$prepareEdit      = & new CopixAction ('NewsAdmin', 'doPrepareEdit');
$create           = & new CopixAction ('NewsAdmin', 'doCreate');
$edit             = & new CopixAction ('NewsAdmin', 'getEdit');

$valid            = & new CopixAction ('NewsAdmin', 'doValid');
$cancelEdit       = & new CopixAction ('NewsAdmin', 'doCancelEdit');

$delete           = & new CopixAction ('NewsAdmin', 'doDelete');

$statusPublish    = & new CopixAction ('NewsAdmin', 'doStatusPublish');
$statusValid      = & new CopixAction ('NewsAdmin', 'doStatusValid');
$statusPropose    = & new CopixAction ('NewsAdmin', 'doStatusPropose');
$statusRefuse     = & new CopixAction ('NewsAdmin', 'doStatusRefuse');
$statusTrash      = & new CopixAction ('NewsAdmin', 'doStatusTrash');
$statusDraft      = & new CopixAction ('NewsAdmin', 'doStatusDraft');

//Cut and paste tools.
$cut           = & new CopixAction ('NewsAdmin', 'doCut');
$paste         = & new CopixAction ('NewsAdmin', 'doPaste');

$default       = & $create;
?>