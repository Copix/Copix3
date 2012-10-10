<?php
/**
* @package	copix
* @author	Chazot Virginie
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

$create             = & new CopixAction ('Theme', 'doCreate');
$edit               = & new CopixAction ('Theme', 'getEdit');
$prepareEdit        = & new CopixAction ('Theme', 'doPrepareEdit');
$valid              = & new CopixAction ('Theme', 'doValid');
$cancelEdit         = & new CopixAction ('Theme', 'doCancelEdit');
$delete             = & new CopixAction ('Theme', 'doDelete');

$default            = & $getTemplateList;
?>