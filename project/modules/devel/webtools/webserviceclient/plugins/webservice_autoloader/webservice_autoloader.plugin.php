<?php
/**
* @package   standard
* @subpackage plugin_theme_ajax
* @author   Salleyron Julien
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Plugin qui permet de garder un theme changé dans tous les appels ajax
* @package   standard
* @subpackage plugin_theme_ajax
*/
class PluginWebService_Autoloader extends CopixPlugin {
	public function beforeProcess (&$pAction) {
        $ws_class_generator = _ioClass ('webserviceclient|wsclassgenerator');
        spl_autoload_register(array ($ws_class_generator,'autoload'));
	}
}
?>