<?php
/**
* @package	cms
* @subpackage newsletter
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license   http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * @ignore
 */
$get               = & new CopixAction ('Newsletter', 'getNewsletter');

/**
* Implements the subscribe / unsubscribe fonctions for the newsletter
*/
$validSubscription = & new CopixAction ('Newsletter', 'doValidSubscription');
$subscribe         = & new CopixAction ('Newsletter', 'doSubscribe');
$unsubscribe       = & new CopixAction ('Newsletter', 'getUnsubscribe');
$validUnsubscribe  = & new CopixAction ('Newsletter', 'doUnsubscribe');

$default           = & $get;
?>