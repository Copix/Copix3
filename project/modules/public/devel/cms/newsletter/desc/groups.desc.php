<?php
/**
* @package	cms
* @subpackage newsletter
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore 
 */
 $create        = & new CopixAction ('Groups', 'doCreate'          , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_MODERATE)));
$edit          = & new CopixAction ('Groups', 'getEdit'           , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_MODERATE)));
$valid         = & new CopixAction ('Groups', 'doValid'           , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_MODERATE)));
$prepareEdit   = & new CopixAction ('Groups', 'doPrepareEdit'     , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_MODERATE)));
$cancelEdit    = & new CopixAction ('Groups', 'doCancelEdit'      , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_MODERATE)));
$delete        = & new CopixAction ('Groups', 'doDelete'          , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_MODERATE)));
$confirmDelete = & new CopixAction ('Groups', 'getConfirmDelete'  , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_MODERATE)));

?>
