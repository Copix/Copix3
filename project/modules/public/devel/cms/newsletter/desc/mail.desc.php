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
$prepareEdit   = & new CopixAction ('MailAdmin', 'doPrepareEdit' , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_MODERATE)));
$create        = & new CopixAction ('MailAdmin', 'doCreate'      , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_MODERATE)));
$edit          = & new CopixAction ('MailAdmin', 'getEdit'       , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_MODERATE)));
$valid         = & new CopixAction ('MailAdmin', 'doValid'       , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_MODERATE)));
$delete        = & new CopixAction ('MailAdmin', 'doDelete'      , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_MODERATE)));
$cancelEdit    = & new CopixAction ('MailAdmin', 'doCancelEdit'  , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_MODERATE)));
?>