<?php
/**
 * @package		standard
 * @subpackage 	plugin_utf8
 * @author		Guillaume Perréal
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
	public function beforeDisplay (& $display){		
		
		// Si on a mb_string, on l'utilise pour court-circuiter le traitement si le contenu est déjà en UTF-8
		if(function_exists('mb_check_encoding') && mb_check_encoding($display, 'UTF-8')) {
			return;
		}
		 
		// En un seul appel:
		// 1) encode l'UTF-8 valide (et les '~') dans un format maison en ASCII,
		// 2) encode en UTf-8 tout ce qui reste,
		// 3) décode l'UTF-8 encodé en 1) 
		$display = preg_replace_callback(
			'/~([0-9a-f]+)~/i',
			array($this, '_decode'),			
			utf8_encode( 		
				preg_replace_callback( 
					'/(?:~|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3})+/',
					array($this, '_encode'),
					$display
				)
			)
		);
	}
	
	/**
	 * Callback pour encoder l'UTF-8.
	 * 
	 * Le chaîne est convertie en héxadécimal puis placée entre tildes (~).
	 * Exemple : "é" (UTF-8) => "~c3a9~" (ASCII).
	 *
	 * @param array $match Résultat de la regex. L'index 0 contient la chaîne à encoder.
	 * @return string La chaîne encodée.
	 */
	private function _encode($match) {
		return '~'.bin2hex($match[0]).'~';
	}
	
	/**
	 * Callback pour redonner à l'UTF-8 sa forme initiale.
	 *
	 * @param array $match Résultat de la regex. L'index 1 contient la chaîne à décoder.
	 * @return string Le chaîne originale.
	 */
	private function _decode($match) {
		return pack('H*', $match[1]);
	}
	
	
}
?>