<?php
/**
 * @package    copix
 * @subpackage smarty_plugins
 * @author     Guillaume Perréal
 * @copyright  2001-2008 CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Plugin smarty type modifier
 * Purpose: encode a value using JSON.
 * @return string
 */
function smarty_modifier_json($value) {
	return CopixJSON::encode($value);
}