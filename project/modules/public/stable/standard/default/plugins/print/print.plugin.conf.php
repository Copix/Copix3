<?php
/**
* @package   standard
* @subpackage plugin_print
* @author   Croës Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe de configuration pour le plugin Print
 * @package standard
 * @subpackage	plugin_print
 */
class PluginConfigPrint {
	/**
    * Template we're gonna use to print with
    */
	var $_templatePrint;

	/**
    * says the command needed to activate the print plugin.
    * format: _runPrintUrl['name']=Value
    * will activate the print plugin on index.php?name=value
    */
	var $_runPrintUrl;

	function PluginConfigPrint (){
		$this->_templatePrint = 'main.print.tpl';
		$this->_runPrintUrl = array ('toPrint'=>'1');
	}
}
?>