<?php
/**
* @package	 cms
* @subpackage cms_portlet_newsdetail
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
$edit       		= & new CopixAction ('NewsDetailPortlet', 'getEdit');
$valid      		= & new CopixAction ('NewsDetailPortlet', 'doValid');
$validEdit			= & new CopixAction ('NewsDetailPortlet', 'doValidEdit');

$selectPicture		= & new CopixAction ('NewsDetailPortlet', 'getSelectPicture');
$deletePicture		= & new CopixAction ('NewsDetailPortlet', 'doDeletePicture');

$fromPageUpdate	= & new CopixAction ('NewsDetailPortlet', 'doSelectPage');
?>