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
class PluginDefaultConfigPrint {
	/**
    * Template we're gonna use to print with
    */
	public $templatePrint;

	/**
    * says the command needed to activate the print plugin.
    * format: _runPrintUrl['name']=Value
    * will activate the print plugin on index.php?name=value
    */
	public $runPrintUrl;

	public function __construct(){
		$this->templatePrint = 'default|main.print.tpl';
		$this->runPrintUrl = array ('toPrint'=>'1');
	}
}
?>