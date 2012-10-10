<?php
/**
* @package	cms
* @subpackage cms_portlet_news
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */

$edit             = new CopixAction ('NewsPortlet', 'getEdit');
$validedit        = new CopixAction ('NewsPortlet', 'doValidEdit');
$valid            = new CopixAction ('NewsPortlet', 'doValid');

$fromPageUpdate	  = new CopixAction ('NewsPortlet', 'doSelectPage');
$selectHeading    = new CopixAction ('NewsPortlet', 'doSelectHeading');
?>