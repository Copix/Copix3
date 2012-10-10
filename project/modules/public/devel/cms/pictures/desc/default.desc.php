<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
$get     = & new CopixAction ('Front', 'get');
$getFull = & new CopixAction ('Front', 'showFullScreen');
$download= & new CopixAction ('Front', 'download');
$default = & $get;
?>