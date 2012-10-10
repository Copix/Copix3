<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
$createTheme          = & new CopixAction ('Admin', 'doCreateTheme');
$prepareEditTheme     = & new CopixAction ('Admin', 'doPrepareEditTheme');
$editTheme            = & new CopixAction ('Admin', 'getEditTheme');
$deleteTheme          = & new CopixAction ('Admin', 'doDeleteTheme');
$prepareDelTheme      = & new CopixAction ('Admin', 'doPrepareDelTheme');
$cancelEditTheme      = & new CopixAction ('Admin', 'doCancelEditTheme');
$validTheme           = & new CopixAction ('Admin', 'doValidTheme');

$prepareEditProperties= & new CopixAction ('Admin', 'doPrepareEditProperties');
$editProperties       = & new CopixAction ('Admin', 'getEditProperties');
$validProperties      = & new CopixAction ('Admin', 'doValidProperties');

$validPicture         = & new CopixAction ('Admin', 'doValidPicture');
$prepareEditPicture   = & new CopixAction ('Admin', 'doPrepareEditPicture');
$createPicture        = & new CopixAction ('Admin', 'doCreatePicture');
$editPicture          = & new CopixAction ('Admin', 'getEditPicture');
$movePicture          = & new CopixAction ('Admin', 'doMovePicture');
$confirmDeletePicture = & new CopixAction ('Admin', 'getConfirmDeletePicture');
$deletePicture        = & new CopixAction ('Admin', 'doDeletePicture');
$cancelEditPicture    = & new CopixAction ('Admin', 'doCancelEditPicture');

$statusPublishPicture = & new CopixAction ('Admin', 'doStatusPublishPicture');
$statusValidPicture   = & new CopixAction ('Admin', 'doStatusValidPicture');
$statusProposePicture = & new CopixAction ('Admin', 'doStatusProposePicture');
$statusRefusePicture  = & new CopixAction ('Admin', 'doStatusRefusePicture');
$statusTrashPicture   = & new CopixAction ('Admin', 'doStatusTrashPicture');
$statusDraftPicture   = & new CopixAction ('Admin', 'doStatusDraftPicture');

//$contrib              = & new CopixActionZone ('PicturesAdminHeading', array ('TITLE_PAGE'=>CopixI18N::get ('pictures.titlePage.manage'), 'Params'=>array('id_head'=>$_GET['id_head'])));

//Cut and paste tools.
$cut           = & new CopixAction ('Admin', 'doCut');
$paste         = & new CopixAction ('Admin', 'doPaste');
$import        = & new CopixAction ('Admin', 'doImport');

$default              = & $contrib;
?>
