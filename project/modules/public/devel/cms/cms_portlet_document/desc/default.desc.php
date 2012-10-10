<?php
/**
* @package cms
* @subpackage	cms_portlet_document
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */

$edit           = new CopixAction ('DocumentPortlet', 'getEdit');
$valid          = new CopixAction ('DocumentPortlet', 'doValid');
$validedit      = new CopixAction ('DocumentPortlet', 'doValidEdit');
$selectDocument = new CopixAction ('DocumentPortlet', 'getSelectDocument');
$deleteDocument = new CopixAction ('DocumentPortlet', 'doDeleteDocument');
$add            = new CopixAction ('DocumentPortlet', 'doAddDocument');
$moveUp         = new CopixAction ('DocumentPortlet', 'doMoveUp');
$moveDown       = new CopixAction ('DocumentPortlet', 'doMoveDown');

$default        = $edit;
?>