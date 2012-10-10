<?php
/**
* @package	copix
* @author	Chazot Virginie
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

$getTemplateList = & new CopixAction ('FrontTemplates', 'getTemplateList');
$themeList       = & new CopixAction ('FrontTemplates', 'getThemeList');
$setTheme        = & new CopixAction ('FrontTemplates', 'doSelectTheme');

$default            = & $getTemplateList;
?>