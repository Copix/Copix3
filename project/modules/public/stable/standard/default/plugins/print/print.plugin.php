<?php
/**
* @package   standard
* @subpackage	plugin_print
* @author   Croës Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Plugin permettant de changer le template principal à utiliser à partir d'une information dans l'url
 * @package standard
 * @subpackage plugin_print
 */
class PluginPrint extends CopixPlugin {
	/**
	* On change le template principal pour lui affecter le template d'impression défini dans la configuration
	* du plugin.
	*/
	function beforeSessionStart(){
		if ($this->shouldPrint ()){
			CopixConfig::instance ()->mainTemplate = $this->config->templatePrint;
		}
	}
	
	/**
    * Méthode utilisée en interne indiquant si l'on devrait ou non imprimer
    * @return bool
    */
	function shouldPrint (){
		foreach ($this->config->runPrintUrl as $name=>$value){
			if (_request ($name) != $value){
				return false;
			}
		}
		return true;
	}
	
	/**
    * Gets the url of the current page, with the "ask for print" informations.
    */
	function getPrintableUrl (){
		return _url ('#', $this->config->runPrintUrl);
	}
}
?>