<?php
/**
 * @package		standard
 * @subpackage 	plugin_utf8
 * @author		Patrice Ferlet
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Plugin d'encodage de la sortie en UTF8
 * @package standard
 * @subpackage	plugin_utf8
 */
class PluginUTF8 extends CopixPlugin{
	/**
	 * Encodage en UTF8
	 */
	function beforeDisplay (& $display){
		$display = utf8_encode($display);
		$display = str_replace(array ("Ã¢", "Ã¤", "Ã¨","Ã©","Ãª","Ã«","Ã¯","Ã®","Ã¶","Ã´","Ã¹","Ã»","Ã¼","Ã¿","Ã","Ã","Ã","Ã","Ã", "Ã", "Ã", "Ã", "Ã", "Ã","Ã","Ã","Ã","Ã","Å¸","Ã§", "â¬", "Â£","Â¥","Â¢", utf8_encode("à")), 
							   array ("â",  "ä",  "è", "é", "ê", "ë", "ï", "î", "ö", "ô", "ù", "û", "û", "ÿ", "À",  "Â",  "Ä",  "É"  ,"È",    "Ë",   "Ê",   "Ï",   "Î",   "Ö",  "Û",  "Ù ",  "Ü ", "Ü",  "Ÿ", "ç",  "€",    "£", "¥", "¢",  "à"), 
							   $display);
	}
}
?>