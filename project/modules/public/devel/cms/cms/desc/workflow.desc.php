<?php
/**
* @package	cms
* @author	Croes Gérald, see copix.org for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
 
 
$propose = new CopixAction ('CMSPageWorkflow', 'doPropose');
$valid   = new CopixAction ('CMSPageWorkflow', 'doValid');
$publish = new CopixAction ('CMSPageWorkflow', 'doPublish');
$trash   = new CopixAction ('CMSPageWorkflow', 'doTrash');
$restore = new CopixAction ('CMSPageWorkflow', 'doRestore');
$delete  = new CopixAction ('CMSPageWorkflow', 'doDelete');
$refuse  = new CopixAction ('CMSPageWorkflow', 'doRefuse');

$deleteOnline = new CopixAction ('CMSPageWorkflow', 'doDeleteOnline');
?>
