<?php
/**
 * @package	cms
 * @subpackage menu_2
 * @version	$Id: admin.desc.php,v 1.1 2007/04/08 18:08:14 gcroes Exp $
 * @author	Sylvain DACLIN
 * @copyright 2001-2006 CopixTeam
 * @link		http://copix.org
 * @license  http://www.gnu.org/licenses/lgpl.htmlGNU Leser General Public Licence, see LICENCE file
 */
 
 /**
  * @ignore
  */
$list             = new CopixAction ('Admin', 'getAdmin');

$prepareEdit      = new CopixAction ('Admin', 'doPrepareEdit');
$create           = new CopixAction ('Admin', 'doCreate');
$edit             = new CopixAction ('Admin', 'getEdit');
$valid            = new CopixAction ('Admin', 'doValid');
$cancelEdit       = new CopixAction ('Admin', 'doCancelEdit');

$selectPage       = new CopixAction ('Admin', 'getSelectPage');
$validPage        = new CopixAction ('Admin', 'doValidPage');

$delete           = new CopixAction ('Admin', 'doDelete');

$toggleDisplay    = new CopixAction ('Admin', 'doToggleDisplay');
$up               = new CopixAction ('Admin', 'doUp');
$down             = new CopixAction ('Admin', 'doDown');

$cut              = new CopixAction ('Admin', 'doCut');
$paste            = new CopixAction ('Admin', 'doPaste');

$default = $list;
?>