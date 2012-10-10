<?php
/**
* @package	cms
* @subpackage newsletter
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * @ignore
 */
$prepareSendToGroup = & new CopixAction ('NewsletterAdmin', 'getPrepareSendToGroup', array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_PUBLISH)));
$prepareSendTest    = & new CopixAction ('NewsletterAdmin', 'getPrepareSendTest'   , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_PUBLISH)));
$sendTest           = & new CopixAction ('NewsletterAdmin', 'doSendTest'           , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_PUBLISH)));
$sendToGroup        = & new CopixAction ('NewsletterAdmin', 'doSendToGroup'        , array('profile|profile'=>new CapabilityValueOf('modules|newsletter','newsletter',PROFILE_CCV_PUBLISH)));
?>
