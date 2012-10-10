<?php
/**
* @package	cms
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
$create      = new CopixAction ('CMSAdmin', 'doCreate');
$chooseKind = $create;

$edit        = new CopixAction ('CMSAdmin', 'getEdit');
$prepareEdit = new CopixAction ('CMSAdmin', 'doPrepareEdit');

$afterValid      = new CopixAction ('CMSAdmin', 'doAfterValid');
$cancel          = new CopixAction ('CMSAdmin', 'doCancel');

$emptyTrash = new CopixAction ('CMSAdmin', 'doEmptyTrash');

$newFromPage = new CopixAction ('CMSAdmin', 'doNewFromPage');
$showHistory = new CopixAction ('CMSAdmin', 'getShowHistory');
$showVersion = new CopixAction ('CMSAdmin', 'getShowVersion');

//Cut and paste tools.
$cut        = new CopixAction ('CMSAdmin', 'doCut');
$paste      = new CopixAction ('CMSAdmin', 'doPaste');

//view a draft
$getDraft       = new CopixAction ('CMSAdmin', 'getDraft');
$templateChoice = new CopixAction ('CMSAdmin', 'getTemplateChoice');

$validedit      = new CopixAction ('CMSAdmin', 'doValidEdit');
$valid          = new CopixAction ('CMSAdmin', 'doValid');
$validPortlet   = new CopixAction ('CMSAdmin', 'doValidPortlet');

//Manipulation des portlets
$newPortlet         = new CopixAction ('CMSAdmin', 'doNewPortlet');
$preparePortletEdit = new CopixAction ('CMSAdmin', 'doPreparePortletEdit');
$deletePortlet      = new CopixAction ('CMSAdmin', 'doDeletePortlet');
$movePortletUp      = new CopixAction ('CMSAdmin', 'doMovePortletUp');
$movePortletDown    = new CopixAction ('CMSAdmin', 'doMovePortletDown');
$cancelPortlet      = new CopixAction ('CMSAdmin', 'doCancelPortlet');
$copyPortlet     = new CopixAction ('CMSAdmin', 'doCopyPortlet');
$cutPortlet      = new CopixAction ('CMSAdmin', 'doCutPortlet');
$pastePortlet    = new CopixAction ('CMSAdmin', 'doPastePortlet');
$portletChoice  = new CopixAction ('CMSAdmin', 'getPortletChoice');

//default action is cancellation
$default     = $cancel;
?>