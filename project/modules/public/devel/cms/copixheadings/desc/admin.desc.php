<?php
/**
* @package		cms
* @subpackage	copixheadings
* @author		Croës Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
$list    = new CopixAction ('AdminHeading', 'getAdmin',
              array ('profile|profile'=>new CapabilityValueIn ('modules|copixheadings', 'copixheadings', PROFILE_CCV_SHOW)));
$create  = new CopixAction ('AdminHeading', 'doCreate',
              array ('profile|profile'=>new CapabilityValueIn ('modules|copixheadings', 'copixheadings', PROFILE_CCV_SHOW)));
$prepareEdit = new CopixAction ('AdminHeading', 'doPrepareEdit',
              array ('profile|profile'=>new CapabilityValueIn ('modules|copixheadings', 'copixheadings', PROFILE_CCV_SHOW)));
$edit = new CopixAction ('AdminHeading', 'getEdit',
              array ('profile|profile'=>new CapabilityValueIn ('modules|copixheadings', 'copixheadings', PROFILE_CCV_SHOW)));

$valid = new CopixAction ('AdminHeading', 'doValid',
              array ('profile|profile'=>new CapabilityValueIn ('modules|copixheadings', 'copixheadings', PROFILE_CCV_WRITE)));

$cancelEdit = new CopixAction ('AdminHeading', 'doCancel');

$cut   = new CopixAction ('AdminHeading', 'doCut');
$paste = new CopixAction ('AdminHeading', 'doPaste');

$delete = new CopixAction ('AdminHeading', 'doDelete');

$selectHeading = new CopixAction ('AdminHeading', 'getSelect',
              array ('profile|profile'=>new CapabilityValueIn ('modules|copixheadings', 'copixheadings', PROFILE_CCV_SHOW)));

$default = $list;
?>