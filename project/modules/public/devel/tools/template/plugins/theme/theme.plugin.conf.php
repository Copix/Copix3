<?php
/**
* @package	copix
* @subpackage auth
* @author	Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class PluginConfigTheme {
	/**
	* Can the user change its theme
	*/
	var $enableUserTheme;

    /**
    * How long does the user's theme last
    */
	var $themeLifeTime = 'session';//session/cookie
}
?>